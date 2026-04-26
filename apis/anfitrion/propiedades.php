<?php
header('Content-Type: application/json');
require_once '../../negocio/auth/verificar_sesion.php';
validarSesionAPI('anfitrion');

$idUsuario = intval($_SESSION['idUsuario']);
$accion    = $_GET['accion'] ?? 'listar';

require_once '../../datos/anfitrion/propiedades.php';

require_once '../../negocio/anfitrion/propiedades.php';

if (isset($resultado)) {
    echo json_encode($resultado);
} else {
    echo json_encode(['error' => 'Error en la lógica de negocio.']);
    http_response_code(400);
}
