<?php
session_start();
header("Content-Type: application/json");

// Verificar sesión
if (!isset($_SESSION['idUsuario'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado. Inicia sesión.']);
    exit;
}

$idUsuario = intval($_SESSION['idUsuario']);
$accion    = $_GET['accion'] ?? 'listar';

if ($accion === 'detalle') {
    $idPropiedad = intval($_GET['id'] ?? 0);
    if ($idPropiedad <= 0) {
        echo json_encode(['error' => 'ID inválido.']);
        exit;
    }
} else if ($accion !== 'listar') {
    echo json_encode(['error' => 'Acción no reconocida.']);
    exit;
}

require_once '../../negocio/anfitrion/propiedades.php';

if (isset($resultado)) {
    echo json_encode($resultado);
} else {
    echo json_encode(['error' => 'Error en la lógica de negocio.']);
    http_response_code(400);
}
