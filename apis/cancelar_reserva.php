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

// 2. Intentar actualizar el estatus en tbl_reserva
if ($role === 'anfitrion') {
    $sql = "UPDATE tbl_reserva r 
            JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad 
            SET r.vEstatus = 'Cancelada' 
            WHERE r.idReserva = ? AND p.idUsuario = ?";
} else {
    // Quitar el alias 'r' para evitar errores de sintaxis en UPDATE de una sola tabla en MySQL
    $sql = "UPDATE tbl_reserva 
            SET vEstatus = 'Cancelada' 
            WHERE idReserva = ? AND idUsuario = ?";
}

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    // Intentar con vEstado en lugar de vEstatus por compatibilidad
    if ($role === 'anfitrion') {
        $sql = "UPDATE tbl_reserva r JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad SET r.vEstado = 'Cancelada' WHERE r.idReserva = ? AND p.idUsuario = ?";
    } else {
        $sql = "UPDATE tbl_reserva SET vEstado = 'Cancelada' WHERE idReserva = ? AND idUsuario = ?";
    }
    $stmt = $conexion->prepare($sql);
}

if (!$stmt) {
    echo json_encode(['ok' => false, 'mensaje' => 'Error en la consulta.']);
    exit();
}

$conexion->begin_transaction();

$stmt->bind_param("ii", $idReserva, $idUsuario);

if ($stmt->execute()) {
    // Aceptar >= 0 porque si ya estaba cancelada (affected_rows = 0), igual queremos intentar insertar en el historial
    if ($stmt->affected_rows >= 0) {
        // 3. Registrar la cancelación en tbl_cancelacion con las columnas correctas
        $sqlCancel = "INSERT INTO tbl_cancelacion (idReserva, vQuienCancelo, vMotivo) VALUES (?, ?, 'Cancelación solicitada desde panel de usuario')";
        $stmtCancel = $conexion->prepare($sqlCancel);
        if ($stmtCancel) {
            $stmtCancel->bind_param("is", $idReserva, $role);
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
