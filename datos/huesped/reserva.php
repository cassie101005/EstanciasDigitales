<?php
// Este archivo se encarga de extraer y validar los datos para una reservación de huésped

$idPropiedad = isset($_POST['idPropiedad']) ? intval($_POST['idPropiedad']) : 0;
$idUsuario = isset($_POST['idUsuario']) ? intval($_POST['idUsuario']) : 0;
$fechaInicio = isset($_POST['fechaInicio']) ? $_POST['fechaInicio'] : '';
$fechaFin = isset($_POST['fechaFin']) ? $_POST['fechaFin'] : '';
$total = isset($_POST['total']) ? floatval($_POST['total']) : 0;
$huespedes = isset($_POST['huespedes']) ? intval($_POST['huespedes']) : 1;

if ($idPropiedad == 0 || $idUsuario == 0 || empty($fechaInicio) || empty($fechaFin)) {
    echo json_encode(['ok' => false, 'mensaje' => 'Datos incompletos']);
    exit();
}
?>
