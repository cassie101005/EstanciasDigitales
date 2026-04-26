<?php
require_once '../../negocio/auth/verificar_sesion.php';
validarSesion('huesped', '../../');
require_once '../../datos/conexion.php';

// Obtener ID de la reserva y propiedad
$idReserva = isset($_GET['id_reserva']) ? intval($_GET['id_reserva']) : 0;
$idPropiedad = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($idReserva <= 0 || $idPropiedad <= 0) {
    header("Location: reservas.php");
    exit();
}

$userId = $_SESSION['idUsuario'] ?? 2;

require_once '../../negocio/huesped/detalle_reserva_view.php';

// 1. Consultar detalles de la reservación
$reserva = getReservationDetails($idReserva, $userId, $conexion);

if (!$reserva) {
    header("Location: reservas.php");
    exit();
}

// 2. Consultar imágenes de la propiedad
$mainImage = getReservationMainImage($idPropiedad, $conexion);

// 3. Formatear fechas y calcular estado
$statusData = calculateReservationStatus($reserva);
$noches = $statusData['noches'];
$status = $statusData['status'];
$color = $statusData['color'];
$bgColor = $statusData['bgColor'];
$fechaInicio = $statusData['fechaInicio'];
$fechaFin = $statusData['fechaFin'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reserva #<?php echo $idReserva; ?> | Estancias Digitales</title>
    <link rel="stylesheet" href="../../recursos/css/variables.css">
    <link rel="stylesheet" href="../../recursos/css/main.css">
    <link rel="stylesheet" href="../../recursos/css/components/navbar.css">
    <link rel="stylesheet" href="../../recursos/css/huesped/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../recursos/css/huesped/detalle_reserva.css">
</head>
<body class="body-surface">
    <?php include '../../recursos/navbar.php'; ?>

    <div class="container main-container">
        
        <div class="reserva-detail-header header-column">
            <a href="reservas.php" class="back-link">
                <i class="fa-solid fa-arrow-left"></i> Volver a mis reservaciones
            </a>
            <div class="header-flex-between">
                <h1 class="header-title">Confirmación #RES-<?php echo $idReserva; ?></h1>
                <div>
                    <span class="status-pill" style="background: <?php echo $bgColor; ?>; color: <?php echo $color; ?>;">
                        <i class="fa-solid fa-circle status-icon"></i>
                        <?php echo $status; ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="info-grid">
            <div class="main-card">
                <div class="prop-info-header">
                    <img src="<?php echo $mainImage; ?>" class="prop-main-img">
                    <div>
                        <h2 class="prop-title"><?php echo htmlspecialchars($reserva['nombrePropiedad']); ?></h2>
                        <p class="prop-location"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($reserva['ciudad'] . ', ' . $reserva['pais']); ?></p>
                        <p class="prop-host">Anfitrión: <?php echo htmlspecialchars($reserva['hostNombre'] . ' ' . $reserva['hostApellido']); ?></p>
                    </div>
                </div>

                <div class="info-details-grid">
                    <div>
                        <h3 class="section-title-muted">Información de Estancia</h3>
                        
                        <div class="detail-group">
                            <label class="detail-label">Llegada</label>
                            <p class="detail-value"><?php echo $fechaInicio->format('l, d F Y'); ?></p>
                        </div>
                        
                        <div class="detail-group">
                            <label class="detail-label">Salida</label>
                            <p class="detail-value"><?php echo $fechaFin->format('l, d F Y'); ?></p>
                        </div>

                        <div>
                            <label class="detail-label">Noches</label>
                            <p class="detail-value"><?php echo $noches; ?> Noches</p>
                        </div>
                    </div>

                    <div class="payment-summary-box">
                        <h3 class="section-title-muted">Resumen de Pago</h3>
                        
                        <div class="summary-item">
                            <span>$<?php echo number_format($reserva['dPrecioNoche'], 0); ?> x <?php echo $noches; ?> noches</span>
                            <span class="summary-value-bold">$<?php echo number_format($reserva['dPrecioNoche'] * $noches, 0); ?></span>
                        </div>
                        <div class="summary-item">
                            <span>Tarifa de limpieza</span>
                            <span class="summary-value-bold">$1,200</span>
                        </div>
                        <div class="summary-item">
                            <span>Total</span>
                            <span class="summary-total-value">$<?php echo number_format($reserva['dTotalReserva'], 0); ?> MXN</span>
                        </div>
                    </div>
                </div>

                <div class="actions-row">
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="fa-solid fa-print"></i> Imprimir Comprobante
                    </button>
                    <button class="btn btn-outline-primary" onclick="window.location.href='reservas.php'">
                        Volver a Reservaciones
                    </button>
                    <?php if ($status !== 'CANCELADA' && $status !== 'FINALIZADA'): ?>
                    <button class="btn btn-cancel-reserva" onclick="cancelarReserva(<?php echo $idReserva; ?>, 'huesped', <?php echo $userId; ?>)">
                        <i class="fa-solid fa-ban"></i> Cancelar Reserva
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="side-cards-col">
                <div class="main-card card-padding">
                    <h3 class="card-title">Políticas de Estancia</h3>
                    <ul class="policies-list">
                        <li><i class="fa-solid fa-ban policy-icon"></i> Prohibido fiestas o eventos</li>
                        <li><i class="fa-solid fa-clock policy-icon"></i> Check-in: 15:00 - 20:00</li>
                        <li><i class="fa-solid fa-smoking-ban policy-icon"></i> No fumar dentro</li>
                    </ul>
                </div>

               
            </div>
        </div>
    </div>

    <!-- Cancelation Modal -->
    <div id="modalCancelacion" class="modal-overlay">
        <div class="modal-content-box">
            <div class="modal-close-btn" onclick="cerrarModalCancelacion()">
                <i class="fa-solid fa-xmark"></i>
            </div>
            
            <div class="modal-header-row">
                <div class="modal-alert-icon">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h3 class="modal-title">Cancelar Reserva</h3>
                    <p class="modal-subtitle">Por favor indícanos el motivo de la cancelación</p>
                </div>
            </div>

            <div class="modal-field-group">
                <label class="modal-label">Motivo de cancelación</label>
                <textarea id="motivoCancelacion" rows="4" class="modal-textarea" placeholder="Escribe el motivo detallado de por qué necesitas cancelar esta reserva..."></textarea>
            </div>

            <div class="refund-policy-box">
                <h4 class="refund-policy-title">
                    <i class="fa-solid fa-circle-info" style="color: var(--primary);"></i> Política de Reembolso
                </h4>
                
                <div class="refund-info-col">
                    <div id="refundInfoBox" class="refund-status-box">
                        <p class="refund-status-text" id="refundStatusText">Calculando reembolso...</p>
                        <p class="refund-policy-detail" id="refundPolicyDetail">Si cancelas con más de 24 horas de anticipación, recibirás un reembolso total.</p>
                    </div>

                    <div class="refund-row">
                        <span>Total de la reserva:</span>
                        <span class="refund-val-bold">$<?php echo number_format($reserva['dTotalReserva'], 2); ?></span>
                    </div>
                    <div class="refund-row">
                        <span>Cargo por cancelación:</span>
                        <span class="refund-val-danger" id="cancelChargeText">$0.00</span>
                    </div>
                    <div class="refund-total-row">
                        <span class="refund-total-label">Monto a devolver:</span>
                        <span class="refund-total-val" id="refundAmountText">$0.00</span>
                    </div>
                </div>
            </div>

            <div class="modal-actions-row">
                <button onclick="cerrarModalCancelacion()" class="btn-modal-back">Volver</button>
                <button onclick="confirmarCancelacion()" class="btn-modal-confirm">Confirmar la cancelación</button>
            </div>
        </div>
    </div>

    <script>
        // Variables del servidor — inyectadas para uso en detalle_reserva.js
        window.RESERVA_DATA = {
            fechaInicio:  '<?php echo $reserva['dtFechaInicio']; ?>',
            totalReserva: <?php echo $reserva['dTotalReserva']; ?>
        };
    </script>
    <script src="../../recursos/js/huesped/detalle_reserva.js"></script>

</body>
</html>
