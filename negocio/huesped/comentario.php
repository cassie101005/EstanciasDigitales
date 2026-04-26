<?php
require_once '../../datos/conexion.php';

// Protección
if (!isset($idReserva) || !isset($idPropiedad) || !isset($idUsuario)) {
    $resultado = ['ok' => false, 'mensaje' => 'Acceso no permitido'];
    return;
}

// Verificar si ya comentó para esta reserva
$check = $conexion->prepare("SELECT idComentario FROM tbl_comentarios WHERE idReserva = ?");
$check->bind_param("i", $idReserva);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    $resultado = ['ok' => false, 'mensaje' => 'Ya has dejado un comentario para esta reserva'];
    return;
}

$sql = "INSERT INTO tbl_comentarios (idReserva, idPropiedad, idUsuario, vComentario, iCalificacion) VALUES (?, ?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("iiisi", $idReserva, $idPropiedad, $idUsuario, $comentario, $calificacion);

if ($stmt->execute()) {
    $resultado = ['ok' => true, 'mensaje' => 'Comentario guardado con éxito'];
} else {
    $resultado = ['ok' => false, 'mensaje' => 'Error al guardar el comentario.'];
}
?>
