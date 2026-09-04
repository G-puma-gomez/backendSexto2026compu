<?php

class ApiResponse
{
    public static function input(array $requiredFields): ?array
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            self::error('El cuerpo de la solicitud debe ser un objeto JSON válido.', 400);
            return null;
        }

        $missing = [];
        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $data) || trim((string) $data[$field]) === '') {
                $missing[] = $field;
            }
        }

        if ($missing !== []) {
            self::error('Faltan campos obligatorios.', 400, ['campos_faltantes' => $missing]);
            return null;
        }

        return $data;
    }

    public static function error(string $message, int $status = 400, array $extra = []): void
    {
        http_response_code($status);
        echo json_encode(array_merge([
            'estado' => false,
            'message' => $message,
        ], $extra), JSON_UNESCAPED_UNICODE);
    }
}
