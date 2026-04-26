<?php
require_once '../../datos/conexion.php';

// Protección
if (!isset($idReserva) || !isset($idUsuario)) {
    $resultado = ['ok' => false, 'mensaje' => 'Acceso no permitido'];
    return;
}

$motivoFormateado = "Motivo de cancelación: " . ($motivo ?? 'Sin motivo especificado');

// El huésped solo solicita la cancelación (cambia a 'Pendiente Cancelacion')
$sql = "UPDATE tbl_reserva SET vEstatus = 'Pendiente Cancelacion', vObservaciones = ? WHERE idReserva = ? AND idUsuario = ?";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    // Fallback por si la columna se llama vEstado
    $sql = "UPDATE tbl_reserva SET vEstado = 'Pendiente Cancelacion', vObservaciones = ? WHERE idReserva = ? AND idUsuario = ?";
    $stmt = $conexion->prepare($sql);
}

if ($stmt) {
    $stmt->bind_param("sii", $motivoFormateado, $idReserva, $idUsuario);
    if ($stmt->execute()) {
        $resultado = ['ok' => true, 'mensaje' => 'Solicitud de cancelación enviada al anfitrión.'];
    } else {
        $resultado = ['ok' => false, 'mensaje' => 'Error al solicitar cancelación.'];
    }
} else {
    $resultado = ['ok' => false, 'mensaje' => 'Error en la consulta de actualización.'];
}
?>
