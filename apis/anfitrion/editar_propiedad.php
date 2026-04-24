<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

session_start();
if (!isset($_SESSION['idUsuario'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado.']);
    exit;
}

$idUsuario = intval($_SESSION['idUsuario']);
$accion = $_REQUEST['accion'] ?? 'obtener';

require_once '../../negocio/anfitrion/editar_propiedad.php';

if (isset($resultado)) {
    echo json_encode($resultado);
} else {
    echo json_encode(['error' => 'Acción no encontrada o lógica fallida.']);
    http_response_code(400);
}
?>
