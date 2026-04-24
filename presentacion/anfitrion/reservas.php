<?php
session_start();
require_once '../../datos/conexion.php';

$idHost = $_SESSION['idUsuario'] ?? 1; // 1 por defecto para pruebas

// 1. Consultar reservaciones de las propiedades del anfitrión
$sqlRes = "SELECT r.*, p.vNombre as nombrePropiedad, p.idPropiedad, u.vNombre as guestNombre, u.vApellido as guestApellido, u.vFoto as guestFoto
           FROM tbl_reserva r
           JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad
           JOIN tbl_usuarios u ON r.idUsuario = u.idUsuario
           WHERE p.idUsuario = ?
           ORDER BY r.dtFechaInicio DESC";
$stmtRes = $conexion->prepare($sqlRes);
$stmtRes->bind_param("i", $idHost);
$stmtRes->execute();
$reservas = $stmtRes->get_result();

// 2. Consultar comentarios de las propiedades del anfitrión
$sqlCom = "SELECT c.*, p.vNombre as nombrePropiedad, u.vNombre as guestNombre, u.vApellido as guestApellido, u.vFoto as guestFoto
           FROM tbl_comentarios c
           JOIN tbl_propiedad p ON c.idPropiedad = p.idPropiedad
           JOIN tbl_usuarios u ON c.idUsuario = u.idUsuario
           WHERE p.idUsuario = ?
           ORDER BY c.dtFechaRegistro DESC";
$stmtCom = $conexion->prepare($sqlCom);
$stmtCom->bind_param("i", $idHost);
$stmtCom->execute();
$comentarios = $stmtCom->get_result();

// 3. Calcular promedio (opcional pero bueno para los KPI)
$sqlAvg = "SELECT AVG(iCalificacion) as promedio, COUNT(*) as total FROM tbl_comentarios c JOIN tbl_propiedad p ON c.idPropiedad = p.idPropiedad WHERE p.idUsuario = ?";
$stmtAvg = $conexion->prepare($sqlAvg);
$stmtAvg->bind_param("i", $idHost);
$stmtAvg->execute();
$avgData = $stmtAvg->get_result()->fetch_assoc();
$promedio = round($avgData['promedio'] ?? 5.0, 1);
$totalComentarios = $avgData['total'] ?? 0;
?>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Reservas | Modo Anfitrión</title>
    <link rel="stylesheet" href="../../recursos/css/variables.css">
    <link rel="stylesheet" href="../../recursos/css/layouts/shared.css">
    <link rel="stylesheet" href="../../recursos/css/components/navbar.css">
    <link rel="stylesheet" href="../../recursos/css/anfitrion/host_main.css">
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
                    <li class="side-nav-item" onclick="window.location.href='dashboard.php'"><i class="fa-solid fa-house"></i> Inicio</li>
                    <li class="side-nav-item" onclick="window.location.href='propiedades.php'"><i class="fa-solid fa-building"></i> Propiedades</li>
                    <li class="side-nav-item" onclick="window.location.href='calendario.php'"><i class="fa-solid fa-calendar-days"></i> Calendario</li>
                    <li class="side-nav-item active" onclick="window.location.href='reservas.php'"><i class="fa-solid fa-receipt"></i> Reservas</li>
                </nav>
            </div>

            
        </aside>

        <!-- Main Content -->
        <main class="host-content-main">
            <?php include '../../recursos/navbar.php'; ?>
            
            <div style="padding: 2.5rem 4rem; max-width: 1600px; margin: 0 auto;">
                <header style="display: flex; justify-content: space-between; align-items: flex-end;">
                    <div>
                        <h1 style="font-size: 2rem; font-weight: 800; letter-spacing: -1px; margin-bottom: 0.5rem;">Gestión de Reservas</h1>
                        <p style="color: #64748b; font-size: 14px; max-width: 600px;">Supervise sus reservas entrantes y mantenga una comunicación fluida con sus huéspedes para asegurar una experiencia de cinco estrellas.</p>
                    </div>
                    <div style="display: flex; gap: 1rem;">
                        <button class="btn btn-primary" style="padding: 0.8rem 1.5rem; font-weight: 800; font-size: 13px; border-radius: 12px; background: var(--primary); color: white;"><i class="fa-solid fa-plus"></i> Nueva Reserva</button>
                    </div>
                </header>

                <!-- KPI Grid -->
                <section class="kpi-host-grid">
                    <div class="kpi-host-card">
                        <span class="label">Total Reservas</span>
                        <div class="value"><?php echo $reservas->num_rows; ?></div>
                    </div>
                    <div class="kpi-host-card">
                        <span class="label">Calificación Media</span>
                        <div class="value"><?php echo $promedio; ?> <i class="fa-solid fa-star" style="color: var(--primary); font-size: 1rem;"></i></div>
                    </div>
                    <div class="kpi-host-card">
                        <span class="label">Ocupación este mes</span>
                        <div class="value">88%</div>
                    </div>
                </section>

                <!-- Reservations Table -->
                <section class="admin-table-container" style="border-radius: 1.5rem; margin-top: 2rem;">
                    <div style="padding: 2rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; flex-wrap: wrap; gap: 1.5rem;">
                        <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--text-main);">Reservas Recientes</h3>
                        <div style="display: flex; gap: 1rem; font-size: 12px; font-weight: 800;">
                            <span onclick="filtrarReservas('Todas', this)" class="filtro-btn" style="color: white; background: var(--primary); padding: 8px 16px; border-radius: 99px; cursor: pointer;">Todas</span>
                            <span onclick="filtrarReservas('Confirmada', this)" class="filtro-btn" style="color: #64748b; background: #f8fafc; padding: 8px 16px; border-radius: 99px; cursor: pointer;">Confirmadas</span>
                            <span onclick="filtrarReservas('En curso', this)" class="filtro-btn" style="color: #64748b; background: #f8fafc; padding: 8px 16px; border-radius: 99px; cursor: pointer;">En curso</span>
                            <span onclick="filtrarReservas('Finalizada', this)" class="filtro-btn" style="color: #64748b; background: #f8fafc; padding: 8px 16px; border-radius: 99px; cursor: pointer;">Finalizadas</span>
                            <span onclick="filtrarReservas('Cancelada', this)" class="filtro-btn" style="color: #64748b; background: #f8fafc; padding: 8px 16px; border-radius: 99px; cursor: pointer;">Canceladas</span>
                        </div>
                    </div>
                    
                    <div style="overflow-x: auto;">
                        <table class="table-v2">
                            <thead>
                                <tr style="background: #f8fafc;">
                                    <th>Cliente</th>
                                    <th>Propiedad</th>
                                    <th>Fechas</th>
                                    <th>Estado</th>
                                    <th>Total</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($reservas->num_rows > 0): ?>
                                    <?php while ($res = $reservas->fetch_assoc()): ?>
                                        <?php 
                                            $fIni = new DateTime($res['dtFechaInicio']);
                                            $fFin = new DateTime($res['dtFechaFin']);
                                            $hoy = new DateTime();
                                            $status = "Confirmada";
                                            $stBg = "#d1fae5"; $stColor = "#065f46";
                                            if (isset($res['vEstatus']) && (strtoupper($res['vEstatus']) === 'CANCELADA' || strtoupper($res['vEstatus']) === 'CANCELADO')) {
                                                $status = "Cancelada";
                                                $stBg = "#fee2e2"; $stColor = "#991b1b";
                                            } elseif (isset($res['vEstado']) && (strtoupper($res['vEstado']) === 'CANCELADA' || strtoupper($res['vEstado']) === 'CANCELADO')) {
                                                $status = "Cancelada";
                                                $stBg = "#fee2e2"; $stColor = "#991b1b";
                                            } elseif ($hoy >= $fIni && $hoy <= $fFin) {
                                                $status = "En curso";
                                                $stBg = "#dbeafe"; $stColor = "#1e40af";
                                            } elseif ($hoy > $fFin) {
                                                $status = "Finalizada";
                                                $stBg = "#f1f5f9"; $stColor = "#64748b";
                                            }
                                        ?>
                                        <tr class="reserva-row" data-status="<?php echo $status; ?>">
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 1rem;">
                                                    <img src="<?php echo !empty($res['guestFoto']) ? '../../' . $res['guestFoto'] : 'https://i.pravatar.cc/100?u=' . $res['idUsuario']; ?>" style="width: 40px; height: 40px; border-radius: 12px; object-fit: cover;">
                                                    <div>
                                                        <div style="font-size: 14px; font-weight: 800; color: var(--text-main);"><?php echo htmlspecialchars($res['guestNombre'] . ' ' . $res['guestApellido']); ?></div>
                                                        <div style="font-size: 11px; color: #64748b; font-weight: 600;">Huésped registrado</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="font-size: 14px; font-weight: 700; color: var(--text-main);"><?php echo htmlspecialchars($res['nombrePropiedad']); ?></div>
                                            </td>
                                            <td>
                                                <div style="font-size: 14px; font-weight: 700; color: var(--text-main);"><?php echo $fIni->format('d M') . ' - ' . $fFin->format('d M'); ?></div>
                                                <div style="font-size: 11px; color: #64748b; font-weight: 600;"><?php echo $fIni->diff($fFin)->days; ?> noches</div>
                                            </td>
                                            <td><span class="status-tag" style="background: <?php echo $stBg; ?>; color: <?php echo $stColor; ?>;"><?php echo $status; ?></span></td>
                                            <td><strong style="font-size: 15px; color: var(--primary);">$<?php echo number_format($res['dTotalReserva'], 0); ?></strong></td>
                                            <td>
                                                <div style="display: flex; gap: 0.5rem;">
                                                    <button style="border: none; background: #f1f5f9; padding: 8px; border-radius: 8px; color: #64748b; cursor: pointer;">
                                                        <i class="fa-solid fa-ellipsis"></i>
                                                    </button>
                                                    <?php if ($status !== 'Cancelada' && $status !== 'Finalizada'): ?>
                                                    <button onclick="cancelarReserva(<?php echo $res['idReserva']; ?>, 'anfitrion', <?php echo $idHost; ?>)" title="Cancelar Reserva" style="border: none; background: #fee2e2; padding: 8px; border-radius: 8px; color: #dc2626; cursor: pointer;">
                                                        <i class="fa-solid fa-ban"></i>
                                                    </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 3rem; color: #94a3b8;">No hay reservas registradas.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Reviews Section -->
                <section style="margin-top: 5rem;">
                    <header style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 3rem; flex-wrap: wrap; gap: 1.5rem;">
                         <div>
                            <h2 style="font-size: 2rem; font-weight: 800; color: var(--text-main); letter-spacing: -1px;">Reseñas de Huéspedes</h2>
                            <p style="color: #64748b; font-size: 15px; font-weight: 500; margin-top: 0.5rem;">Gestione el feedback de sus clientes y mejore su reputación.</p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--primary); font-weight: 800; background: #f0f4ff; padding: 10px 20px; border-radius: 12px;">
                            <?php for($i=0; $i<round($promedio); $i++): ?>
                                <i class="fa-solid fa-star"></i>
                            <?php endfor; ?>
                            <span style="font-size: 15px; margin-left: 10px;"><?php echo $promedio; ?> / 5.0</span>
                        </div>
                    </header>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem;">
                        <?php if ($comentarios->num_rows > 0): ?>
                            <?php while ($com = $comentarios->fetch_assoc()): ?>
                                <div class="review-card">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem;">
                                        <div style="display: flex; gap: 1rem;">
                                            <img src="<?php echo !empty($com['guestFoto']) ? '../../' . $com['guestFoto'] : 'https://i.pravatar.cc/100?u=' . $com['idUsuario']; ?>" style="width: 48px; height: 48px; border-radius: 14px; object-fit: cover;">
                                            <div>
                                                <div style="font-size: 15px; font-weight: 800; color: var(--text-main);"><?php echo htmlspecialchars($com['guestNombre'] . ' ' . $com['guestApellido']); ?></div>
                                                <div style="font-size: 12px; color: #94a3b8; font-weight: 600;"><?php echo htmlspecialchars($com['nombrePropiedad']); ?> • <?php echo date('M Y', strtotime($com['dtFechaRegistro'])); ?></div>
                                            </div>
                                        </div>
                                        <div style="color: var(--primary); font-size: 12px; display: flex; gap: 2px;">
                                            <?php for($i=0; $i<$com['iCalificacion']; $i++): ?>
                                                <i class="fa-solid fa-star"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <p style="font-style: italic; color: #475569; font-size: 15px; line-height: 1.7; margin-bottom: 2rem; background: #fcfdfe; padding: 1rem; border-radius: 12px;">"<?php echo htmlspecialchars($com['vComentario']); ?>"</p>
                                    <div style="text-align: right; border-top: 1px solid #f1f5f9; padding-top: 1.5rem;">
                                        <a href="#" style="font-size: 13px; font-weight: 800; color: var(--primary); text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">Responder comentario <i class="fa-solid fa-reply"></i></a>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div style="grid-column: 1 / -1; text-align: center; padding: 4rem; background: white; border-radius: 1.5rem; color: #94a3b8;">
                                <i class="fa-regular fa-comments" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                                Aún no has recibido comentarios en tus propiedades.
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        </main>
    </div>
    <script>
    function filtrarReservas(estado, btn) {
        // Actualizar estilos visuales de los botones
        const botones = document.querySelectorAll('.filtro-btn');
        botones.forEach(b => {
            b.style.color = '#64748b';
            b.style.background = '#f8fafc';
        });
        btn.style.color = 'white';
        btn.style.background = 'var(--primary)';

        // Mostrar/Ocultar filas
        const filas = document.querySelectorAll('.reserva-row');
        filas.forEach(fila => {
            if (estado === 'Todas' || fila.getAttribute('data-status') === estado) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });
    }

    function cancelarReserva(idReserva, role, idUsuario) {
        if (confirm("¿Estás seguro de que deseas cancelar esta reserva?")) {
            const formData = new FormData();
            formData.append('idReserva', idReserva);
            formData.append('role', role);
            formData.append('idUsuario', idUsuario);

            fetch('../../apis/cancelar_reserva.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.ok) {
                    alert("Reserva cancelada exitosamente.");
                    window.location.reload();
                } else {
                    alert("Error: " + data.mensaje);
                }
            })
            .catch(err => {
                console.error(err);
                alert("Ocurrió un problema de red al intentar cancelar.");
            });
        }
    }
    </script>
</body>
</html>
