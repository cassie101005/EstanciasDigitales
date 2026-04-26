<?php
require_once '../../datos/conexion.php';

// Protección
if (!isset($idReserva) || !isset($idUsuario)) {
    $resultado = ['ok' => false, 'mensaje' => 'Acceso no permitido'];
    return;
}

// 1. Obtener detalles de la reserva para calcular penalización y reembolso
$sqlInfo = "SELECT dtFechaInicio, dTotalReserva FROM tbl_reserva WHERE idReserva = ?";
$stmtInfo = $conexion->prepare($sqlInfo);
$stmtInfo->bind_param("i", $idReserva);
$stmtInfo->execute();
$resInfo = $stmtInfo->get_result()->fetch_assoc();

if (!$resInfo) {
    $resultado = ['ok' => false, 'mensaje' => 'No se encontró la información de la reserva'];
    return;
}

$fechaInicio = $resInfo['dtFechaInicio'];
$total = floatval($resInfo['dTotalReserva']);
$inicioTimestamp = strtotime($fechaInicio . ' 15:00:00'); // Check-in standard 3 PM
$ahora = time();
$horasParaInicio = ($inicioTimestamp - $ahora) / 3600;

$penalizacion = 0;
$reembolso = $total;
$tipoCancelacion = 'Reembolso Completo';

if ($horasParaInicio < 24) {
    $penalizacion = $total * 0.10;
    $reembolso = $total * 0.90;
    $tipoCancelacion = 'Con Penalización (10%)';
}

// 2. Actualizar el estado de la reserva
$sql = "UPDATE tbl_reserva r 
        JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad 
        SET r.vEstatus = 'Cancelada' 
        WHERE r.idReserva = ? AND p.idUsuario = ?";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    $sql = "UPDATE tbl_reserva r JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad SET r.vEstado = 'Cancelada' WHERE r.idReserva = ? AND p.idUsuario = ?";
    $stmt = $conexion->prepare($sql);
}

if (!$stmt) {
    $resultado = ['ok' => false, 'mensaje' => 'Error en la consulta de actualización.'];
    return;
}

$conexion->begin_transaction();
$stmt->bind_param("ii", $idReserva, $idUsuario);

if ($stmt->execute()) {
    if ($stmt->affected_rows >= 0) {
        // 3. Registrar la cancelación en tbl_cancelacion
        $sqlCancel = "INSERT INTO tbl_cancelacion (idReserva, vQuienCancelo, vTipoCancelacion, vMotivo, dPenalizacion, dReembolso) VALUES (?, 'anfitrion', ?, ?, ?, ?)";
        $stmtCancel = $conexion->prepare($sqlCancel);
        if ($stmtCancel) {
            $stmtCancel->bind_param("issdd", $idReserva, $tipoCancelacion, $motivo, $penalizacion, $reembolso);
            if ($stmtCancel->execute()) {
                $conexion->commit();
                $msg = "Reserva cancelada correctamente. ";
                $msg .= ($horasParaInicio >= 24) ? "Se procesará un reembolso total de $" . number_format($reembolso, 2) : "Se aplicó una penalización del 10%. Reembolso de $" . number_format($reembolso, 2);
                $resultado = ['ok' => true, 'mensaje' => $msg];
            } else {
                $conexion->rollback();
                $resultado = ['ok' => false, 'mensaje' => 'No se pudo registrar la cancelación en el historial.'];
            }
        } else {
            $conexion->rollback();
            $resultado = ['ok' => false, 'mensaje' => 'Error preparando el registro de cancelación.'];
        }
    } else {
        $conexion->rollback();
        $resultado = ['ok' => false, 'mensaje' => 'No se encontró la reserva o ya estaba cancelada.'];
    }
} else {
    $conexion->rollback();
    $resultado = ['ok' => false, 'mensaje' => 'Error al ejecutar la cancelación en la base de datos'];
}
?>
