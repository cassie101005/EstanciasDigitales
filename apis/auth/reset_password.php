<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    $correo = $data['correo'] ?? '';
    $nuevaContrasenia = $data['nuevaContrasenia'] ?? '';

    if (empty($correo) || empty($nuevaContrasenia)) {
        echo json_encode(['error' => 'Correo y nueva contraseña son obligatorios']);
        http_response_code(400);
        exit;
    }

    require_once '../../datos/conexion.php';
    require_once '../../datos/auth/queries_auth.php';

    $queriesAuth = new QueriesAuth($conexion);

    // Verificar si el correo existe
    $verificar = $queriesAuth->verificarCorreoExistente($correo);
    if ($verificar->num_rows === 0) {
        echo json_encode(['ok' => false, 'mensaje' => 'El correo electrónico no está registrado.']);
        exit;
    }

    // Actualizar contraseña
    if ($queriesAuth->actualizarContrasenia($correo, $nuevaContrasenia)) {
        echo json_encode(['ok' => true, 'mensaje' => 'Contraseña actualizada correctamente.']);
    } else {
        echo json_encode(['ok' => false, 'mensaje' => 'Error al actualizar la contraseña.']);
    }

} else {
    echo json_encode(['error' => 'Metodo no permitido']);
    http_response_code(405);
}
