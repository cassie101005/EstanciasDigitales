<?php
// Permitir peticiones desde cualquier origen
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // 1. Ejecutar lógica de negocio de manera secuencial
    require_once '../../negocio/anfitrion/listar_servicios.php';

    // 2. Retornar resultados en formato JSON
    echo json_encode($resultado);

} else {
    echo json_encode(['error' => 'Metodo no permitido o datos invalidos.']);
    http_response_code(405);
}