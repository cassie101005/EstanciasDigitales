<?php
// Permitir peticiones
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Obtener datos (JSON)
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    $idRol = intval($data['idRol'] ?? 0);
    $nombre = $data['nombre'] ?? '';
    $apellido = $data['apellido'] ?? '';
    $correo = $data['correo'] ?? '';
    $contrasenia = $data['contrasenia'] ?? '';

    // 2. Validaciones básicas
    if ($idRol <= 0 || empty($nombre) || empty($apellido) || empty($correo) || empty($contrasenia)) {
        echo json_encode(['error' => 'Completa todos los campos']);
        http_response_code(400);
        exit;
    }

    // 3. Lógica
    require_once '../../negocio/auth/registro.php';

    // 4. Respuesta
    echo json_encode($resultado);

} else {
    echo json_encode(['error' => 'Metodo no permitido']);
    http_response_code(405);
}