<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = ['success' => false, 'message' => 'Error inesperado'];

    require_once '../../datos/auth/registro.php';
    require_once '../../negocio/auth/registro.php';

    if (!$resultado['success']) {
        if (strpos($resultado['message'], 'ya está registrado') !== false) {
            http_response_code(409); // Conflict
        } else {
            http_response_code(400); // Bad Request
        }
    }

    echo json_encode($resultado);
    exit;
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Metodo no permitido'
    ]);
    http_response_code(405);
    exit;
}