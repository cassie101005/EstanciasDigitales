<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Evitar que errores PHP se filtren en la salida JSON

require_once '../../datos/conexion.php';
require_once '../../negocio/huesped/resenia.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['idUsuario'])) {
        echo json_encode(['ok' => false, 'error' => 'Debes iniciar sesión para comentar.']);
        exit;
    }

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
