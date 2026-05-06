<?php
session_start();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    // 1. Extraer y validar datos
    require_once '../../datos/auth/login.php';

    // 2. Ejecutar lógica de negocio
    require_once '../../negocio/auth/login.php';

    // 4. Retornar resultados en formato JSON
    if (isset($resultado['error'])) {
        if (strpos($resultado['error'], 'bloqueada') !== false) {
            http_response_code(423); // Locked
        } elseif (strpos($resultado['error'], 'incorrecta') !== false || strpos($resultado['error'], 'no existe') !== false) {
            http_response_code(401); // Unauthorized
        } else {
            http_response_code(400); // Bad Request
        }
    }
    echo json_encode($resultado);

} else {
    http_response_code(405);
    echo json_encode([
        'error' => 'Metodo no permitido o datos invalidos.'
    ]);
}