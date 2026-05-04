<?php
ob_start(); // Evita que warnings/notices contaminen la respuesta JSON
header('Content-Type: application/json');
require_once '../../negocio/auth/verificar_sesion.php';
validarSesionAPI('anfitrion');
require_once '../../datos/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 1. Extraer y validar datos
        require_once '../../datos/anfitrion/respuesta.php';
        
        // 2. Ejecutar lógica de negocio
        require_once '../../negocio/anfitrion/respuesta.php';

        ob_end_clean();
        echo json_encode($resultado);
    } catch (Throwable $e) {
        ob_end_clean();
        http_response_code(500);
        echo json_encode([
            "ok"    => false,
            "error" => "Error interno del servidor: " . $e->getMessage()
        ]);
    }
} else {
    ob_end_clean();
    echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
}
?>
