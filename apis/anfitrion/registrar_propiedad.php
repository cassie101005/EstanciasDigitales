<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

require_once '../../negocio/auth/verificar_sesion.php';
validarSesionAPI('anfitrion');
require_once '../../datos/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$accion = $_REQUEST['accion'] ?? 'guardar';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../../datos/anfitrion/propiedad.php';
}

require_once '../../negocio/anfitrion/registrar_propiedad.php';

if (isset($resultado)) {
    echo json_encode($resultado);
} else {
    echo json_encode(['error' => 'Acción no encontrada o lógica fallida.']);
    http_response_code(400);
}