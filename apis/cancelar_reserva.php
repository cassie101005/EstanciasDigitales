<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
require_once '../datos/conexion.php';

$idReserva = isset($_POST['idReserva']) ? intval($_POST['idReserva']) : 0;
$role = isset($_POST['role']) ? $_POST['role'] : '';
$idUsuario = isset($_POST['idUsuario']) ? intval($_POST['idUsuario']) : 0;

if ($idReserva <= 0 || $idUsuario <= 0) {
    echo json_encode(['ok' => false, 'mensaje' => 'Datos inválidos']);
    exit();
}

// 2. Ejecutar la acción según el rol
if ($role === 'huesped') {
    // El huésped solo solicita la cancelación
    $motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : 'Sin motivo especificado';
    $motivoFormateado = "Motivo de cancelación: " . $motivo;
    
    // Usamos UPDATE tbl_reserva
    $sql = "UPDATE tbl_reserva SET vEstatus = 'Pendiente Cancelacion', vObservaciones = ? WHERE idReserva = ? AND idUsuario = ?";
    $stmt = $conexion->prepare($sql);
    
    if (!$stmt) {
        $sql = "UPDATE tbl_reserva SET vEstado = 'Pendiente Cancelacion', vObservaciones = ? WHERE idReserva = ? AND idUsuario = ?";
        $stmt = $conexion->prepare($sql);
    }
    
    if ($stmt) {
        $stmt->bind_param("sii", $motivoFormateado, $idReserva, $idUsuario);
        if ($stmt->execute()) {
            echo json_encode(['ok' => true, 'mensaje' => 'Solicitud de cancelación enviada al anfitrión.']);
        } else {
            echo json_encode(['ok' => false, 'mensaje' => 'Error al solicitar cancelación.']);
        }
    } else {
        echo json_encode(['ok' => false, 'mensaje' => 'Error en la consulta.']);
    }
    exit();
}

// Lógica para el anfitrión (cancela directamente)
// 1. Obtener detalles de la reserva para calcular el reembolso
$sqlInfo = "SELECT dtFechaInicio, dTotalReserva FROM tbl_reserva WHERE idReserva = ?";
$stmtInfo = $conexion->prepare($sqlInfo);
$stmtInfo->bind_param("i", $idReserva);
$stmtInfo->execute();
$resInfo = $stmtInfo->get_result()->fetch_assoc();

if (!$resInfo) {
    echo json_encode(['ok' => false, 'mensaje' => 'No se encontró la información de la reserva']);
    exit();
}

$fechaInicio = $resInfo['dtFechaInicio'];
$total = floatval($resInfo['dTotalReserva']);
// Calcular cuántas horas faltan para el check-in (asumiendo las 3:00 PM / 15:00)
$inicioTimestamp = strtotime($fechaInicio . ' 15:00:00');
$ahora = time();
$horasParaInicio = ($inicioTimestamp - $ahora) / 3600;

$penalizacion = 0;
$reembolso = $total;
$tipoCancelacion = 'Reembolso Completo';

if ($horasParaInicio < 24) {
    // Si falta menos de 24 horas para el check-in o ya pasó
    $penalizacion = $total * 0.10;
    $reembolso = $total * 0.90;
    $tipoCancelacion = 'Con Penalización (10%)';
}

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
    echo json_encode(['ok' => false, 'mensaje' => 'Error en la consulta.']);
    exit();
}

$conexion->begin_transaction();
$stmt->bind_param("ii", $idReserva, $idUsuario);

if ($stmt->execute()) {
    if ($stmt->affected_rows >= 0) {
        $motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : 'Sin motivo especificado';
        $sqlCancel = "INSERT INTO tbl_cancelacion (idReserva, vQuienCancelo, vTipoCancelacion, vMotivo, dPenalizacion, dReembolso) VALUES (?, ?, ?, ?, ?, ?)";
        $stmtCancel = $conexion->prepare($sqlCancel);
        if ($stmtCancel) {
            $stmtCancel->bind_param("isssdd", $idReserva, $role, $tipoCancelacion, $motivo, $penalizacion, $reembolso);
            if ($stmtCancel->execute()) {
                $conexion->commit();
                $msg = "Reserva cancelada correctamente. ";
                $msg .= ($horasParaInicio >= 24) ? "Se procesará un reembolso total de $" . number_format($reembolso, 2) : "Se aplicó una penalización del 10%. Reembolso de $" . number_format($reembolso, 2) . " (La comisión de $" . number_format($penalizacion, 2) . " se reflejará al anfitrión)";
                echo json_encode(['ok' => true, 'mensaje' => $msg]);
            } else {
                $conexion->rollback();
                echo json_encode(['ok' => false, 'mensaje' => 'No se pudo registrar la cancelación en el historial.']);
            }
        } else {
            $conexion->rollback();
            echo json_encode(['ok' => false, 'mensaje' => 'Error preparando el registro de cancelación.']);
        }
    } else {
        $conexion->rollback();
        echo json_encode(['ok' => false, 'mensaje' => 'No se encontró la reserva o ya estaba cancelada.']);
    }
} else {
    $conexion->rollback();
    echo json_encode(['ok' => false, 'mensaje' => 'Error al ejecutar la cancelación en la base de datos']);
}

?>
