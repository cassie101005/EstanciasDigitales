<?php
header('Content-Type: application/json');
require_once '../../negocio/auth/verificar_sesion.php';
validarSesionAPI('anfitrion');
require_once '../../datos/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../../datos/conexion.php';
    
    // 1. Extraer y validar datos
    require_once '../../datos/anfitrion/respuesta.php';
    
    // 2. Ejecutar lógica de negocio
    require_once '../../negocio/anfitrion/respuesta.php';

    echo json_encode($resultado);
} else {
    echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
}
?>
