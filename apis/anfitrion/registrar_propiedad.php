<?php
ob_start();
// Solo peticiones del mismo origen (CORS por defecto bloquea el resto sin esta cabecera)
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
    // Verificar si el payload excede el post_max_size de PHP
    if (empty($_POST) && $_SERVER['CONTENT_LENGTH'] > 0) {
        echo json_encode(['error' => 'Los archivos subidos son demasiado grandes para el servidor.']);
        exit;
    }
    require_once '../../datos/anfitrion/propiedad.php';
}

require_once '../../negocio/anfitrion/registrar_propiedad.php';

ob_end_clean();
if (isset($resultado)) {
    echo json_encode($resultado);
} else {
    echo json_encode(['error' => 'Acción no encontrada o lógica fallida.']);
    http_response_code(400);
}