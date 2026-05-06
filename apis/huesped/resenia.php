<?php
header('Content-Type: application/json');
require_once '../../negocio/auth/verificar_sesion.php';
validarSesionAPI('huesped');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../../datos/conexion.php';
    require_once '../../negocio/huesped/resenia.php';
    
    $idUsuario = $_SESSION['idUsuario'];
    $reseniaNegocio = new ReseniaNegocio($conexion);

    $idResenia = isset($_POST['idResenia']) ? intval($_POST['idResenia']) : 0;
    
    if ($idResenia > 0) {
        $comentario = isset($_POST['vComentario']) ? $_POST['vComentario'] : '';
        $resultado = $reseniaNegocio->actualizarResenia($idResenia, $idUsuario, $comentario);
    } else {
        require_once '../../datos/huesped/resenia.php';
        $resultado = $reseniaNegocio->guardarResenia($idPropiedad, $idUsuario, $calificacion, $comentario);
    }

    echo json_encode($resultado);
} else {
    echo json_encode(['ok' => false, 'error' => 'Método no permitido.']);
}
?>
