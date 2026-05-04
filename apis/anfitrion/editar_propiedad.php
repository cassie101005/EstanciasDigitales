<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Hacer que MySQLi lance excepciones en lugar de errores silenciosos
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Solo peticiones del mismo origen (CORS por defecto bloquea el resto sin esta cabecera)
header("Content-Type: application/json");

try {
    require_once '../../negocio/auth/verificar_sesion.php';
    validarSesionAPI('anfitrion');

    if (!isset($_SESSION['idUsuario'])) {
        throw new Exception("Sesión no iniciada.");
    }

    $idUsuario = intval($_SESSION['idUsuario']);
    $accion = $_REQUEST['accion'] ?? 'obtener';

    // Verificar si el payload excede el post_max_size de PHP
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && $_SERVER['CONTENT_LENGTH'] > 0) {
        throw new Exception("Los archivos subidos son demasiado grandes. El límite del servidor ha sido excedido.");
    }

    require_once '../../negocio/anfitrion/editar_propiedad.php';

    ob_end_clean();
    if (isset($resultado)) {
        echo json_encode($resultado);
    } else {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Acción no encontrada o lógica fallida.']);
    }

} catch (Throwable $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'ok'      => false,
        'error'   => 'Error en el servidor',
        'mensaje' => $e->getMessage()
    ]);
}
?>
