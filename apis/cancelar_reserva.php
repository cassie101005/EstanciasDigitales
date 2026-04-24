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
        $sqlCancel = "INSERT INTO tbl_cancelacion (idReserva, vQuienCancelo, vTipoCancelacion, vMotivo) VALUES (?, ?, 'Cancelado desde panel', ?)";
        $stmtCancel = $conexion->prepare($sqlCancel);
        if ($stmtCancel) {
            $stmtCancel->bind_param("iss", $idReserva, $role, $motivo);
            if ($stmtCancel->execute()) {
                $conexion->commit();
                echo json_encode(['ok' => true, 'mensaje' => 'Reserva cancelada correctamente y registrada en cancelaciones']);
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
