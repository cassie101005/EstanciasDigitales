<?php
// Permitir peticiones
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Extraer y validar datos
    require_once '../../datos/auth/registro.php';

    // 2. Ejecutar lógica de negocio
    require_once '../../negocio/auth/registro.php';

    // 4. Respuesta
    echo json_encode($resultado);

} else {
    echo json_encode(['error' => 'Metodo no permitido']);
    http_response_code(405);
}