<?php
// Este archivo se encarga únicamente de recibir, limpiar y validar los datos enviados para el login

$correo = trim($_POST['correo'] ?? '');
$contrasenia = trim($_POST['contrasenia'] ?? '');
$rol = trim($_POST['rol'] ?? '');

// Validaciones básicas
if (empty($correo) || empty($contrasenia) || empty($rol)) {
    echo json_encode([
        'error' => 'Por favor, completa todos los campos correctamente.'
    ]);
    http_response_code(400);
    exit;
}
?>
