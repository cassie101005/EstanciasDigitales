<?php
session_start();
require_once '../../datos/conexion.php';
$idHost = $_SESSION['idUsuario'] ?? 1;

// Obtener las notificaciones recientes de reservas
$sqlNotif = "SELECT r.idReserva, r.dtFechaInicio, r.dtFechaFin, r.vEstatus, u.vNombre, u.vApellido, u.vFoto, p.vNombre as propiedad
             FROM tbl_reserva r 
             JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad 
             JOIN tbl_usuarios u ON r.idUsuario = u.idUsuario
             WHERE p.idUsuario = ? 
             ORDER BY r.idReserva DESC LIMIT 6";
$stmtNotif = $conexion->prepare($sqlNotif);
$notificaciones = [];
if ($stmtNotif) {
    $stmtNotif->bind_param("i", $idHost);
    $stmtNotif->execute();
    $resNotif = $stmtNotif->get_result();
    while ($row = $resNotif->fetch_assoc()) {
        $notificaciones[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control | Modo Anfitrión</title>
    <link rel="stylesheet" href="../../recursos/css/variables.css">
    <link rel="stylesheet" href="../../recursos/css/layouts/shared.css">
    <link rel="stylesheet" href="../../recursos/css/components/navbar.css">
    <link rel="stylesheet" href="../../recursos/css/anfitrion/host_main.css">
    <link rel="stylesheet" href="../../recursos/css/admin/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="host-body">
    <div class="host-wrapper">
        <aside class="sidebar-host">
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div class="host-logo-box">
                    <h2 style="font-size: 1.3rem; display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-house-laptop"></i>
                        Estancias Digitales
                    </h2>
                    <p>Modo Anfitrión</p>
                </div>
                
                <nav class="side-nav-host">
                    <li class="side-nav-item active" onclick="window.location.href='dashboard.php'"><i class="fa-solid fa-house"></i> Inicio</li>
                    <li class="side-nav-item" onclick="window.location.href='propiedades.php'"><i class="fa-solid fa-building"></i> Propiedades</li>
                    <li class="side-nav-item" onclick="window.location.href='calendario.php'"><i class="fa-solid fa-calendar-days"></i> Calendario</li>
                    <li class="side-nav-item" onclick="window.location.href='reservas.php'"><i class="fa-solid fa-receipt"></i> Reservas</li>
                </nav>
            </div>

        </aside>

        <main class="host-content-main">
            <?php include '../../recursos/navbar.php'; ?>
            
            <div class="host-dashboard-container">
                <section class="main-stats-section">
                    <header style="margin-bottom: 2.5rem;">
                        <h1 style="font-size: 2.2rem; font-weight: 800; letter-spacing: -1px; color: var(--text-main);">Bienvenido a EstanciasDigitales</h1>
                        <p style="color: #94a3b8; font-size: 14px; font-weight: 500;">Aquí tienes el rendimiento de tus propiedades para este mes.</p>
                    </header>

    

                    <!-- Recent Reservations -->
                    <div style="margin-top: 4rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                            <h3 style="font-size: 1.5rem; font-weight: 800; letter-spacing: -1px;">Reservas recientes</h3>
                            <a href="reservas.php" style="font-size: 13px; font-weight: 800; color: var(--primary); text-decoration: none;">Ver todas</a>
                        </div>

                        <div class="reservation-list-item">
                            <div style="display: flex; gap: 1.5rem; align-items: center;">
                                <img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=60&q=80" style="width: 52px; height: 52px; border-radius: 12px; object-fit: cover;">
                                <div>
                                    <div style="font-size: 15px; font-weight: 800;">Sofia Martínez</div>
                                    <div style="font-size: 12px; color: #94a3b8; margin-top: 2px;">Villa Marítima • 12 - 18 Oct</div>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <span class="status-tag tag-confirmed" style="font-size: 9px; padding: 4px 10px;">CONFIRMADA</span>
                                <div style="margin-top: 8px; font-size: 14px; font-weight: 800;">$1,240</div>
                            </div>
                        </div>

                        <div class="reservation-list-item">
                            <div style="display: flex; gap: 1.5rem; align-items: center;">
                                <img src="https://images.unsplash.com/photo-1544984243-ec57ea16fe25?auto=format&fit=crop&w=60&q=80" style="width: 52px; height: 52px; border-radius: 12px; object-fit: cover;">
                                <div>
                                    <div style="font-size: 15px; font-weight: 800;">Erik Johannsen</div>
                                    <div style="font-size: 12px; color: #94a3b8; margin-top: 2px;">Ático Skyview • 20 - 25 Oct</div>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <span class="status-tag tag-pending" style="font-size: 9px; padding: 4px 10px; background: #fef3c7; color: #92400e;">PENDIENTE</span>
                                <div style="margin-top: 8px; font-size: 14px; font-weight: 800;">$850</div>
                            </div>
                        </div>

                        <div class="reservation-list-item">
                            <div style="display: flex; gap: 1.5rem; align-items: center;">
                                <img src="https://images.unsplash.com/photo-1510798831971-661eb04b3739?auto=format&fit=crop&w=60&q=80" style="width: 52px; height: 52px; border-radius: 12px; object-fit: cover;">
                                <div>
                                    <div style="font-size: 15px; font-weight: 800;">Lucía Fernández</div>
                                    <div style="font-size: 12px; color: #94a3b8; margin-top: 2px;">Refugio Alpino • 02 - 05 Nov</div>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <span class="status-tag tag-confirmed" style="font-size: 9px; padding: 4px 10px;">CONFIRMADA</span>
                                <div style="margin-top: 8px; font-size: 14px; font-weight: 800;">€420</div>
                            </div>
                        </div>
                    </div>
                </section>

                <aside>
                    <!-- Las notificaciones ahora se muestran al dar clic en la campana (navbar.php -> notificaciones_modal.php) -->
                </aside>
            </div>
        </div>
    </div>

    <!-- Floating Button -->
    <div class="floating-add-btn" onclick="window.location.href='nueva-propiedad.php'" title="Nueva Propiedad - Subir al sistema">
        <i class="fa-solid fa-plus"></i>
    </div>
</body>
</html>
