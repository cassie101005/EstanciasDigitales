<?php
require_once '../../datos/conexion.php';

// Protección
if (!isset($id) || !isset($tipo) || !isset($respuesta)) {
    $resultado = ['ok' => false, 'error' => 'Acceso no permitido'];
    return;
}

$tabla = ($tipo === 'comentario') ? 'tbl_comentarios' : 'tbl_resenia';
$colId = ($tipo === 'comentario') ? 'idComentario' : 'idResenia';

$sql = "UPDATE $tabla SET vRespuesta = ?, dtFechaRespuesta = NOW() WHERE $colId = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("si", $respuesta, $id);

if ($stmt->execute()) {
    $resultado = ['ok' => true];
} else {
    $resultado = ['ok' => false, 'error' => 'Error al guardar la respuesta.'];
}
?>
