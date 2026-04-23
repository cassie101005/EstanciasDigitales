<?php
session_start();
require_once '../../datos/conexion.php';

// Simular usuario logueado si no hay sesión
$idUsuarioHuesped = isset($_SESSION['idUsuario']) ? $_SESSION['idUsuario'] : 2; // ID 2 por defecto para pruebas

// Consultar reservaciones del usuario
$sql = "SELECT r.*, p.vNombre as nombrePropiedad, p.vDescripcion, 
               (SELECT vImagen FROM tbl_imagen_propiedad WHERE idPropiedad = p.idPropiedad LIMIT 1) as imagen,
               ci.vNombreCiudad as ciudad, pa.vNombrePais as pais
        FROM tbl_reserva r
        JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad
        LEFT JOIN tbl_ciudad ci ON p.idCiudad = ci.idCiudad
        LEFT JOIN tbl_estado es ON ci.idEstado = es.idEstado
        LEFT JOIN tbl_pais pa ON es.idPais = pa.idPais
        WHERE r.idUsuario = ?
        ORDER BY r.dtFechaInicio DESC";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $idUsuarioHuesped);
$stmt->execute();
$reservas = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Reservaciones | Estancias Digitales</title>
    <link rel="stylesheet" href="../../recursos/css/variables.css">
    <link rel="stylesheet" href="../../recursos/css/main.css">
    <link rel="stylesheet" href="../../recursos/css/components/navbar.css">
    <link rel="stylesheet" href="../../recursos/css/huesped/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background: #f8f9fa;">
    <?php include '../../recursos/navbar.php'; ?>

    <div class="reservation-container">
        <header>
            <h1 style="font-size: 2.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem;">Mis Reservaciones</h1>
            <p style="color: #64748b; font-size: 1.1rem;">Gestiona tus estancias actuales y revisa tus experiencias pasadas.</p>
        </header>

        <div class="filter-pills">
            <button class="filter-pill active">Todas</button>
            <button class="filter-pill">Próximas</button>
            <button class="filter-pill">En curso</button>
            <button class="filter-pill">Completadas</button>
        </div>

        <div class="reservations-list">
            
            <?php if ($reservas->num_rows > 0): ?>
                <?php while ($res = $reservas->fetch_assoc()): ?>
                    <?php 
                        $fechaInicio = new DateTime($res['dtFechaInicio']);
                        $fechaFin = new DateTime($res['dtFechaFin']);
                        $hoy = new DateTime();
                        
                        $status = "CONFIRMADA";
                        $statusColor = "var(--primary)";
                        
                        if ($hoy >= $fechaInicio && $hoy <= $fechaFin) {
                            $status = "EN CURSO";
                            $statusColor = "#008a60";
                        } elseif ($hoy > $fechaFin) {
                            $status = "FINALIZADA";
                            $statusColor = "#6c757d";
                        }
                    ?>
                    <div class="res-card-v2">
                        <div class="res-img-box">
                            <img src="<?php echo htmlspecialchars($res['imagen'] ?? 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=600&q=80'); ?>">
                            <div class="status-badge-v2" style="background: <?php echo $statusColor; ?>;"><?php echo $status; ?></div>
                        </div>
                        <div class="res-content-box">
                            <h2 style="font-size: 1.5rem; font-weight: 700;"><?php echo htmlspecialchars($res['nombrePropiedad']); ?></h2>
                            <div style="font-size: 14px; color: #64748b; display: flex; flex-direction: column; gap: 0.5rem;">
                                <span style="display: flex; align-items: center; gap: 0.75rem;">
                                    <i class="fa-regular fa-calendar"></i> 
                                    <?php echo $fechaInicio->format('d M') . ' - ' . $fechaFin->format('d M, Y'); ?>
                                </span>
                                <span style="display: flex; align-items: center; gap: 0.75rem;">
                                    <i class="fa-solid fa-location-dot"></i> 
                                    <?php echo htmlspecialchars($res['ciudad'] . ', ' . $res['pais']); ?>
                                </span>
                            </div>
                            <div class="res-actions">
                                <button class="btn btn-primary" onclick="window.location.href='detalle.php?id=<?php echo $res['idPropiedad']; ?>'">Ver detalle</button>
                                <button class="btn btn-res-grey">Contactar anfitrión</button>
                            </div>
                            <div class="res-price-abs">$<?php echo number_format($res['dTotalReserva'], 0); ?> <span>total</span></div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 4rem; background: white; border-radius: 1rem; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
                    <i class="fa-solid fa-calendar-xmark" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1.5rem;"></i>
                    <h2 style="font-size: 1.5rem; font-weight: 700; color: #64748b;">No tienes reservaciones aún</h2>
                    <p style="color: #94a3b8; margin-top: 0.5rem;">¡Explora nuestras propiedades y planea tu próximo viaje!</p>
                    <button class="btn btn-primary" onclick="window.location.href='home.php'" style="margin-top: 2rem;">Explorar Marketplace</button>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <footer class="main-footer" style="padding: 4rem 10%; margin-top: 6rem; background: white;">
        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <div>Estancias Digitales © 2024. Tus viajes, simplificados.</div>
            <div class="footer-links">
                <a href="#">Privacidad</a>
                <a href="#">Ayuda</a>
            </div>
        </div>
    </footer>
</body>
</html>
