<?php
session_start();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Extraer datos del cliente
    $correo = trim($_POST['correo'] ?? '');
    $contrasenia = trim($_POST['contrasenia'] ?? '');
    $rol = trim($_POST['rol'] ?? '');

    // 2. Validaciones básicas
    if (empty($correo) || empty($contrasenia) || empty($rol)) {
        echo json_encode([
            'error' => 'Por favor, completa todos los campos correctamente.'
        ]);
        http_response_code(400);
        exit;
    }

    // 3. Ejecutar lógica de negocio de manera secuencial
    require_once '../../negocio/auth/login.php';

    // 4. Retornar resultados en formato JSON
    echo json_encode($resultado);

} else {
    echo json_encode([
        'error' => 'Metodo no permitido o datos invalidos.'
    ]);
    http_response_code(405);
}