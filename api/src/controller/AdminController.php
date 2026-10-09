<?php
require_once __DIR__ . '/../config/AdminAuth.php';

class AdminController
{
    public function session(): void
    {
        if (AdminAuth::requireAdmin()) {
            echo json_encode(['estado' => true, 'username' => 'administrador']);
        }
    }

    public function login(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) {
            ApiResponse::error('Completa el usuario y la contraseña.', 400);
            return;
        }
        echo json_encode(AdminAuth::login($data), JSON_UNESCAPED_UNICODE);
    }
}
