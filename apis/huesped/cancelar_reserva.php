<?php
header('Content-Type: application/json');
require_once '../../negocio/auth/verificar_sesion.php';
validarSesionAPI('huesped');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../../datos/conexion.php';
    
    // 1. Extraer y validar datos
    require_once '../../datos/huesped/cancelar_reserva.php';
    
    $idUsuario = $_SESSION['idUsuario'];

    // 2. Ejecutar lógica de negocio
    require_once '../../negocio/huesped/cancelar_reserva.php';

    echo json_encode($resultado);
} else {
    echo json_encode(['ok' => false, 'mensaje' => 'Método no permitido']);
    http_response_code(405);
}
?>
