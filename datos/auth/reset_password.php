<?php
// Este archivo se encarga únicamente de recibir, limpiar y validar los datos enviados para restablecer contraseña

$input = file_get_contents("php://input");
$data = json_decode($input, true);

$correo = trim($data['correo'] ?? '');
$nuevaContrasenia = trim($data['nuevaContrasenia'] ?? '');

if (empty($correo) || empty($nuevaContrasenia)) {
    echo json_encode(['error' => 'Correo y nueva contraseña son obligatorios']);
    http_response_code(400);
    exit;
}
?>
