<?php
header('Content-Type: application/json');
require_once '../../negocio/auth/verificar_sesion.php';
validarSesionAPI('huesped');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../../datos/conexion.php';
    
    // 1. Extraer y validar datos
    require_once '../../datos/huesped/resenia.php';
    
    $idUsuario = $_SESSION['idUsuario'];

    // 2. Ejecutar lógica de negocio
    require_once '../../negocio/huesped/resenia.php';
    $reseniaNegocio = new ReseniaNegocio($conexion);
    $resultado = $reseniaNegocio->guardarResenia($idPropiedad, $idUsuario, $calificacion, $comentario);

    echo json_encode($resultado);
} else {
    echo json_encode(['ok' => false, 'error' => 'Método no permitido.']);
}
?>
