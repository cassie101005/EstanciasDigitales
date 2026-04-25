<?php
header('Content-Type: application/json');
require_once '../../negocio/auth/verificar_sesion.php';
validarSesionAPI('huesped');
require_once '../../datos/conexion.php';
require_once '../../negocio/huesped/resenia.php';

    $idPropiedad = intval($_POST['idPropiedad'] ?? 0);
    $comentario = trim($_POST['vComentario'] ?? '');
    $calificacion = intval($_POST['iCalificacion'] ?? 0);
    $idUsuario = $_SESSION['idUsuario'];

    if ($idPropiedad <= 0 || empty($comentario) || $calificacion < 0 || $calificacion > 5) {
        echo json_encode(['ok' => false, 'error' => 'Datos incompletos o comentario vacío.']);
        exit;
    }

    $reseniaNegocio = new ReseniaNegocio($conexion);
    $resultado = $reseniaNegocio->guardarResenia($idPropiedad, $idUsuario, $calificacion, $comentario);

    echo json_encode($resultado);
} else {
    echo json_encode(['ok' => false, 'error' => 'Método no permitido.']);
}
?>
