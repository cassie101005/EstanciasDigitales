<?php
require_once '../../datos/conexion.php';

// Protección
if (!isset($idPropiedad) || !isset($idUsuario) || !isset($fechaInicio) || !isset($fechaFin)) {
    $resultado = ['ok' => false, 'mensaje' => 'Acceso no permitido'];
    return;
}

// 1. Validar solapamiento en el backend para seguridad
$sqlCheck = "SELECT idReserva FROM tbl_reserva 
             WHERE idPropiedad = ? 
             AND vEstatus NOT IN ('Cancelada')
             AND (
                 (dtFechaInicio < ? AND dtFechaFin > ?)
             )";
$stmtCheck = $conexion->prepare($sqlCheck);
$stmtCheck->bind_param("iss", $idPropiedad, $fechaFin, $fechaInicio);
$stmtCheck->execute();
if ($stmtCheck->get_result()->num_rows > 0) {
    $resultado = ['ok' => false, 'mensaje' => 'Lo sentimos, estas fechas ya no están disponibles.'];
    return;
}

// 2. Insertar en tbl_reserva
$sql = "INSERT INTO tbl_reserva (idUsuario, idPropiedad, dtFechaInicio, dtFechaFin, dTotalReserva) 
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    $resultado = ['ok' => false, 'mensaje' => 'Error en la preparación: ' . $conexion->error];
    return;
}

$stmt->bind_param("iissd", $idUsuario, $idPropiedad, $fechaInicio, $fechaFin, $total);

if ($stmt->execute()) {
    $resultado = ['ok' => true, 'mensaje' => 'Reservación guardada con éxito'];
} else {
    $resultado = ['ok' => false, 'mensaje' => 'Error al guardar la reservación: ' . $stmt->error];
}
?>
