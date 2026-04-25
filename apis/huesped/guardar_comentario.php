<?php
header('Content-Type: application/json');
require_once '../../negocio/auth/verificar_sesion.php';
validarSesionAPI('huesped');
require_once '../../datos/conexion.php';

$idUsuario = $_SESSION['idUsuario'];
$idReserva = isset($_POST['idReserva']) ? intval($_POST['idReserva']) : 0;
$idPropiedad = isset($_POST['idPropiedad']) ? intval($_POST['idPropiedad']) : 0;
$comentario = isset($_POST['comentario']) ? trim($_POST['comentario']) : '';
$calificacion = isset($_POST['calificacion']) ? intval($_POST['calificacion']) : 5;

if ($idReserva <= 0 || $idPropiedad <= 0 || empty($comentario)) {
    echo json_encode(['ok' => false, 'mensaje' => 'Datos incompletos']);
    exit;
}

// Verificar si ya comentó para esta reserva
$check = $conexion->prepare("SELECT idComentario FROM tbl_comentarios WHERE idReserva = ?");
$check->bind_param("i", $idReserva);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    echo json_encode(['ok' => false, 'mensaje' => 'Ya has dejado un comentario para esta reserva']);
    exit;
}

$sql = "INSERT INTO tbl_comentarios (idReserva, idPropiedad, idUsuario, vComentario, iCalificacion) VALUES (?, ?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("iiisi", $idReserva, $idPropiedad, $idUsuario, $comentario, $calificacion);

if ($stmt->execute()) {
    echo json_encode(['ok' => true, 'mensaje' => 'Comentario guardado con éxito']);
} else {
    echo json_encode(['ok' => false, 'mensaje' => 'Error al guardar: ' . $conexion->error]);
}
?>
