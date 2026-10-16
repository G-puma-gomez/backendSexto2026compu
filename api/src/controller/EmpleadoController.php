<?php
require_once "../src/models/empleados.php";

class EmpleadoController
{
    public function login()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            ApiResponse::error('El cuerpo de la solicitud debe ser JSON válido.', 400);
            return;
        }
        echo json_encode(EmployeeAuth::login($data), JSON_UNESCAPED_UNICODE);
    }

    public function getAll()
    {
        echo json_encode(empleados::all());
    }

    public function add()
    {
        $data = ApiResponse::input(['ci', 'nombre', 'apellidos', 'permiso', 'salario', 'email', 'password']);
        if ($data === null) return;
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) || !in_array($data['permiso'], ['Vendedor', 'Supervisor'], true)) {
            ApiResponse::error('Ingresa un correo válido y elige Vendedor o Supervisor.', 400);
            return;
        }
        if (strlen($data['password']) < 8) {
            ApiResponse::error('La contraseña debe tener al menos 8 caracteres.', 400);
            return;
        }
        $id = empleados::add($data);
        echo json_encode(['estado' => (bool) $id, 'message' => $id ? 'empleado agregado correctamente' : 'no se pudo agregar el empleado', 'id' => $id ? (int) $id : null]);
    }

    public function update($id)
    {
        $data = ApiResponse::input(['ci', 'nombre', 'apellidos', 'permiso', 'salario', 'email']);
        if ($data === null) return;
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) || !in_array($data['permiso'], ['Vendedor', 'Supervisor'], true)) {
            ApiResponse::error('Ingresa un correo válido y elige Vendedor o Supervisor.', 400);
            return;
        }
        if (($data['password'] ?? '') !== '' && strlen($data['password']) < 8) {
            ApiResponse::error('La contraseña debe tener al menos 8 caracteres.', 400);
            return;
        }
        if (!empleados::hasAccount($id) && empty($data['password'])) {
            ApiResponse::error('Define una contraseña para crear la cuenta de este empleado.', 400);
            return;
        }
        $updated = empleados::update($id, $data);
        echo json_encode(['estado' => (bool) $updated, 'message' => $updated ? 'empleado actualizado correctamente' : 'no se pudo actualizar el empleado']);
    }

    public function delete($id)
    {
        $deleted = empleados::delete($id);
        echo json_encode(['estado' => (bool) $deleted, 'message' => $deleted ? 'empleado eliminado correctamente' : 'no se pudo eliminar el empleado']);
    }
}
