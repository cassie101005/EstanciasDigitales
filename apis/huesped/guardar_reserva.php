<?php
header('Content-Type: application/json');
require_once '../../negocio/auth/verificar_sesion.php';
validarSesionAPI('huesped');
require_once '../../datos/conexion.php';

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

// NUEVO: Validar solapamiento en el backend para seguridad
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
    echo json_encode(['ok' => false, 'mensaje' => 'Lo sentimos, estas fechas ya no están disponibles.']);
    exit();
}

// Insertar en tbl_reserva
// Nota: Ajustar los nombres de las columnas según la estructura real de la tabla
$sql = "INSERT INTO tbl_reserva (idUsuario, idPropiedad, dtFechaInicio, dtFechaFin, dTotalReserva) 
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    echo json_encode(['ok' => false, 'mensaje' => 'Error en la preparación: ' . $conexion->error]);
    exit();
}

$stmt->bind_param("iissd", $idUsuario, $idPropiedad, $fechaInicio, $fechaFin, $total);

if ($stmt->execute()) {
    echo json_encode(['ok' => true, 'mensaje' => 'Reservación guardada con éxito']);
} else {
    echo json_encode(['ok' => false, 'mensaje' => 'Error al guardar la reservación: ' . $stmt->error]);
}
?>
