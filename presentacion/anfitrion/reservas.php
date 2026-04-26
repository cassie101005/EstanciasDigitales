<?php
require_once '../../negocio/auth/verificar_sesion.php';
validarSesion('anfitrion', '../../');
require_once '../../datos/conexion.php';

$idHost = $_SESSION['idUsuario'] ?? 1; // 1 por defecto para pruebas

require_once '../../negocio/anfitrion/reservas_view.php';

$reservas = getHostReservas($idHost, $conexion);
$comentarios = getHostComentarios($idHost, $conexion);
$stats = getHostStats($idHost, $conexion);

$promedio = $stats['promedio'];
$totalComentarios = $stats['total'];
?>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Reservas | Modo Anfitrión</title>
    <link rel="icon" type="image/png" href="../../recursos/img/logo.png">
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
                </header>

                <!-- KPI Grid -->
                <section class="kpi-host-grid">
                    <div class="kpi-host-card">
                        <span class="label">Total Reservas</span>
                        <div class="value"><?php echo count($reservas); ?></div>
                    </div>
                    <div class="kpi-host-card">
                        <span class="label">Calificación Media</span>
                        <div class="value"><?php echo $promedio; ?> <i class="fa-solid fa-star" style="color: var(--primary); font-size: 1rem;"></i></div>
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
                                    <th>Detalles</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($reservas) > 0): ?>
                                    <?php foreach ($reservas as $res): ?>
                                        <?php 
                                            $fIni = new DateTime($res['dtFechaInicio']);
                                            $fFin = new DateTime($res['dtFechaFin']);
                                            $hoy = new DateTime();
                                            $status = "Confirmada";
                                            $stBg = "#d1fae5"; $stColor = "#065f46";
                                            if (isset($res['vEstatus']) && strtoupper($res['vEstatus']) === 'PENDIENTE CANCELACION') {
                                                $status = "Pendiente Cancelación";
                                                $stBg = "#fef08a"; $stColor = "#854d0e";
                                            } elseif (isset($res['vEstatus']) && (strtoupper($res['vEstatus']) === 'CANCELADA' || strtoupper($res['vEstatus']) === 'CANCELADO')) {
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
                                            
                                            $obs = isset($res['vObservaciones']) ? htmlspecialchars(str_replace("\n", ' ', $res['vObservaciones']), ENT_QUOTES) : '';
                                            if ($status === 'Cancelada') {
                                                if (!empty($res['motivoCancelacionReal'])) {
                                                    $obs = htmlspecialchars(str_replace("\n", ' ', $res['motivoCancelacionReal']), ENT_QUOTES);
                                                } else {
                                                    $obs = "Cancelada sin motivo especificado.";
                                                }
                                            }
                                            
                                            $jsObs = addslashes(html_entity_decode($obs, ENT_QUOTES));
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
                                                    <?php if ($status !== 'Cancelada' && $status !== 'Finalizada' && $status !== 'Pendiente Cancelación'): ?>
                                                    <button onclick="cancelarReserva(<?php echo $res['idReserva']; ?>, 'anfitrion', <?php echo $idHost; ?>)" title="Cancelar Reserva" style="border: none; background: #fee2e2; padding: 8px; border-radius: 8px; color: #dc2626; cursor: pointer;">
                                                        <i class="fa-solid fa-ban"></i>
                                                    </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($status === 'Pendiente Cancelación'): ?>
                                                    <button onclick="verSolicitudCancelacion(<?php echo $res['idReserva']; ?>, '<?php echo $jsObs; ?>', <?php echo $idHost; ?>, '<?php echo $res['dtFechaInicio']; ?>', <?php echo $res['dTotalReserva']; ?>)" style="background: #fef08a; border: 1px solid #eab308; color: #854d0e; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;">
                                                        <i class="fa-solid fa-envelope-open-text"></i> Ver Solicitud
                                                    </button>
                                                <?php else: ?>
                                                    <button onclick="verDetallesGenerales(<?php echo $res['idReserva']; ?>, '<?php echo $status; ?>', '<?php echo $jsObs; ?>', <?php echo htmlspecialchars(json_encode(['reembolso' => $res['reembolso'] ?? 0, 'penalizacion' => $res['penalizacion'] ?? 0, 'tipo' => $res['tipoCancelacion'] ?? '']), ENT_QUOTES); ?>)" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #475569; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;">
                                                        <i class="fa-solid fa-circle-info"></i> Ver Detalles
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 3rem; color: #94a3b8;">No hay reservas registradas.</td>
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

                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 2rem;">
                        <?php if (count($comentarios) > 0): ?>
                            <?php foreach ($comentarios as $com): ?>
                                <div class="review-card">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem;">
                                        <div style="display: flex; gap: 1rem;">
                                            <img src="<?php echo !empty($com['guestFoto']) ? '../../' . $com['guestFoto'] : 'https://i.pravatar.cc/100?u=' . $com['idUsuario']; ?>" style="width: 48px; height: 48px; border-radius: 14px; object-fit: cover;">
                                            <div>
                                                <div style="font-size: 15px; font-weight: 800; color: var(--text-main);"><?php echo htmlspecialchars($com['guestNombre'] . ' ' . $com['guestApellido']); ?></div>
                                                <div style="font-size: 12px; color: #94a3b8; font-weight: 600;"><?php echo htmlspecialchars($com['nombrePropiedad']); ?> • <?php echo date('M Y', strtotime($com['fecha'])); ?></div>
                                            </div>
                                        </div>
                                        <div style="color: var(--primary); font-size: 12px; display: flex; gap: 2px;">
                                            <?php 
                                            $calif = intval($com['iCalificacion']);
                                            for($i=0; $i<$calif; $i++): ?>
                                                <i class="fa-solid fa-star"></i>
                                            <?php endfor; 
                                            for($i=$calif; $i<5; $i++): ?>
                                                <i class="fa-regular fa-star" style="opacity: 0.3;"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <p style="font-size: 14px; color: #475569; line-height: 1.6; margin: 0 0 1.5rem 0; font-style: italic;">"<?php echo htmlspecialchars($com['vComentario']); ?>"</p>
                                    <div id="respContainer_<?php echo $com['tipo']; ?>_<?php echo $com['id']; ?>">
                                        <?php if (!empty($com['vRespuesta'])): ?>
                                            <div style="background: #f1f5f9; padding: 1.25rem; border-radius: 12px; margin-bottom: 1.5rem; border-left: 4px solid var(--primary);">
                                                <div style="font-size: 11px; font-weight: 800; color: var(--primary); text-transform: uppercase; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 6px;">
                                                    <i class="fa-solid fa-reply-all"></i> Tu Respuesta
                                                </div>
                                                <p style="font-size: 14px; color: #475569; line-height: 1.5; margin: 0;"><?php echo htmlspecialchars($com['vRespuesta']); ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div style="text-align: right; border-top: 1px solid #f1f5f9; padding-top: 1.5rem;">
                                        <a href="javascript:void(0)" onclick="abrirModalRespuesta('<?php echo $com['tipo']; ?>', <?php echo $com['id']; ?>, '<?php echo addslashes(htmlspecialchars($com['guestNombre'])); ?>')" style="font-size: 13px; font-weight: 800; color: var(--primary); text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                                            <?php echo empty($com['vRespuesta']) ? 'Responder comentario' : 'Editar respuesta'; ?> 
                                            <i class="fa-solid fa-reply"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
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

            <div style="background: #f1f5f9; padding: 1rem; border-radius: 12px; margin-bottom: 2rem;">
                <h4 style="font-size: 12px; font-weight: 800; color: #475569; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 6px;"><i class="fa-solid fa-circle-info" style="color: var(--primary);"></i> Política de Cancelación</h4>
                <?php
                $politicas = getPoliticasCancelacion($conexion);
                if (count($politicas) > 0) {
                    foreach($politicas as $rowPol) {
                        echo '<div style="margin-bottom: 0.5rem;">';
                        echo '<strong style="font-size: 12px; color: #475569;">' . htmlspecialchars($rowPol['vNombreOpcion']) . '</strong><br>';
                        echo '<span style="font-size: 12px; color: #64748b;">' . htmlspecialchars($rowPol['vDescripcion']) . '</span>';
                        echo '</div>';
                    }
                } else {
                    echo '<p style="font-size: 12px; color: #64748b; margin: 0;">No hay políticas de cancelación definidas.</p>';
                }
                ?>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button onclick="cerrarModalCancelacion()" style="flex: 1; padding: 0.875rem; border: 1px solid #cbd5e1; background: white; color: #475569; border-radius: 12px; font-weight: 700; cursor: pointer;">Volver</button>
                <button onclick="confirmarCancelacion()" style="flex: 1; padding: 0.875rem; border: none; background: #dc2626; color: white; border-radius: 12px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);">Confirmar la cancelación</button>
            </div>
        </div>
    </div>

    <!-- Approve Cancelation / Details Modal -->
    <div id="modalAprobarCancelacion" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 20px; width: 90%; max-width: 450px; padding: 2rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); position: relative;">
            <div style="position: absolute; top: 1.5rem; right: 1.5rem; cursor: pointer; color: #94a3b8; font-size: 1.2rem;" onclick="cerrarModalAprobar()">
                <i class="fa-solid fa-xmark"></i>
            </div>
            
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                <div id="modalIconContainer" style="width: 48px; height: 48px; border-radius: 12px; background: #fef08a; color: #854d0e; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i id="modalIcon" class="fa-solid fa-envelope-open-text"></i>
                </div>
                <div>
                    <h3 id="modalDetallesTitle" style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0;">Solicitud de Cancelación</h3>
                    <p id="modalDetallesSubtitle" style="font-size: 13px; color: #64748b; margin: 0; margin-top: 4px;">El huésped desea cancelar su reserva</p>
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label id="modalDetallesLabel" style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 0.5rem;">Motivo reportado por el huésped</label>
                <div id="motivoSolicitud" style="width: 100%; padding: 1rem; border-radius: 12px; border: 1px solid #cbd5e1; font-size: 14px; background: #f8fafc; color: #475569; min-height: 80px;"></div>
            </div>

            <div id="hostSettlementBox" style="background: #f1f5f9; padding: 1.25rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid #e2e8f0; display: none;">
                <h4 style="font-size: 13px; font-weight: 800; color: #0f172a; margin: 0 0 0.75rem 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-scale-balanced" style="color: var(--primary);"></i> Política y Liquidación
                </h4>
                
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div style="padding: 0.75rem; border-radius: 8px; background: white; border-left: 4px solid #64748b;" id="hostSettlementIndicator">
                        <p style="margin: 0; font-size: 13px; font-weight: 700; color: #0f172a;" id="hostSettlementStatus">Calculando...</p>
                        <p style="margin: 4px 0 0 0; font-size: 12px; color: #64748b;" id="hostSettlementDetail">Políticas de cancelación de Estancias Digitales.</p>
                    </div>

                    <div style="display: flex; justify-content: space-between; font-size: 13px; color: #64748b; padding: 0 4px;">
                        <span>Reembolso al huésped:</span>
                        <span style="font-weight: 700; color: #0f172a;" id="guestRefundTextHost">$0.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px; color: #0f172a; padding: 8px 4px 0 4px; border-top: 1px dashed #cbd5e1;">
                        <span style="font-weight: 800;">Tu ganancia (10%):</span>
                        <span style="font-weight: 800; color: #10b981;" id="hostEarningsText">$0.00</span>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button onclick="cerrarModalAprobar()" style="flex: 1; padding: 0.875rem; border: 1px solid #cbd5e1; background: white; color: #475569; border-radius: 12px; font-weight: 700; cursor: pointer;">Cerrar</button>
                <button id="btnAprobarCancelacionHost" onclick="aprobarCancelacion()" style="flex: 1; padding: 0.875rem; border: none; background: #dc2626; color: white; border-radius: 12px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);">Confirmar Cancelación</button>
            </div>
        </div>
    </div>

    <script src="../../recursos/js/anfitrion/reservas.js"></script>

    <!-- Modal Respuesta -->
    <div id="modalRespuesta" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 2000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 20px; width: 90%; max-width: 500px; padding: 2.5rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
                <div style="width: 54px; height: 54px; border-radius: 14px; background: #f0f4ff; color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fa-solid fa-reply"></i>
                </div>
                <div>
                    <h3 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0;">Responder a <span id="respGuestName"></span></h3>
                    <p style="font-size: 14px; color: #64748b; margin: 0.25rem 0 0 0;">Mantén una actitud cordial y profesional.</p>
                </div>
            </div>

            <textarea id="txtRespuesta" style="width: 100%; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; font-family: inherit; font-size: 15px; resize: vertical; min-height: 150px; outline: none; background: #f8fafc; transition: all 0.2s;" placeholder="Escribe tu respuesta aquí..."></textarea>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button onclick="cerrarModalRespuesta()" style="flex: 1; padding: 0.875rem; border: 1px solid #e2e8f0; background: white; color: #475569; border-radius: 12px; font-weight: 700; cursor: pointer; transition: all 0.2s;">Cancelar</button>
                <button onclick="enviarRespuesta()" style="flex: 1; padding: 0.875rem; border: none; background: var(--primary); color: white; border-radius: 12px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 12px rgba(124, 58, 237, 0.2); transition: all 0.2s;">Enviar Respuesta</button>
            </div>
        </div>
    </div>
</body>
</html>
