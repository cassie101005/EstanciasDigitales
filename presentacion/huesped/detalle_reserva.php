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

// 4. Consultar reglas y políticas de la propiedad real
require_once '../../negocio/huesped/detalle_view.php';
$reglas = getPropertyReglas($idPropiedad, $conexion);
$politicas = getPropertyPoliticas($idPropiedad, $conexion);
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
    <link rel="stylesheet" href="../../recursos/css/huesped/detalle_reserva.css?v=<?php echo time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Fuerza absoluta para que SweetAlert esté por encima de cualquier modal */
        .swal2-container, 
        .swal2-backdrop, 
        .swal2-popup {
            z-index: 999999999 !important;
        }
    </style>
</head>
<body class="body-surface">
    <?php include '../../recursos/navbar.php'; ?>

    <div class="container main-container">
        
        <!-- Header Section -->
        <div class="reserva-detail-header">
            <a href="reservas.php" class="back-link">
                <i class="fa-solid fa-arrow-left"></i> Volver a mis reservaciones
            </a>
            <div class="header-flex-between">
                <h1 class="header-title">Confirmación #RES-<?php echo $idReserva; ?></h1>
                <div>
                    <?php echo renderizarBadgeEstado($reserva); ?>
                </div>
            </div>
        </div>

        <!-- Main Content Layout -->
        <div class="info-grid">
            
            <!-- Left Column: Details & Payment -->
            <div class="main-column">
                <div class="main-card">
                    <!-- Property Image -->
                    <img src="<?php echo $mainImage; ?>" class="reserva-img" alt="Propiedad">

                    <!-- Property Info -->
                    <div class="prop-info-section">
                        <h2 class="prop-title"><?php echo htmlspecialchars($reserva['nombrePropiedad']); ?></h2>
                        <p class="prop-location">
                            <i class="fa-solid fa-location-dot"></i> 
                            <?php echo htmlspecialchars($reserva['ciudad'] . ', ' . $reserva['pais']); ?>
                        </p>
                        <p class="prop-host">Anfitrión: <?php echo htmlspecialchars($reserva['hostNombre'] . ' ' . $reserva['hostApellido']); ?></p>
                    </div>

                    <!-- Stay Details -->
                    <div class="details-section">
                        <div class="detail-group">
                            <label>Llegada</label>
                            <p><?php echo $fechaInicio->format('d M, Y'); ?></p>
                        </div>
                        <div class="detail-group">
                            <label>Salida</label>
                            <p><?php echo $fechaFin->format('d M, Y'); ?></p>
                        </div>
                        <div class="detail-group">
                            <label>Duración</label>
                            <p><?php echo $noches; ?> Noches</p>
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    <div class="payment-section">
                        <h3 class="payment-title">Resumen de Pago (Cerrado)</h3>
                        
                        <?php 
                            // Usar snapshot si existe
                            $precioSnapshot = !empty($reserva['dPrecioNocheReserva']) ? floatval($reserva['dPrecioNocheReserva']) : floatval($reserva['dPrecioNoche']);
                            $limpiezaSnapshot = isset($reserva['dLimpiezaReserva']) ? floatval($reserva['dLimpiezaReserva']) : 1200;
                            $subtotalSnapshot = !empty($reserva['dSubtotalReserva']) ? floatval($reserva['dSubtotalReserva']) : ($precioSnapshot * $noches);
                            $impuestosSnapshot = isset($reserva['dImpuestosReserva']) ? floatval($reserva['dImpuestosReserva']) : ($subtotalSnapshot * 0.16);
                            
                            // Desglose de noches (JSON)
                            $desglose = !empty($reserva['tDesgloseNoches']) ? json_decode($reserva['tDesgloseNoches'], true) : null;
                        ?>

                        <!-- Lista Detallada de Noches -->
                        <div class="nightly-breakdown">
                            <span class="breakdown-title">Desglose por noche</span>
                            <?php if ($desglose): ?>
                                <?php foreach ($desglose as $n): ?>
                                    <div class="night-item <?php echo $n['esEspecial'] ? 'night-special' : ''; ?>">
                                        <span>
                                            <?php echo date('d M, Y', strtotime($n['fecha'])); ?>
                                            <?php if ($n['esEspecial']): ?>
                                                <span class="special-tag">Tarifa especial</span>
                                            <?php endif; ?>
                                        </span>
                                        <span>$<?php echo number_format($n['precio'], 2); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <!-- Fallback para reservas antiguas sin JSON -->
                                <div class="night-item">
                                    <span>$<?php echo number_format($precioSnapshot, 2); ?> × <?php echo $noches; ?> noches</span>
                                    <span>$<?php echo number_format($subtotalSnapshot, 2); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="payment-row">
                            <span>Subtotal noches</span>
                            <span class="payment-val-bold">$<?php echo number_format($subtotalSnapshot, 2); ?></span>
                        </div>
                        
                        <div class="payment-row">
                            <span>Tarifa de limpieza</span>
                            <span class="payment-val-bold">$<?php echo number_format($limpiezaSnapshot, 2); ?></span>
                        </div>

                        <div class="payment-row">
                            <span>Impuestos (16%)</span>
                            <span class="payment-val-bold">$<?php echo number_format($impuestosSnapshot, 2); ?></span>
                        </div>

                        <div class="payment-total-row">
                            <span class="total-label">Monto Total Pagado</span>
                            <span class="total-value">$<?php echo number_format($reserva['dTotalReserva'], 2); ?> MXN</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="actions-row">
                        
                        <button class="btn btn-outline" onclick="window.location.href='reservas.php'">
                            Volver a Reservaciones
                        </button>
                        <?php if ($status !== 'CANCELADA' && $status !== 'FINALIZADA'): ?>
                        <button class="btn btn-cancel" onclick="cancelarReserva(<?php echo $idReserva; ?>, 'huesped', <?php echo $userId; ?>)">
                            <i class="fa-solid fa-ban"></i> Cancelar Reserva
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column: Policies -->
            <div class="side-column">
                <div class="policies-card">
                    <h3 class="section-title">Reglas de la estancia</h3>
                    <ul class="policies-list">
                        <?php foreach ($reglas as $regla): ?>
                            <li>
                                <i class="fa-solid fa-check policy-icon"></i> 
                                <span><?php echo htmlspecialchars($regla); ?></span>
                            </li>
                        <?php endforeach; ?>
                        <?php if (empty($reglas)): ?>
                            <li style="color: #94a3b8; font-style: italic;">No hay reglas específicas.</li>
                        <?php endif; ?>
                    </ul>

                    <h3 class="section-title" style="margin-top: 2rem;">Políticas de la propiedad</h3>
                    <ul class="policies-list">
                        <?php foreach ($politicas as $politica): ?>
                            <li>
                                <i class="fa-solid fa-circle-info policy-icon"></i> 
                                <span><?php echo htmlspecialchars($politica); ?></span>
                            </li>
                        <?php endforeach; ?>
                        <?php if (empty($politicas)): ?>
                            <li style="color: #94a3b8; font-style: italic;">No hay políticas específicas.</li>
                        <?php endif; ?>
                    </ul>
                    
                    <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #f1f5f9;">
                        <p style="font-size: 13px; color: #94a3b8; line-height: 1.6;">
                            Al reservar, aceptas las reglas de la casa y las políticas de cancelación de Estancias Digitales.
                        </p>
                    </div>
                </div>
            </div>
        </div>    <!-- Modal de Cancelación (Sección reutilizable del sistema global) -->
    <div id="modalCancelacion" class="modal-overlay">
        <div style="background: white; border-radius: 24px; width: 100%; max-width: 480px; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15); border: 1px solid rgba(255,255,255,0.1); position: relative;">
            
            <!-- Sticky Header -->
            <div style="padding: 1.25rem 2rem; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 1.25rem; background: white; z-index: 10;">
                <div style="width: 48px; height: 48px; border-radius: 14px; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div style="flex: 1;">
                    <h3 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.5px;">Cancelar Reserva</h3>
                    <p style="font-size: 13px; color: #64748b; margin: 0; margin-top: 2px; font-weight: 500;">Indícanos el motivo de la cancelación</p>
                </div>
                <div style="cursor: pointer; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: #f1f5f9; color: #64748b; font-size: 1rem; transition: all 0.2s; flex-shrink: 0;" onclick="cerrarModalCancelacion()">
                    <i class="fa-solid fa-xmark"></i>
                </div>
            </div>

            <!-- Scrollable Content -->
            <div style="flex: 1; overflow-y: auto; padding: 1.5rem 2rem; scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;">
                
                <!-- Motivo Card -->
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 11px; font-weight: 800; color: #94a3b8; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 1.5px;">Motivo de cancelación</label>
                    <textarea id="motivoCancelacion" rows="4" style="width: 100%; padding: 1.25rem; border-radius: 16px; border: 1px solid #f1f5f9; font-size: 14px; background: #f8fafc; color: #475569; resize: none; outline: none; transition: border-color 0.2s;" placeholder="Escribe aquí por qué necesitas cancelar..."></textarea>
                </div>

                <!-- Reembolso Card -->
                <div style="background: #f8fafc; padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0;">
                    <h4 style="font-size: 13px; font-weight: 800; color: #0f172a; margin: 0 0 0.75rem 0; display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-circle-info" style="color: var(--primary);"></i> Detalle de Reembolso
                    </h4>
                    
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <div id="refundInfoBox" style="padding: 0.75rem; border-radius: 10px; background: white; border-left: 4px solid #64748b;">
                            <p style="margin: 0; font-size: 13px; font-weight: 700; color: #0f172a;" id="refundStatusText">Calculando...</p>
                            <p style="margin: 4px 0 0 0; font-size: 12px; color: #64748b; line-height: 1.4;" id="refundPolicyDetail">Consultando políticas...</p>
                        </div>

                        <div style="display: flex; justify-content: space-between; font-size: 12px; color: #64748b; padding: 0 4px; font-weight: 500;">
                            <span>Pagado:</span>
                            <span style="font-weight: 700; color: #0f172a;">$<?php echo number_format($reserva['dTotalReserva'], 2); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 12px; color: #64748b; padding: 0 4px; font-weight: 500;">
                            <span>Cargo por servicio:</span>
                            <span style="font-weight: 700; color: #dc2626;" id="cancelChargeText">$0.00</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 14px; color: #0f172a; padding: 0.75rem 4px 0 4px; border-top: 1px dashed #cbd5e1;">
                            <span style="font-weight: 800;">Reembolso estimado:</span>
                            <span style="font-weight: 900; color: var(--primary);" id="refundAmountText">$0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sticky Footer -->
            <div style="padding: 1.25rem 2rem; border-top: 1px solid #f1f5f9; background: white; display: flex; gap: 1rem; z-index: 10;">
                <button onclick="cerrarModalCancelacion()" style="flex: 1; padding: 0.875rem; border: 1px solid #e2e8f0; background: #ffffff; color: #475569; border-radius: 12px; font-weight: 700; cursor: pointer; transition: all 0.2s; font-size: 13px;">Volver</button>
                <button onclick="confirmarCancelacion()" style="flex: 1; padding: 0.875rem; border: none; background: #dc2626; color: white; border-radius: 12px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.15); font-size: 13px;">Confirmar Cancelación</button>
            </div>
        </div>
    </div>

    <script>
        // Datos inyectados para el JS
        window.RESERVA_DATA = {
            fechaInicio:  '<?php echo $reserva['dtFechaInicio']; ?>',
            fechaRegistro: '<?php echo $reserva['dtFechaRegistro']; ?>',
            totalReserva: <?php echo $reserva['dTotalReserva']; ?>
        };
    </script>
    <script src="../../recursos/js/huesped/detalle_reserva.js?v=<?php echo time(); ?>"></script>

</body>
</html>
