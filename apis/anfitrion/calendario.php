<?php
/**
 * API Calendario Anfitrión
 * apis/anfitrion/calendario.php
 */
header('Content-Type: application/json');

try {
    require_once '../../negocio/auth/verificar_sesion.php';
    validarSesionAPI('anfitrion');

    $idUsuario = intval($_SESSION['idUsuario']);
    $accion = $_REQUEST['accion'] ?? '';

    // Extracción de datos
    require_once '../../datos/anfitrion/calendario.php';

    // Lógica de negocio
    require_once '../../negocio/anfitrion/calendario.php';

    if (isset($resultado)) {
        echo json_encode($resultado);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Acción no reconocida o resultado no definido.']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error interno del servidor',
        'detalles' => $e->getMessage()
    ]);
}
