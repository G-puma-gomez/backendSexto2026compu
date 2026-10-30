<?php
require_once __DIR__ . '/conexionDB.php';

class EmployeeAuth
{
    public static function login(array $data): array
    {
        $email = trim((string)($data['email'] ?? $data['username'] ?? ''));
        $password = (string)($data['password'] ?? '');
        if ($email === '' || $password === '') {
            http_response_code(400);
            return ['estado' => false, 'message' => 'Correo y contraseña son obligatorios.'];
        }

        $employeeColumns = self::columns('empleados');
        if (in_array('email', $employeeColumns, true) && in_array('password_hash', $employeeColumns, true)) {
            $rows = ConexionPDO::query('SELECT id, nombre, apellidos, permiso, email, password_hash
                FROM empleados WHERE email=:email LIMIT 1', [':email' => $email]);
            $accountTable = 'empleados';
        } else {
            $roleColumn = in_array('permiso', $employeeColumns, true) ? 'permiso' : 'cargo';
            $rows = ConexionPDO::query("SELECT e.id, e.nombre, e.apellidos, e.`$roleColumn` AS permiso,
                u.email, u.password_hash, u.estado_cuenta
                FROM empleados e JOIN usuarios u ON u.cod_empleado=e.id
                WHERE u.email=:email LIMIT 1", [':email' => $email]);
            $accountTable = 'usuarios';
        }

        $employee = $rows[0] ?? null;
        $storedPassword = (string)($employee['password_hash'] ?? '');
        $validPassword = $storedPassword !== ''
            && (password_verify($password, $storedPassword) || hash_equals($storedPassword, $password));
        if (!$employee || !$validPassword || (($employee['estado_cuenta'] ?? 'Activo') !== 'Activo')) {
            http_response_code(401);
            return ['estado' => false, 'message' => 'Correo o contraseña incorrectos.'];
        }

        if (!password_get_info($storedPassword)['algo']) {
            $storedPassword = password_hash($password, PASSWORD_DEFAULT);
            if ($accountTable === 'empleados') {
                ConexionPDO::execute('UPDATE empleados SET password_hash=:hash WHERE id=:id', [':hash' => $storedPassword, ':id' => $employee['id']]);
            } else {
                ConexionPDO::execute('UPDATE usuarios SET password_hash=:hash WHERE cod_empleado=:id', [':hash' => $storedPassword, ':id' => $employee['id']]);
            }
        }
        if ($accountTable === 'usuarios') {
            ConexionPDO::execute('UPDATE usuarios SET ultimo_acceso=NOW() WHERE cod_empleado=:id', [':id' => $employee['id']]);
        }

        $payload = self::base64url(json_encode([
            'sub' => (int)$employee['id'],
            'role' => $employee['permiso'],
            'exp' => time() + 28800,
        ], JSON_UNESCAPED_SLASHES));
        $token = $payload . '.' . self::base64url(hash_hmac('sha256', $payload, $storedPassword, true));
        unset($employee['password_hash'], $employee['estado_cuenta']);
        return ['estado' => true, 'token' => $token, 'employee' => $employee];
    }

    private static function columns(string $table): array
    {
        return ConexionPDO::connect()->query("SHOW COLUMNS FROM `$table`")->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    private static function base64url(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
