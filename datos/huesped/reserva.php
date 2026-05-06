<?php
// Este archivo se encarga de extraer y validar los datos para una reservación de huésped

$idPropiedad = isset($_POST['idPropiedad']) ? intval($_POST['idPropiedad']) : 0;
$idUsuario = isset($_POST['idUsuario']) ? intval($_POST['idUsuario']) : 0;
$fechaInicio = isset($_POST['fechaInicio']) ? $_POST['fechaInicio'] : '';
$fechaFin = isset($_POST['fechaFin']) ? $_POST['fechaFin'] : '';
$total = isset($_POST['total']) ? floatval($_POST['total']) : 0;
$huespedes = isset($_POST['huespedes']) ? intval($_POST['huespedes']) : 1;

$fechaInicio = trim($_POST['fechaInicio'] ?? '');
$fechaFin    = trim($_POST['fechaFin']    ?? '');

if ($idPropiedad == 0 || $idUsuario == 0 || empty($fechaInicio) || empty($fechaFin)) {
    echo json_encode(['ok' => false, 'mensaje' => 'Datos incompletos']);
    exit();
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaInicio) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaFin)) {
    echo json_encode(['ok' => false, 'mensaje' => 'Formato de fecha inválido.']);
    exit();
}

$hoy = date('Y-m-d');
$fechaMaxima = date('Y-m-d', strtotime('+1 year'));

if ($fechaInicio < $hoy) {
    echo json_encode(['ok' => false, 'mensaje' => 'No se permiten fechas pasadas.']);
    exit();
}

if ($fechaInicio > $fechaMaxima || $fechaFin > $fechaMaxima) {
    echo json_encode(['ok' => false, 'mensaje' => 'Solo puedes realizar reservas desde la fecha actual hasta máximo 1 año en el futuro.']);
    exit();
}

if ($fechaInicio >= $fechaFin) {
    echo json_encode(['ok' => false, 'mensaje' => 'La fecha de inicio debe ser menor a la fecha de fin.']);
    exit();
}
?>
