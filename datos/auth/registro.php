<?php
// Este archivo se encarga únicamente de recibir, limpiar y validar los datos enviados para el registro

$input = file_get_contents("php://input");
$data = json_decode($input, true);

$idRol = intval($data['idRol'] ?? 0);
$nombre = trim($data['nombre'] ?? '');
$apellido = trim($data['apellido'] ?? '');
$correo = trim($data['correo'] ?? '');
$contrasenia = trim($data['contrasenia'] ?? '');

// Validaciones básicas
if ($idRol <= 0 || empty($nombre) || empty($apellido) || empty($correo) || empty($contrasenia)) {
    echo json_encode(['error' => 'Completa todos los campos correctamente.']);
    http_response_code(400);
    exit;
}
?>
