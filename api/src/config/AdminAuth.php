<?php
require_once __DIR__ . '/conexionDB.php';

class AdminAuth
{
    private static ?array $employeeColumns = null;

    private static function employeeColumns(): array
    {
        if (self::$employeeColumns === null) {
            self::$employeeColumns = ConexionPDO::connect()->query('SHOW COLUMNS FROM empleados')->fetchAll(PDO::FETCH_COLUMN, 0);
        }
        return self::$employeeColumns;
    }

    private static function usesEmployeeAccounts(): bool
    {
        $columns = self::employeeColumns();
        return in_array('email', $columns, true) && in_array('password_hash', $columns, true);
    }

    private static function findEmployee(string $login): ?array
    {
        if (self::usesEmployeeAccounts()) {
            $rows = ConexionPDO::query('SELECT id, nombre, apellidos, permiso, email, password_hash
                FROM empleados WHERE email=:email OR ci=:ci LIMIT 1', [':email' => $login, ':ci' => $login]);
        } else {
            $roleColumn = in_array('permiso', self::employeeColumns(), true) ? 'permiso' : 'cargo';
            $rows = ConexionPDO::query("SELECT e.id, e.nombre, e.apellidos, e.`$roleColumn` AS permiso,
                u.email, u.password_hash, u.estado_cuenta
                FROM empleados e JOIN usuarios u ON u.cod_empleado=e.id
                WHERE u.email=:email OR e.ci=:ci LIMIT 1", [':email' => $login, ':ci' => $login]);
        }
        return $rows[0] ?? null;
    }

    private static function passwordHashFor(int $employeeId): string
    {
        if (self::usesEmployeeAccounts()) {
            $rows = ConexionPDO::query("SELECT password_hash FROM empleados WHERE id=:id AND LOWER(TRIM(permiso)) IN ('administrador','admin') LIMIT 1", [':id' => $employeeId]);
        } else {
            $roleColumn = in_array('permiso', self::employeeColumns(), true) ? 'permiso' : 'cargo';
            $rows = ConexionPDO::query("SELECT u.password_hash FROM empleados e JOIN usuarios u ON u.cod_empleado=e.id
                WHERE e.id=:id AND LOWER(TRIM(e.`$roleColumn`)) IN ('administrador','admin') LIMIT 1", [':id' => $employeeId]);
        }
        return (string)($rows[0]['password_hash'] ?? '');
    }

    public static function login(array $data): array
    {
        $login = trim((string)($data['username'] ?? $data['email'] ?? ''));
        $password = (string)($data['password'] ?? '');
        if ($login === '' || $password === '') {
            http_response_code(400);
            return ['estado' => false, 'message' => 'Correo y contraseña son obligatorios.'];
        }

        $employee = self::findEmployee($login);
        $role = strtolower(trim((string)($employee['permiso'] ?? '')));
        $storedPassword = (string)($employee['password_hash'] ?? '');
        $validPassword = $storedPassword !== '' && (password_verify($password, $storedPassword) || hash_equals($storedPassword, $password));

        if (!$employee || !in_array($role, ['administrador', 'admin'], true) || !$validPassword
            || (($employee['estado_cuenta'] ?? 'Activo') !== 'Activo')) {
            http_response_code(401);
            return ['estado' => false, 'message' => 'Correo o contraseña incorrectos, o la cuenta no tiene permiso de administrador.'];
        }

        // Permite credenciales existentes en texto y las convierte a hash después del primer acceso.
        if (!password_get_info($storedPassword)['algo']) {
            $storedPassword = password_hash($password, PASSWORD_DEFAULT);
            if (self::usesEmployeeAccounts()) {
                ConexionPDO::execute('UPDATE empleados SET password_hash=:hash WHERE id=:id', [':hash' => $storedPassword, ':id' => $employee['id']]);
            } else {
                ConexionPDO::execute('UPDATE usuarios SET password_hash=:hash WHERE cod_empleado=:id', [':hash' => $storedPassword, ':id' => $employee['id']]);
            }
        }

        $payload = self::base64url(json_encode([
            'sub' => (int)$employee['id'],
            'role' => 'Administrador',
            'exp' => time() + 28800,
        ], JSON_UNESCAPED_SLASHES));
        $token = $payload . '.' . self::signature($payload, $storedPassword);
        return [
            'estado' => true,
            'token' => $token,
            'username' => $employee['email'],
            'employee' => [
                'id' => (int)$employee['id'],
                'nombre' => $employee['nombre'],
                'apellidos' => $employee['apellidos'],
                'permiso' => $employee['permiso'],
                'email' => $employee['email'],
            ],
        ];
    }

    public static function requireAdmin(): bool
    {
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $authorization = $headers['Authorization'] ?? $headers['authorization'] ?? ($_SERVER['HTTP_AUTHORIZATION'] ?? '');
        if (!preg_match('/^Bearer\\s+([A-Za-z0-9_.-]+)$/i', $authorization, $matches)) {
            return self::unauthorized();
        }

        $parts = explode('.', $matches[1]);
        if (count($parts) !== 2) return self::unauthorized();
        [$payload, $signature] = $parts;
        $decoded = json_decode(self::base64urlDecode($payload), true);
        $employeeId = (int)($decoded['sub'] ?? 0);
        if (!is_array($decoded) || $employeeId < 1 || ($decoded['role'] ?? '') !== 'Administrador'
            || (int)($decoded['exp'] ?? 0) < time()) {
            return self::unauthorized();
        }

        $passwordHash = self::passwordHashFor($employeeId);
        if ($passwordHash === '' || !hash_equals(self::signature($payload, $passwordHash), $signature)) {
            return self::unauthorized();
        }
        return true;
    }

    private static function unauthorized(): bool
    {
        ApiResponse::error('Debes iniciar sesión como administrador para realizar esta acción.', 401);
        return false;
    }

    private static function signature(string $payload, string $key): string
    {
        return self::base64url(hash_hmac('sha256', $payload, $key, true));
    }

    private static function base64url(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private static function base64urlDecode(string $value): string
    {
        return (string)base64_decode(strtr($value, '-_', '+/') . str_repeat('=', (4 - strlen($value) % 4) % 4));
    }
}
