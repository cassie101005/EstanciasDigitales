<?php
// Este archivo se encarga únicamente de recibir, limpiar y validar los datos enviados para restablecer contraseña

$input = file_get_contents("php://input");
$data = json_decode($input, true);

$correo = filter_var(trim($data['correo'] ?? ''), FILTER_SANITIZE_EMAIL);
$nuevaContrasenia = trim($data['nuevaContrasenia'] ?? '');

if (empty($correo) || empty($nuevaContrasenia)) {
    echo json_encode(['error' => 'Correo y nueva contraseña son obligatorios']);
    http_response_code(400);
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => 'El correo no tiene un formato válido']);
    http_response_code(400);
    exit;
}

if (strlen($nuevaContrasenia) < 8) {
    echo json_encode(['error' => 'La contraseña debe tener al menos 8 caracteres']);
    http_response_code(400);
    exit;
}

if (!preg_match('/[A-Z]/', $nuevaContrasenia)) {
    echo json_encode(['error' => 'La contraseña debe contener al menos una letra mayúscula']);
    http_response_code(400);
    exit;
}
?>
