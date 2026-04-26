<?php
// Este archivo se encarga de extraer y validar los datos para un comentario de huésped

$idReserva = isset($_POST['idReserva']) ? intval($_POST['idReserva']) : 0;
$idPropiedad = isset($_POST['idPropiedad']) ? intval($_POST['idPropiedad']) : 0;
$comentario = isset($_POST['comentario']) ? trim($_POST['comentario']) : '';
$calificacion = isset($_POST['calificacion']) ? intval($_POST['calificacion']) : 5;

if ($idReserva <= 0 || $idPropiedad <= 0 || empty($comentario)) {
    echo json_encode(['ok' => false, 'mensaje' => 'Datos incompletos']);
    exit;
}
?>
