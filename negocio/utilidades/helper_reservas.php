<?php
/**
 * helper_reservas.php
 * Utilidades para el manejo de estados de reservaciones
 */

/**
 * Calcula el estado dinámico de una reserva basado en fechas y estatus de BD
 * @param array $reserva Fila de la tabla tbl_reserva
 * @return array [status, color, bgColor, label]
 */
function obtenerEstadoReserva($reserva) {
    // Asegurar zona horaria correcta para México
    date_default_timezone_set('America/Mexico_City');
    
    $fechaInicio = new DateTime($reserva['dtFechaInicio']);
    $fechaFin    = new DateTime($reserva['dtFechaFin']);
    $hoy         = new DateTime(date('Y-m-d'));
    
    // Estatus base de la BD
    $estatusBD = $reserva['vEstatus'] ?? 'Confirmada';
    
    $status = "Confirmada";
    $color  = "#3b82f6"; // Blue
    $bgColor = "#eff6ff";

    if (strtolower($estatusBD) === 'cancelada') {
        $status = "Cancelada";
        $color  = "#991b1b"; // Red
        $bgColor = "#fee2e2";
    } else if (strtolower($estatusBD) === 'pendiente cancelacion') {
        $status = "Pendiente Cancelación";
        $color  = "#92400e"; // Yellow/Brown
        $bgColor = "#fef3c7";
    } else if ($fechaFin < $hoy) {
        $status = "Finalizada";
        $color  = "#64748b"; // Gray
        $bgColor = "#f8fafc";
    } else if ($hoy >= $fechaInicio && $hoy <= $fechaFin) {
        $status = "En curso";
        $color  = "#059669"; // Green
        $bgColor = "#ecfdf5";
    }

    return [
        'label'        => $status,
        'color'        => $color,
        'bgColor'      => $bgColor,
        'noches'       => $fechaInicio->diff($fechaFin)->days,
        'fechaInicio'  => $fechaInicio,
        'fechaFin'     => $fechaFin
    ];
}

/**
 * Devuelve el HTML del badge de estado con clases semánticas
 */
function renderizarBadgeEstado($reserva) {
    $info = obtenerEstadoReserva($reserva);

    // Mapa de estado → clase CSS modificadora
    $classMap = [
        'Confirmada'            => 'confirmada',
        'Cancelada'             => 'cancelada',
        'Finalizada'            => 'finalizada',
        'En curso'              => 'en-curso',
        'Pendiente Cancelación' => 'pendiente-cancelacion',
    ];
 
    // Mapa de estado → ícono FontAwesome
    $iconMap = [
        'Confirmada'            => 'fa-circle-check',
        'Cancelada'             => 'fa-circle-xmark',
        'Finalizada'            => 'fa-circle-dot',
        'En curso'              => 'fa-circle-play',
        'Pendiente Cancelación' => 'fa-triangle-exclamation',
    ];

    $statusClass = $classMap[$info['label']] ?? 'confirmada';
    $icon        = $iconMap[$info['label']] ?? 'fa-circle';

    return '<span class="reserva-status-badge ' . $statusClass . '">'
         . '<i class="fa-solid ' . $icon . '"></i>'
         . htmlspecialchars($info['label'])
         . '</span>';
}
