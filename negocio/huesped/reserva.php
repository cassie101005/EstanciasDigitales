<?php
require_once '../../datos/conexion.php';

// Protección
if (!isset($idPropiedad) || !isset($idUsuario) || !isset($fechaInicio) || !isset($fechaFin)) {
    $resultado = ['ok' => false, 'mensaje' => 'Acceso no permitido'];
    return;
}

require_once '../../negocio/utilidades/seguridad.php';

// 0. Validar CSRF
if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    $resultado = ['ok' => false, 'mensaje' => 'Error de seguridad (CSRF). Por favor, recarga la página e intenta de nuevo.'];
    return;
}

// 1. Validar disponibilidad real en BD (Doble verificación)
if (!validarDisponibilidad($idPropiedad, $fechaInicio, $fechaFin, $conexion)) {
    http_response_code(400);
    $resultado = ['ok' => false, 'mensaje' => 'Lo sentimos, estas fechas ya no están disponibles. Alguien más pudo haber reservado mientras realizabas el pago.'];
    return;
}

// 1.1 Validar capacidad de huéspedes
$stmtCap = $conexion->prepare("SELECT iCapacidadHuespedes FROM tbl_propiedad WHERE idPropiedad = ?");
$stmtCap->bind_param("i", $idPropiedad);
$stmtCap->execute();
$resCap = $stmtCap->get_result()->fetch_assoc();
$capacidadMax = $resCap['iCapacidadHuespedes'] ?? 0;

if ($huespedes > $capacidadMax) {
    http_response_code(400);
    $resultado = ['ok' => false, 'mensaje' => 'La cantidad de huéspedes (' . $huespedes . ') excede la capacidad máxima de la propiedad (' . $capacidadMax . ').'];
    return;
}

// 2. Validar fechas pasadas y coherencia
$hoy = new DateTime();
$hoy->setTime(0, 0, 0); // Ignorar la hora para comparar solo fechas
$inicioObj = DateTime::createFromFormat('Y-m-d', $fechaInicio);
$finObj = DateTime::createFromFormat('Y-m-d', $fechaFin);

if (!$inicioObj || !$finObj) {
    http_response_code(400);
    $resultado = ['ok' => false, 'mensaje' => 'Formato de fecha inválido.'];
    return;
}
$inicioObj->setTime(0, 0, 0);
$finObj->setTime(0, 0, 0);

if ($inicioObj < $hoy) {
    http_response_code(400);
    $resultado = ['ok' => false, 'mensaje' => 'No puedes realizar reservas con fechas de llegada en el pasado.'];
    return;
}

$fechaMaxima = clone $hoy;
$fechaMaxima->modify('+1 year');

if ($inicioObj > $fechaMaxima || $finObj > $fechaMaxima) {
    http_response_code(400);
    $resultado = ['ok' => false, 'mensaje' => 'Solo puedes realizar reservas desde la fecha actual hasta máximo 1 año en el futuro.'];
    return;
}

if ($finObj <= $inicioObj) {
    http_response_code(400);
    $resultado = ['ok' => false, 'mensaje' => 'La fecha de salida debe ser posterior a la fecha de llegada.'];
    return;
}

// 3. Recalcular precio real desde BD (No confiar en el precio enviado por el cliente)
$calculo = calcularPrecioEstancia($idPropiedad, $fechaInicio, $fechaFin, $conexion);
$totalReal = $calculo['granTotal'];

// 3.1 Comparar monto enviado con monto calculado (Seguridad de integridad de pago)
$montoEnviado = floatval($_POST['montoTotal'] ?? 0);
if (abs($totalReal - $montoEnviado) > 0.01) {
    http_response_code(400);
    $resultado = ['ok' => false, 'mensaje' => 'Se detectó una discrepancia en el monto total. Por favor, intenta realizar la reserva de nuevo.'];
    return;
}

// 4. Insertar en tbl_reserva (Guardando snapshot de precios)
// idEstadoReserva = 1 => Confirmada; dtFechaRegistro = NOW() para el cálculo de 24h en cancelaciones
$sql = "INSERT INTO tbl_reserva (idUsuario, idPropiedad, dtFechaInicio, dtFechaFin, iCantidadHuespedes, dSubtotalReserva, dTotalReserva, dPrecioNocheReserva, dLimpiezaReserva, dImpuestosReserva, tDesgloseNoches, idEstadoReserva, vEstatus, dtFechaRegistro) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 'Confirmada', NOW())";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    $resultado = ['ok' => false, 'mensaje' => 'Error en la preparación: ' . $conexion->error];
    return;
}

$subtotal    = $calculo['totalBase'];
$precioNoche = $calculo['precioPromedio'];
$limpieza    = $calculo['limpieza'];
$impuestos   = $calculo['impuestos'];
$desgloseJson = json_encode($calculo['desgloseNoches']);

// Tipos: i=idUsuario, i=idPropiedad, s=fechaInicio, s=fechaFin,
//        i=huespedes, d=subtotal, d=totalReal, d=precioNoche,
//        d=limpieza, d=impuestos, s=desgloseJson  → 11 params
$stmt->bind_param("iissiddddds", 
    $idUsuario, 
    $idPropiedad, 
    $fechaInicio, 
    $fechaFin,
    $huespedes,
    $subtotal,
    $totalReal, 
    $precioNoche, 
    $limpieza, 
    $impuestos,
    $desgloseJson
);

if ($stmt->execute()) {
    $idReserva = $conexion->insert_id;
    $resultado = ['ok' => true, 'mensaje' => 'Reservación confirmada con éxito'];

    // ── NOTIFICACIÓN AL HUÉSPED ──
    require_once '../../negocio/utilidades/notificaciones.php';
    
    // Obtener nombre de la propiedad
    $stmtProp = $conexion->prepare("SELECT vNombre FROM tbl_propiedad WHERE idPropiedad = ?");
    $stmtProp->bind_param("i", $idPropiedad);
    $stmtProp->execute();
    $propData = $stmtProp->get_result()->fetch_assoc();
    $nombreProp = $propData['vNombre'] ?? 'la propiedad';

    $tituloNotif = "¡Reserva Confirmada!";
    $mensajeNotif = "Tu reservación en " . $nombreProp . " ha sido confirmada. ¡Prepárate para tu viaje!";
    $urlNotif = "presentacion/huesped/detalle_reserva.php?id=" . $idPropiedad . "&id_reserva=" . $idReserva;
    
    registrarNotificacion($idUsuario, 'reserva', $tituloNotif, $mensajeNotif, $urlNotif, $idReserva);

    // ── NOTIFICACIÓN AL ANFITRIÓN ──
    // Obtener id del anfitrión y nombre del huésped
    $sqlHost = "SELECT p.idUsuario as idAnfitrion, u.vNombre, u.vApellido 
                FROM tbl_propiedad p 
                JOIN tbl_usuarios u ON u.idUsuario = ? 
                WHERE p.idPropiedad = ?";
    $stmtHost = $conexion->prepare($sqlHost);
    $stmtHost->bind_param("ii", $idUsuario, $idPropiedad);
    $stmtHost->execute();
    $hostData = $stmtHost->get_result()->fetch_assoc();
    
    if ($hostData) {
        $idAnfitrion = $hostData['idAnfitrion'];
        $nombreHuesped = $hostData['vNombre'] . ' ' . $hostData['vApellido'];
        
        $tituloHost = "Nueva reserva recibida";
        $mensajeHost = $nombreHuesped . " ha reservado '" . $nombreProp . "'.";
        $urlHost = "presentacion/anfitrion/reservas.php"; 
        
        registrarNotificacion($idAnfitrion, 'reserva_nueva', $tituloHost, $mensajeHost, $urlHost, $idReserva);

        // Notificación de Pago Confirmado
        $tituloPago = "Pago confirmado";
        $mensajePago = "Se ha confirmado el pago de " . $nombreHuesped . " por su estancia en '" . $nombreProp . "'.";
        registrarNotificacion($idAnfitrion, 'pago_confirmado', $tituloPago, $mensajePago, $urlHost, $idReserva);
    }
} else {
    $resultado = ['ok' => false, 'mensaje' => 'Error al guardar la reservación: ' . $stmt->error];
}
?>
