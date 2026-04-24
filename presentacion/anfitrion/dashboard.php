<?php
session_start();
require_once '../../datos/conexion.php';
$idHost = $_SESSION['idUsuario'] ?? 1;

// Obtener las reservas recientes
$sqlNotif = "SELECT r.idReserva, r.dtFechaInicio, r.dtFechaFin, r.vEstatus, r.dTotalReserva, u.vNombre, u.vApellido, u.vFoto, p.vNombre as propiedad
             FROM tbl_reserva r 
             JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad 
             JOIN tbl_usuarios u ON r.idUsuario = u.idUsuario
             WHERE p.idUsuario = ? 
             ORDER BY r.idReserva DESC LIMIT 5";
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
                      <div style="max-width: 420px; margin-top: 10px;">
    
    <p style="
        color: #64748b;
        font-size: 15px;
        line-height: 1.7;
        font-weight: 400;
        margin: 0;
    ">
        Optimiza tu perfil y mantén una comunicación clara con tus huéspedes.
    </p>

    <p style="
        color: #0f172a;
        font-size: 15px;
        font-weight: 600;
        margin-top: 6px;
    ">
        Brinda una experiencia excepcional ✨
    </p>

</div>
                    </header>

    

                    <!-- Recent Reservations -->
                    <div style="margin-top: 4rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                            <h3 style="font-size: 1.5rem; font-weight: 800; letter-spacing: -1px;">Reservas recientes</h3>
                            <a href="reservas.php" style="font-size: 13px; font-weight: 800; color: var(--primary); text-decoration: none;">Ver todas</a>
                        </div>

                        <?php if (count($notificaciones) > 0): ?>
                            <?php foreach ($notificaciones as $notif): 
                                $fIni = new DateTime($notif['dtFechaInicio']);
                                $fFin = new DateTime($notif['dtFechaFin']);
                                
                                $status = "Confirmada";
                                $stStyle = "font-size: 9px; padding: 4px 10px; background: #d1fae5; color: #065f46; border-radius: 99px;";
                                if (isset($notif['vEstatus']) && strtoupper($notif['vEstatus']) === 'PENDIENTE CANCELACION') {
                                    $status = "Pendiente";
                                    $stStyle = "font-size: 9px; padding: 4px 10px; background: #fef08a; color: #854d0e; border-radius: 99px;";
                                } elseif (isset($notif['vEstatus']) && strtoupper($notif['vEstatus']) === 'CANCELADA') {
                                    $status = "Cancelada";
                                    $stStyle = "font-size: 9px; padding: 4px 10px; background: #fee2e2; color: #991b1b; border-radius: 99px;";
                                }
                                $imgSrc = !empty($notif['vFoto']) ? '../../' . $notif['vFoto'] : 'https://i.pravatar.cc/100?u=' . $notif['idReserva'];
                            ?>
                                <div class="reservation-list-item">
                                    <div style="display: flex; gap: 1.5rem; align-items: center;">
                                        <img src="<?php echo htmlspecialchars($imgSrc); ?>" style="width: 52px; height: 52px; border-radius: 12px; object-fit: cover;">
                                        <div>
                                            <div style="font-size: 15px; font-weight: 800;"><?php echo htmlspecialchars($notif['vNombre'] . ' ' . $notif['vApellido']); ?></div>
                                            <div style="font-size: 12px; color: #94a3b8; margin-top: 2px;"><?php echo htmlspecialchars($notif['propiedad']); ?> • <?php echo $fIni->format('d M') . ' - ' . $fFin->format('d M'); ?></div>
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <span class="status-tag" style="<?php echo $stStyle; ?>"><?php echo strtoupper($status); ?></span>
                                        <div style="margin-top: 8px; font-size: 14px; font-weight: 800;">$<?php echo number_format($notif['dTotalReserva'], 0); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div style="text-align: center; padding: 3rem; color: #94a3b8; background: white; border-radius: 16px;">
                                <i class="fa-solid fa-receipt" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                                No hay reservas recientes registradas.
                            </div>
                        <?php endif; ?>
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
