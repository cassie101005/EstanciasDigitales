<?php
require_once '../../datos/conexion.php';

require_once '../../negocio/utilidades/seguridad.php';

// Validar CSRF
if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    $resultado = ['ok' => false, 'mensaje' => 'Error de seguridad (CSRF).'];
    return;
}

// Protección
if (!isset($idReserva) || !isset($idUsuario)) {
    $resultado = ['ok' => false, 'mensaje' => 'Acceso no permitido'];
    return;
}

// 1. Obtener detalles de la reserva para calcular penalización y reembolso
$sqlInfo = "SELECT dtFechaInicio, dTotalReserva, dtFechaRegistro 
            FROM tbl_reserva 
            WHERE idReserva = ?";

$stmtInfo = $conexion->prepare($sqlInfo);

if (!$stmtInfo) {
    $resultado = [
        'ok' => false,
        'mensaje' => 'Error en la consulta de información: ' . $conexion->error
    ];
    return;
}

$stmtInfo->bind_param("i", $idReserva);
$stmtInfo->execute();
$resInfo = $stmtInfo->get_result()->fetch_assoc();

if (!$resInfo) {
    $resultado = [
        'ok' => false,
        'mensaje' => 'No se encontró la información de la reserva'
    ];
    return;
}

$fechaInicio = $resInfo['dtFechaInicio'];
$fechaRegistro = $resInfo['dtFechaRegistro'];
$total = floatval($resInfo['dTotalReserva']);

$inicioTimestamp = strtotime($fechaInicio);
$registroTimestamp = strtotime($fechaRegistro);
$ahora = time();

if (!$inicioTimestamp || !$registroTimestamp) {
    $resultado = [
        'ok' => false,
        'mensaje' => 'La fecha de la reserva no es válida'
    ];
    return;
}

// Calcular cuántas horas faltan para que inicie la reserva
$horasParaInicio = ($inicioTimestamp - $ahora) / 3600;

// Validar regla principal:
// Las cancelaciones solo podrán realizarse antes de la fecha de inicio de la reserva
if ($horasParaInicio <= 0) {
    $resultado = [
        'ok' => false,
        'mensaje' => 'No se puede cancelar una reserva que ya inició o que ya pasó.'
    ];
    return;
}

// Calcular cuántas horas han pasado desde que se hizo la reserva
$horasDesdeRegistro = ($ahora - $registroTimestamp) / 3600;

$penalizacion = 0;
$reembolso = $total;
$tipoCancelacion = 'Reembolso Completo';

// Si ya pasaron 24 horas desde que se registró la reserva, se cobra penalización
if ($horasDesdeRegistro >= 24) {
    $penalizacion = $total * 0.10;
    $reembolso = $total * 0.90;
    $tipoCancelacion = 'Con Penalización (10%)';
}

// 2. Actualizar el estado de la reserva
$sql = "UPDATE tbl_reserva r 
        INNER JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad 
        SET r.vEstatus = 'Cancelada', 
            r.idEstadoReserva = 4
        WHERE r.idReserva = ? 
        AND p.idUsuario = ?
        AND r.idEstadoReserva != 4";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    $resultado = [
        'ok' => false,
        'mensaje' => 'Error en la consulta de actualización: ' . $conexion->error
    ];
    return;
}

$conexion->begin_transaction();

$stmt->bind_param("ii", $idReserva, $idUsuario);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {

        // 3. Registrar la cancelación en tbl_cancelacion
        $sqlCancel = "INSERT INTO tbl_cancelacion 
                        (idReserva, vQuienCancelo, vTipoCancelacion, vMotivo, dPenalizacion, dReembolso) 
                      VALUES 
                        (?, 'anfitrion', ?, ?, ?, ?)";

        $stmtCancel = $conexion->prepare($sqlCancel);

        if ($stmtCancel) {
            $stmtCancel->bind_param(
                "issdd",
                $idReserva,
                $tipoCancelacion,
                $motivo,
                $penalizacion,
                $reembolso
            );

            if ($stmtCancel->execute()) {

                // 4. Notificación al huésped
                require_once '../../negocio/utilidades/notificaciones.php';

                $sqlDetalles = "SELECT r.idUsuario AS idHuesped, 
                                       p.vNombre AS nombreProp, 
                                       r.idPropiedad
                                FROM tbl_reserva r 
                                INNER JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad 
                                WHERE r.idReserva = ?";

                $stmtDetalles = $conexion->prepare($sqlDetalles);

                if ($stmtDetalles) {
                    $stmtDetalles->bind_param("i", $idReserva);
                    $stmtDetalles->execute();
                    $datosRes = $stmtDetalles->get_result()->fetch_assoc();

                    if ($datosRes) {
                        $idHuesped = $datosRes['idHuesped'];
                        $nombreProp = $datosRes['nombreProp'];
                        $idProp = $datosRes['idPropiedad'];

                        $tituloNotif = "Reserva cancelada por anfitrión";
                        $mensajeNotif = "Tu reserva en " . $nombreProp . " fue cancelada. Reembolso aplicado: $" . number_format($reembolso, 2);
                        $urlNotif = "presentacion/huesped/detalle_reserva.php?id=" . $idProp . "&id_reserva=" . $idReserva;

                        registrarNotificacion(
                            $idHuesped,
                            'reserva_cancelada',
                            $tituloNotif,
                            $mensajeNotif,
                            $urlNotif,
                            $idReserva
                        );
                    }
                }

                $conexion->commit();

                $msg = "Reserva cancelada correctamente. ";

                if ($horasDesdeRegistro < 24) {
                    $msg .= "Se procesará un reembolso total de $" . number_format($reembolso, 2);
                } else {
                    $msg .= "Se aplicó una penalización del 10%. Reembolso de $" . number_format($reembolso, 2);
                }

                $resultado = [
                    'ok' => true,
                    'mensaje' => $msg
                ];

            } else {
                $conexion->rollback();

                $resultado = [
                    'ok' => false,
                    'mensaje' => 'No se pudo registrar la cancelación en el historial.'
                ];
            }

        } else {
            $conexion->rollback();

            $resultado = [
                'ok' => false,
                'mensaje' => 'Error preparando el registro de cancelación.'
            ];
        }

    } else {
        $conexion->rollback();

        $resultado = [
            'ok' => false,
            'mensaje' => 'No se encontró la reserva, no pertenece al anfitrión o ya estaba cancelada.'
        ];
    }

} else {
    $conexion->rollback();

    $resultado = [
        'ok' => false,
        'mensaje' => 'Error al ejecutar la cancelación en la base de datos'
    ];
}
?>