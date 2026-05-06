<?php
ob_start(); // Captura warnings/notices para que no corrompan el JSON
header('Content-Type: application/json');
require_once '../../negocio/auth/verificar_sesion.php';
validarSesionAPI('huesped');
require_once '../../datos/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Extraer y validar datos
    require_once '../../datos/huesped/reserva.php';
    
    // 1.1 Validación de seguridad: El idUsuario del POST debe coincidir con el de la SESIÓN
    if ($idUsuario !== intval($_SESSION['idUsuario'])) {
        ob_end_clean();
        echo json_encode(['ok' => false, 'mensaje' => 'Error de seguridad: El usuario de la reserva no coincide con el usuario autenticado.']);
        exit();
    }
    
    // 2. Ejecutar lógica de negocio
    require_once '../../negocio/huesped/reserva.php';

    ob_end_clean(); // Descartar cualquier output previo (warnings, notices)
    echo json_encode($resultado);
} else {
    ob_end_clean();
    echo json_encode(['ok' => false, 'mensaje' => 'Método no permitido']);
}
?>
