<?php
ob_start(); // Captura warnings/notices para que no corrompan el JSON
header('Content-Type: application/json');
require_once '../../negocio/auth/verificar_sesion.php';
validarSesionAPI('huesped');
require_once '../../datos/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 1. Extraer y validar datos
        require_once '../../datos/huesped/reserva.php';
        
        // 1.1 Validación de seguridad: Forzar uso de idUsuario de la SESIÓN
        $idUsuarioLogueado = intval($_SESSION['idUsuario']);
        
        // Si el idUsuario enviado no coincide, es un error de seguridad
        if ($idUsuario !== $idUsuarioLogueado) {
            ob_end_clean();
            http_response_code(403);
            echo json_encode(['ok' => false, 'mensaje' => 'Error de seguridad: Sesión no válida para esta operación.']);
            exit();
        }
        
        // 2. Ejecutar lógica de negocio
        require_once '../../negocio/huesped/reserva.php';

        // Descartar cualquier output previo (warnings, notices) y enviar respuesta limpia
        if (ob_get_length()) ob_end_clean(); 
        echo json_encode($resultado);
        exit();

    } catch (Exception $e) {
        if (ob_get_length()) ob_end_clean();
        http_response_code(500);
        echo json_encode(['ok' => false, 'mensaje' => 'Error al procesar la reserva: ' . $e->getMessage()]);
        exit();
    } catch (Error $e) {
        if (ob_get_length()) ob_end_clean();
        http_response_code(500);
        echo json_encode(['ok' => false, 'mensaje' => 'Error crítico en el servidor.']);
        exit();
    }
} else {
    if (ob_get_length()) ob_end_clean();
    http_response_code(405);
    echo json_encode(['ok' => false, 'mensaje' => 'Método no permitido']);
}
?>
