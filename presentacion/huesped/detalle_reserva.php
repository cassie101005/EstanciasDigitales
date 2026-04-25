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

// 1. Consultar detalles de la reservación
$sqlRes = "SELECT r.*, p.vNombre as nombrePropiedad, p.dPrecioNoche,
                  u.vNombre as hostNombre, u.vApellido as hostApellido,
                  tp.vNombreCategoria as tipo, ci.vNombreCiudad as ciudad,
                  es.vNombreEstado as estado, pa.vNombrePais as pais
           FROM tbl_reserva r
           JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad
           JOIN tbl_usuarios u ON p.idUsuario = u.idUsuario
           LEFT JOIN tbl_tipo_propiedad tp ON p.idTipoPropiedad = tp.idTipoPropiedad
           LEFT JOIN tbl_ciudad ci ON p.idCiudad = ci.idCiudad
           LEFT JOIN tbl_estado es ON ci.idEstado = es.idEstado
           LEFT JOIN tbl_pais pa ON es.idPais = pa.idPais
           WHERE r.idReserva = ? AND r.idUsuario = ?";

$stmtRes = $conexion->prepare($sqlRes);
$stmtRes->bind_param("ii", $idReserva, $userId);
$stmtRes->execute();
$reserva = $stmtRes->get_result()->fetch_assoc();

if (!$reserva) {
    header("Location: reservas.php");
    exit();
}

// 2. Consultar imágenes de la propiedad
$sqlImages = "SELECT vImagen FROM tbl_imagen_propiedad WHERE idPropiedad = ? ORDER BY idImagen ASC";
$stmtImages = $conexion->prepare($sqlImages);
$stmtImages->bind_param("i", $idPropiedad);
$stmtImages->execute();
$imagesResult = $stmtImages->get_result();
$images = [];
while ($row = $imagesResult->fetch_assoc()) {
    $images[] = $row['vImagen'];
}
$mainImage = !empty($images) ? $images[0] : "";
if ($mainImage && strpos($mainImage, 'http') === false) {
    $mainImage = "../../" . $mainImage;
} elseif (!$mainImage) {
    $mainImage = "https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80";
}

// 3. Formatear fechas y calcular estado
$fechaInicio = new DateTime($reserva['dtFechaInicio']);
$fechaFin = new DateTime($reserva['dtFechaFin']);
$hoy = new DateTime();
$diff = $fechaInicio->diff($fechaFin);
$noches = $diff->days;

$status = "CONFIRMADA";
$color = "#3b82f6";
$bgColor = "#eff6ff";

if (isset($reserva['vEstatus']) && (strtoupper($reserva['vEstatus']) === 'CANCELADA' || strtoupper($reserva['vEstatus']) === 'CANCELADO')) {
    $status = "CANCELADA";
    $color = "#991b1b";
    $bgColor = "#fee2e2";
} elseif (isset($reserva['vEstado']) && (strtoupper($reserva['vEstado']) === 'CANCELADA' || strtoupper($reserva['vEstado']) === 'CANCELADO')) {
    $status = "CANCELADA";
    $color = "#991b1b";
    $bgColor = "#fee2e2";
} elseif ($hoy >= $fechaInicio && $hoy <= $fechaFin) {
    $status = "EN CURSO";
    $color = "#059669";
    $bgColor = "#ecfdf5";
} elseif ($hoy > $fechaFin) {
    $status = "FINALIZADA";
    $color = "#64748b";
    $bgColor = "#f8fafc";
}
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
    <style>
        .reserva-detail-header {
            background: white;
            padding: 2.5rem;
            border-radius: 2rem;
            box-shadow: 0 4px 30px rgba(0,0,0,0.03);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 2rem;
        }
        .main-card {
            background: white;
            padding: 2.5rem;
            border-radius: 2rem;
            box-shadow: 0 4px 30px rgba(0,0,0,0.03);
        }
        .status-pill {
            padding: 0.6rem 1.2rem;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .summary-item:last-child {
            border-bottom: none;
            padding-top: 1.5rem;
            font-size: 1.25rem;
            font-weight: 800;
        }
    </style>
</head>
<body style="background: var(--surface);">
    <?php include '../../recursos/navbar.php'; ?>

    <div class="container" style="max-width: 1200px; margin: 4rem auto; padding: 0 2rem;">
        
        <div class="reserva-detail-header" style="flex-direction: column; align-items: flex-start; gap: 1rem;">
            <a href="reservas.php" style="display: inline-flex; align-items: center; gap: 0.5rem; color: #64748b; text-decoration: none; font-weight: 700; font-size: 0.9rem; transition: all 0.2s;" onmouseover="this.style.color='var(--primary)'; this.style.transform='translateX(-5px)'" onmouseout="this.style.color='#64748b'; this.style.transform='translateX(0)'">
                <i class="fa-solid fa-arrow-left"></i> Volver a mis reservaciones
            </a>
            <div style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
                <h1 style="font-size: 2.2rem; font-weight: 800; margin-top: 0.5rem;">Confirmación #RES-<?php echo $idReserva; ?></h1>
                <div>
                    <span class="status-pill" style="background: <?php echo $bgColor; ?>; color: <?php echo $color; ?>;">
                        <i class="fa-solid fa-circle" style="font-size: 8px; margin-right: 8px;"></i>
                        <?php echo $status; ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="info-grid">
            <div class="main-card">
                <div style="display: flex; gap: 2rem; align-items: flex-start; margin-bottom: 3rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 2rem;">
                    <img src="<?php echo $mainImage; ?>" style="width: 240px; height: 160px; object-fit: cover; border-radius: 1.5rem;">
                    <div>
                        <h2 style="font-size: 1.6rem; font-weight: 800;"><?php echo htmlspecialchars($reserva['nombrePropiedad']); ?></h2>
                        <p style="color: #64748b; margin-top: 0.5rem;"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($reserva['ciudad'] . ', ' . $reserva['pais']); ?></p>
                        <p style="margin-top: 1rem; font-weight: 600;">Anfitrión: <?php echo htmlspecialchars($reserva['hostNombre'] . ' ' . $reserva['hostApellido']); ?></p>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">
                    <div>
                        <h3 style="font-size: 12px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 1.5rem;">Información de Estancia</h3>
                        
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; font-size: 13px; color: #64748b;">Llegada</label>
                            <p style="font-size: 1.1rem; font-weight: 700;"><?php echo $fechaInicio->format('l, d F Y'); ?></p>
                        </div>
                        
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; font-size: 13px; color: #64748b;">Salida</label>
                            <p style="font-size: 1.1rem; font-weight: 700;"><?php echo $fechaFin->format('l, d F Y'); ?></p>
                        </div>

                        <div>
                            <label style="display: block; font-size: 13px; color: #64748b;">Noches</label>
                            <p style="font-size: 1.1rem; font-weight: 700;"><?php echo $noches; ?> Noches</p>
                        </div>
                    </div>

                    <div style="background: #f8fafc; padding: 2rem; border-radius: 1.5rem;">
                        <h3 style="font-size: 12px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 1.5rem;">Resumen de Pago</h3>
                        
                        <div class="summary-item">
                            <span>$<?php echo number_format($reserva['dPrecioNoche'], 0); ?> x <?php echo $noches; ?> noches</span>
                            <span style="font-weight: 700;">$<?php echo number_format($reserva['dPrecioNoche'] * $noches, 0); ?></span>
                        </div>
                        <div class="summary-item">
                            <span>Tarifa de limpieza</span>
                            <span style="font-weight: 700;">$1,200</span>
                        </div>
                        <div class="summary-item">
                            <span>Total</span>
                            <span style="color: var(--primary); font-weight: 800;">$<?php echo number_format($reserva['dTotalReserva'], 0); ?> MXN</span>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 3rem; display: flex; gap: 1rem;">
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="fa-solid fa-print"></i> Imprimir Comprobante
                    </button>
                    <button class="btn btn-outline-primary" onclick="window.location.href='reservas.php'">
                        Volver a Reservaciones
                    </button>
                    <?php if ($status !== 'CANCELADA' && $status !== 'FINALIZADA'): ?>
                    <button class="btn" style="color: #ef4444; border: 1px solid #ef4444; padding: 0.8rem 1.5rem; border-radius: 12px; background: transparent; cursor: pointer; font-weight: 800;" onclick="cancelarReserva(<?php echo $idReserva; ?>, 'huesped', <?php echo $userId; ?>)">
                        <i class="fa-solid fa-ban"></i> Cancelar Reserva
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <div class="main-card" style="padding: 2rem;">
                    <h3 style="font-size: 1rem; font-weight: 800; margin-bottom: 1.5rem;">Políticas de Estancia</h3>
                    <ul style="list-style: none; display: flex; flex-direction: column; gap: 1rem; font-size: 14px; color: #64748b;">
                        <li><i class="fa-solid fa-ban" style="margin-right: 10px;"></i> Prohibido fiestas o eventos</li>
                        <li><i class="fa-solid fa-clock" style="margin-right: 10px;"></i> Check-in: 15:00 - 20:00</li>
                        <li><i class="fa-solid fa-smoking-ban" style="margin-right: 10px;"></i> No fumar dentro</li>
                    </ul>
                </div>

               
            </div>
        </div>
    </div>

    <!-- Cancelation Modal -->
    <div id="modalCancelacion" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 20px; width: 90%; max-width: 450px; padding: 2rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); position: relative;">
            <div style="position: absolute; top: 1.5rem; right: 1.5rem; cursor: pointer; color: #94a3b8; font-size: 1.2rem;" onclick="cerrarModalCancelacion()">
                <i class="fa-solid fa-xmark"></i>
            </div>
            
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h3 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0;">Cancelar Reserva</h3>
                    <p style="font-size: 13px; color: #64748b; margin: 0; margin-top: 4px;">Por favor indícanos el motivo de la cancelación</p>
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 0.5rem;">Motivo de cancelación</label>
                <textarea id="motivoCancelacion" rows="4" style="width: 100%; padding: 1rem; border-radius: 12px; border: 1px solid #cbd5e1; font-family: inherit; font-size: 14px; resize: none; background: #f8fafc;" placeholder="Escribe el motivo detallado de por qué necesitas cancelar esta reserva..."></textarea>
            </div>

            <div style="background: #f1f5f9; padding: 1.25rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid #e2e8f0;">
                <h4 style="font-size: 13px; font-weight: 800; color: #0f172a; margin: 0 0 0.75rem 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-circle-info" style="color: var(--primary);"></i> Política de Reembolso
                </h4>
                
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div id="refundInfoBox" style="padding: 0.75rem; border-radius: 8px; background: white; border-left: 4px solid #10b981;">
                        <p style="margin: 0; font-size: 13px; font-weight: 700; color: #0f172a;" id="refundStatusText">Calculando reembolso...</p>
                        <p style="margin: 4px 0 0 0; font-size: 12px; color: #64748b;" id="refundPolicyDetail">Si cancelas con más de 24 horas de anticipación, recibirás un reembolso total.</p>
                    </div>

                    <div style="display: flex; justify-content: space-between; font-size: 13px; color: #64748b; padding: 0 4px;">
                        <span>Total de la reserva:</span>
                        <span style="font-weight: 700; color: #0f172a;">$<?php echo number_format($reserva['dTotalReserva'], 2); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 13px; color: #64748b; padding: 0 4px;">
                        <span>Cargo por cancelación:</span>
                        <span style="font-weight: 700; color: #ef4444;" id="cancelChargeText">$0.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px; color: #0f172a; padding: 8px 4px 0 4px; border-top: 1px dashed #cbd5e1;">
                        <span style="font-weight: 800;">Monto a devolver:</span>
                        <span style="font-weight: 800; color: #10b981;" id="refundAmountText">$0.00</span>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button onclick="cerrarModalCancelacion()" style="flex: 1; padding: 0.875rem; border: 1px solid #cbd5e1; background: white; color: #475569; border-radius: 12px; font-weight: 700; cursor: pointer;">Volver</button>
                <button onclick="confirmarCancelacion()" style="flex: 1; padding: 0.875rem; border: none; background: #dc2626; color: white; border-radius: 12px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);">Confirmar la cancelación</button>
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
