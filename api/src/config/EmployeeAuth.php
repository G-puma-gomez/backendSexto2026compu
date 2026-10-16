<?php
require_once __DIR__ . '/conexionDB.php';
require_once __DIR__ . '/AdminAuth.php';

class EmployeeAuth
{
    private static function secret(): string
    {
        return AdminAuth::tokenSecret();
    }

    public static function login(array $data): array
    {
        $email = trim((string)($data['email'] ?? ''));
        $password = (string)($data['password'] ?? '');
        if ($email === '' || $password === '') {
            http_response_code(400);
            return ['estado' => false, 'message' => 'Correo y contraseña son obligatorios.'];
        }

        $rows = ConexionPDO::query("SELECT e.id, e.nombre, e.apellidos, e.permiso, u.email, u.password_hash
            FROM usuarios u JOIN empleados e ON e.id=u.cod_empleado
            WHERE u.email=:email AND u.estado_cuenta='Activo' LIMIT 1", [':email' => $email]);
        $employee = $rows[0] ?? null;
        $secret = self::secret();
        if (!$employee || !password_verify($password, $employee['password_hash']) || $secret === '') {
            http_response_code(401);
            return ['estado' => false, 'message' => 'Correo o contraseña incorrectos.'];
        }

        ConexionPDO::execute('UPDATE usuarios SET ultimo_acceso=NOW() WHERE email=:email', [':email' => $email]);
        $payload = self::base64url(json_encode([
            'sub' => (int)$employee['id'],
            'role' => $employee['permiso'],
            'exp' => time() + 28800,
        ], JSON_UNESCAPED_SLASHES));
        $token = $payload . '.' . self::base64url(hash_hmac('sha256', $payload, $secret, true));
        unset($employee['password_hash']);
        return ['estado' => true, 'token' => $token, 'employee' => $employee];
    }

    private static function base64url(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
