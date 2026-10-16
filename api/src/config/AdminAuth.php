<?php

class AdminAuth
{
    private static function config(): array
    {
        $path = __DIR__ . '/admin.credentials.php';
        $config = is_file($path) ? require $path : false;
        return is_array($config) ? $config : [];
    }

    public static function tokenSecret(): string
    {
        return (string)(self::config()['ADMIN_TOKEN_SECRET'] ?? '');
    }

    public static function login(array $data): array
    {
        $config = self::config();
        $username = (string)($config['ADMIN_USER'] ?? '');
        $passwordHash = (string)($config['ADMIN_PASSWORD_HASH'] ?? '');
        $secret = self::tokenSecret();
        if ($username === '' || $passwordHash === '' || $secret === ''
            || !hash_equals($username, (string)($data['username'] ?? ''))
            || !password_verify((string)($data['password'] ?? ''), $passwordHash)) {
            http_response_code(401);
            return ['estado' => false, 'message' => 'Usuario o contraseña incorrectos.'];
        }

        $payload = self::base64url(json_encode([
            'sub' => $username,
            'exp' => time() + 28800,
        ], JSON_UNESCAPED_SLASHES));
        $signature = self::base64url(hash_hmac('sha256', $payload, $secret, true));
        return ['estado' => true, 'token' => $payload . '.' . $signature, 'username' => $username];
    }

    public static function requireAdmin(): bool
    {
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $authorization = $headers['Authorization'] ?? $headers['authorization'] ?? ($_SERVER['HTTP_AUTHORIZATION'] ?? '');
        if (!preg_match('/^Bearer\s+([A-Za-z0-9_.-]+)$/i', $authorization, $matches)) {
            return self::unauthorized();
        }

        $parts = explode('.', $matches[1]);
        $config = self::config();
        $secret = self::tokenSecret();
        if (count($parts) !== 2 || $secret === '') return self::unauthorized();
        [$payload, $signature] = $parts;
        $expected = self::base64url(hash_hmac('sha256', $payload, $secret, true));
        $decoded = json_decode(self::base64urlDecode($payload), true);
        if (!hash_equals($expected, $signature) || !is_array($decoded)
            || (int)($decoded['exp'] ?? 0) < time()
            || !hash_equals((string)($config['ADMIN_USER'] ?? ''), (string)($decoded['sub'] ?? ''))) {
            return self::unauthorized();
        }
        return true;
    }

    private static function unauthorized(): bool
    {
        ApiResponse::error('Debes iniciar sesión como administrador para realizar esta acción.', 401);
        return false;
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
