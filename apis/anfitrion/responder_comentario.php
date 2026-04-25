<?php
session_start();
header('Content-Type: application/json');
require_once '../../datos/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
    exit;
}

if (!isset($_SESSION['idUsuario'])) {
    echo json_encode(['ok' => false, 'error' => 'Sesión no iniciada']);
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';
$respuesta = isset($_POST['respuesta']) ? trim($_POST['respuesta']) : '';

if ($id <= 0 || empty($tipo) || empty($respuesta)) {
    echo json_encode(['ok' => false, 'error' => 'Datos incompletos']);
    exit;
}

$tabla = ($tipo === 'comentario') ? 'tbl_comentarios' : 'tbl_resenia';
$colId = ($tipo === 'comentario') ? 'idComentario' : 'idResenia';

$sql = "UPDATE $tabla SET vRespuesta = ?, dtFechaRespuesta = NOW() WHERE $colId = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("si", $respuesta, $id);

if ($stmt->execute()) {
    echo json_encode(['ok' => true]);
} else {
    echo json_encode(['ok' => false, 'error' => 'Error al guardar la respuesta: ' . $conexion->error]);
}
?>
