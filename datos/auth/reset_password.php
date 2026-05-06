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

if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d\w\W]{8,}$/', $nuevaContrasenia)) {
    echo json_encode(['error' => 'La contraseña debe tener mínimo 8 caracteres, incluir mayúsculas, minúsculas y números.']);
    http_response_code(400);
    exit;
}
?>
