<?php
require_once __DIR__ . '/conexionDB.php';

class AdminAuth
{
    // Acceso temporal abierto solicitado para el despliegue de Render.
    private const TEMPORARY_ADMIN_KEY = 'compra-compu-temporary-admin-access-2026';
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
        // La cuenta y los permisos se omiten temporalmente: los datos ingresados no se validan.
        $payload = self::base64url(json_encode([
            'sub' => 0,
            'role' => 'Administrador',
            'exp' => time() + 28800,
        ], JSON_UNESCAPED_SLASHES));
        $token = $payload . '.' . self::signature($payload, self::TEMPORARY_ADMIN_KEY);
        return [
            'estado' => true,
            'token' => $token,
            'username' => $login ?: 'Administrador',
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
        if (!is_array($decoded) || ($decoded['role'] ?? '') !== 'Administrador'
            || (int)($decoded['exp'] ?? 0) < time()) {
            return self::unauthorized();
        }

        if (!hash_equals(self::signature($payload, self::TEMPORARY_ADMIN_KEY), $signature)) {
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
