<?php
// Fallback front controller for hosts where frontend is the document root and
// /api is not configured as an Apache Alias to api/public.
$backendPublic = dirname(__DIR__, 2) . '/api/public';

if (!is_file($backendPublic . '/index.php')) {
    http_response_code(503);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'No se encontró el backend de la API.']);
    exit;
}

// Uploaded product images are also exposed under /api/uploads/ by the API.
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if (preg_match('#^/api/uploads/([a-f0-9]{32}\.(jpg|png|webp|gif))$#i', $requestPath, $imageMatch)) {
    $imagePath = $backendPublic . '/uploads/' . $imageMatch[1];
    if (!is_file($imagePath)) {
        http_response_code(404);
        exit;
    }
    header('Content-Type: image/' . ($imageMatch[2] === 'jpg' ? 'jpeg' : strtolower($imageMatch[2])));
    header('Content-Length: ' . filesize($imagePath));
    header('Cache-Control: public, max-age=86400');
    readfile($imagePath);
    exit;
}

// The existing backend uses relative require paths from api/public.
chdir($backendPublic);
require $backendPublic . '/index.php';
