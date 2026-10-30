<?php
require_once __DIR__ . '/../models/users.php';
require_once __DIR__ . '/../config/EmployeeAuth.php';

class userController
{
    public function apiStatus(): void
    {
        echo json_encode(['estado' => true, 'message' => 'API de CompraCompu disponible.'], JSON_UNESCAPED_UNICODE);
    }

    public function login(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            ApiResponse::error('El cuerpo de la solicitud debe ser JSON válido.', 400);
            return;
        }
        echo json_encode(EmployeeAuth::login($data), JSON_UNESCAPED_UNICODE);
    }

    public function getAll(): void
    {
        echo json_encode(usuarios::all(), JSON_UNESCAPED_UNICODE);
    }

    public function add(): void
    {
        $data = ApiResponse::input(['cod_empleado', 'email', 'password_hash']);
        if ($data === null) return;
        $this->saveAccount($data);
        if (http_response_code() >= 400) return;
        $id = usuarios::add([
            'cod_empleado' => $data['cod_empleado'],
            'email' => trim($data['email']),
            'password_hash' => password_hash($data['password_hash'], PASSWORD_DEFAULT),
            'estado_cuenta' => $data['estado_cuenta'] ?? 'Activo',
        ]);
        echo json_encode(['estado' => (bool)$id, 'message' => $id ? 'usuario agregado correctamente' : 'no se pudo agregar el usuario', 'id' => $id], JSON_UNESCAPED_UNICODE);
    }

    public function update($id): void
    {
        $data = ApiResponse::input(['cod_empleado', 'email']);
        if ($data === null) return;
        $this->saveAccount($data, $id);
        if (http_response_code() >= 400) return;
        $values = [
            'cod_empleado' => $data['cod_empleado'],
            'email' => trim($data['email']),
            'estado_cuenta' => $data['estado_cuenta'] ?? 'Activo',
        ];
        if (!empty($data['password_hash'])) {
            if (strlen($data['password_hash']) < 8) {
                ApiResponse::error('La contraseña debe tener al menos 8 caracteres.', 400);
                return;
            }
            $values['password_hash'] = password_hash($data['password_hash'], PASSWORD_DEFAULT);
        }
        $updated = usuarios::update($id, $values);
        echo json_encode(['estado' => (bool)$updated, 'message' => $updated ? 'usuario actualizado correctamente' : 'no se pudo actualizar el usuario'], JSON_UNESCAPED_UNICODE);
    }

    private function saveAccount(array $data, $exceptId = null): void
    {
        if (!filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL)) {
            ApiResponse::error('Ingresa un correo electrónico válido.', 400);
            return;
        }
        if (!ctype_digit((string)$data['cod_empleado'])) {
            ApiResponse::error('Selecciona un empleado válido.', 400);
            return;
        }
        $conflict = usuarios::findConflict($data['cod_empleado'], trim($data['email']), $exceptId);
        if ($conflict) {
            $sameEmployee = (string)$conflict['cod_empleado'] === (string)$data['cod_empleado'];
            http_response_code(409);
            ApiResponse::error($sameEmployee ? 'El empleado ya tiene una cuenta.' : 'El correo electrónico ya está registrado.');
        }
    }

    public function delete($id): void
    {
        $deleted = usuarios::delete($id);
        echo json_encode(['estado' => (bool)$deleted, 'message' => $deleted ? 'usuario eliminado correctamente' : 'no se pudo eliminar el usuario'], JSON_UNESCAPED_UNICODE);
    }
}
