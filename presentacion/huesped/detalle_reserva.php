<?php
session_start();
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
$mainImage = !empty($images) ? $images[0] : "https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80";

// 3. Formatear fechas y calcular estado
$fechaInicio = new DateTime($reserva['dtFechaInicio']);
$fechaFin = new DateTime($reserva['dtFechaFin']);
$hoy = new DateTime();
$diff = $fechaInicio->diff($fechaFin);
$noches = $diff->days;

$status = "CONFIRMADA";
$color = "#3b82f6";
$bgColor = "#eff6ff";

if ($hoy >= $fechaInicio && $hoy <= $fechaFin) {
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
        
        <div class="reserva-detail-header">
            <div>
                <h1 style="font-size: 2.2rem; font-weight: 800; margin-top: 0.5rem;">Confirmación #RES-<?php echo $idReserva; ?></h1>
            </div>
            <div>
                <span class="status-pill" style="background: <?php echo $bgColor; ?>; color: <?php echo $color; ?>;">
                    <i class="fa-solid fa-circle" style="font-size: 8px; margin-right: 8px;"></i>
                    <?php echo $status; ?>
                </span>
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

</body>
</html>
