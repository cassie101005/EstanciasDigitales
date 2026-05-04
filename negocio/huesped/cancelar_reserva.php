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
        
        // ── NOTIFICACIÓN AL HUÉSPED ──
        require_once '../../negocio/utilidades/notificaciones.php';
        $stmtProp = $conexion->prepare("SELECT p.vNombre, r.idPropiedad FROM tbl_reserva r JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad WHERE r.idReserva = ?");
        $stmtProp->bind_param("i", $idReserva);
        $stmtProp->execute();
        $pData = $stmtProp->get_result()->fetch_assoc();
        $nombreProp = $pData['vNombre'] ?? 'la propiedad';
        $idProp = $pData['idPropiedad'] ?? 0;

        $tituloNotif = "Solicitud de cancelación";
        $mensajeNotif = "Tu solicitud de cancelación para " . $nombreProp . " ha sido enviada correctamente.";
        $urlNotif = "presentacion/huesped/detalle_reserva.php?id=" . $idProp . "&id_reserva=" . $idReserva;
        
        registrarNotificacion($idUsuario, 'solicitud_cancelacion', $tituloNotif, $mensajeNotif, $urlNotif, $idReserva);

        // ── NOTIFICACIÓN AL ANFITRIÓN ──
        $sqlHost = "SELECT p.idUsuario as idAnfitrion, u.vNombre, u.vApellido 
                    FROM tbl_propiedad p 
                    JOIN tbl_usuarios u ON u.idUsuario = ? 
                    WHERE p.idPropiedad = ?";
        $stmtHost = $conexion->prepare($sqlHost);
        $stmtHost->bind_param("ii", $idUsuario, $idProp);
        $stmtHost->execute();
        $hostData = $stmtHost->get_result()->fetch_assoc();
        
        if ($hostData) {
            $idAnfitrion = $hostData['idAnfitrion'];
            $nombreHuesped = $hostData['vNombre'] . ' ' . $hostData['vApellido'];
            
            $tituloHost = "Solicitud de cancelación";
            $mensajeHost = $nombreHuesped . " ha solicitado cancelar su reserva en '" . $nombreProp . "'.";
            $urlHost = "presentacion/anfitrion/reservas.php";
            
            registrarNotificacion($idAnfitrion, 'reserva_cancelada', $tituloHost, $mensajeHost, $urlHost, $idReserva);
        }
    } else {
        $resultado = ['ok' => false, 'mensaje' => 'Error al solicitar cancelación.'];
    }
} else {
    $resultado = ['ok' => false, 'mensaje' => 'Error en la consulta de actualización.'];
}
?>
