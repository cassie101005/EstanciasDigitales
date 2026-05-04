<?php
$input = file_get_contents("php://input");
$data = json_decode($input, true);

require_once __DIR__ . '/../conexion.php';

$idRol = intval($data['idRol'] ?? 0);

$rolesPermitidos = [2, 3];

if (!in_array($idRol, $rolesPermitidos)) {
    echo json_encode([
        'success' => false,
        'message' => 'Rol no válido.'
    ]);
    http_response_code(400);
    exit;
}

$nombre = trim($data['nombre'] ?? '');
$apellido = trim($data['apellido'] ?? '');
$fechaNacimiento = trim($data['fechaNacimiento'] ?? '');
$correo = trim($data['correo'] ?? '');
$contrasenia = trim($data['contrasenia'] ?? '');

if (empty($nombre) || empty($apellido) || empty($correo) || empty($contrasenia) || empty($fechaNacimiento)) {
    echo json_encode([
        'success' => false,
        'message' => 'Completa todos los campos correctamente.'
    ]);
    http_response_code(400);
    exit;
}

$nombre = filter_var($nombre, FILTER_SANITIZE_SPECIAL_CHARS);
$apellido = filter_var($apellido, FILTER_SANITIZE_SPECIAL_CHARS);
$correo = strtolower(filter_var($correo, FILTER_SANITIZE_EMAIL));

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'El correo electrónico no es válido.']);
    exit;
}

if (!preg_match('/@.+\.(com|mx|net|org|edu)$/i', $correo)) {
    echo json_encode(['success' => false, 'message' => 'El correo debe contener @ y un dominio válido.']);
    exit;
}

if (
    strlen($contrasenia) < 8 ||
    !preg_match('/[A-Z]/', $contrasenia) ||
    !preg_match('/[a-z]/', $contrasenia) ||
    !preg_match('/[0-9]/', $contrasenia)
) {
    echo json_encode([
        'success' => false,
        'message' => 'La contraseña debe tener mínimo 8 caracteres, incluir mayúsculas, minúsculas y números.'
    ]);
    exit;
}
?>