<?php
ob_start();
header('Content-Type: application/json');
require_once '../../negocio/auth/verificar_sesion.php';
validarSesionAPI('huesped');
require_once '../../datos/conexion.php';
require_once '../../negocio/huesped/resenia.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        require_once '../../negocio/utilidades/seguridad.php';
        if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
            if (ob_get_length()) ob_end_clean();
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'Error de seguridad (CSRF).']);
            exit;
        }

        $idUsuario = intval($_SESSION['idUsuario']);
        $reseniaNegocio = new ReseniaNegocio($conexion);

        $idResenia = isset($_POST['idResenia']) ? intval($_POST['idResenia']) : 0;
        
        if ($idResenia > 0) {
            $comentario = isset($_POST['vComentario']) ? $_POST['vComentario'] : '';
            $resultado = $reseniaNegocio->actualizarResenia($idResenia, $idUsuario, $comentario);
        } else {
            require_once '../../datos/huesped/resenia.php';
            $resultado = $reseniaNegocio->guardarResenia($idPropiedad, $idUsuario, $calificacion, $comentario);
        }

        if (ob_get_length()) ob_end_clean();
        echo json_encode($resultado);
        exit;

    } catch (Exception $e) {
        if (ob_get_length()) ob_end_clean();
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'Error al procesar la reseña: ' . $e->getMessage()]);
        exit;
    } catch (Error $e) {
        if (ob_get_length()) ob_end_clean();
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'Error crítico en el servidor.']);
        exit;
    }
} else {
    if (ob_get_length()) ob_end_clean();
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido.']);
}
?>
