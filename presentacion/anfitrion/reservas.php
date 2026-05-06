<?php
require_once '../../negocio/auth/verificar_sesion.php';
validarSesion('anfitrion', '../../');
require_once '../../datos/conexion.php';
require_once '../../negocio/utilidades/seguridad.php';

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
    <link rel="stylesheet" href="../../recursos/css/main.css">
    <link rel="stylesheet" href="../../recursos/css/layouts/shared.css">
    <link rel="stylesheet" href="../../recursos/css/components/navbar.css">
    <link rel="stylesheet" href="../../recursos/css/anfitrion/host_main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
<body class="host-body">
    <div class="host-wrapper">
        <?php include '../../recursos/sidebar-host.php'; ?>

        <!-- Main Content -->
        <main class="host-content-main">
            <?php include '../../recursos/navbar.php'; ?>
            <input type="hidden" id="global_csrf_token" value="<?php echo generarTokenCSRF(); ?>">
            
            <div class="host-dashboard-container">
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
                            <button data-filter="todas" class="filter-btn active" style="color: white; background: var(--primary); padding: 8px 16px; border-radius: 99px; border: none; cursor: pointer; font-size: 12px; font-weight: 800;">Todas</button>
                            <button data-filter="confirmada" class="filter-btn" style="color: #64748b; background: #f8fafc; padding: 8px 16px; border-radius: 99px; border: none; cursor: pointer; font-size: 12px; font-weight: 800;">Confirmadas</button>
                            <button data-filter="en-curso" class="filter-btn" style="color: #64748b; background: #f8fafc; padding: 8px 16px; border-radius: 99px; border: none; cursor: pointer; font-size: 12px; font-weight: 800;">En curso</button>
                            <button data-filter="finalizada" class="filter-btn" style="color: #64748b; background: #f8fafc; padding: 8px 16px; border-radius: 99px; border: none; cursor: pointer; font-size: 12px; font-weight: 800;">Finalizadas</button>
                            <button data-filter="cancelada" class="filter-btn" style="color: #64748b; background: #f8fafc; padding: 8px 16px; border-radius: 99px; border: none; cursor: pointer; font-size: 12px; font-weight: 800;">Canceladas</button>
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
                            <tbody id="reservasTableBody">
                                <?php if (count($reservas) > 0): ?>
                                    <?php foreach ($reservas as $res): ?>
                                        <?php 
                                            require_once '../../negocio/utilidades/helper_reservas.php';
                                            $statusInfo = obtenerEstadoReserva($res);
                                            $status = $statusInfo['label'];
                                            $stBg = $statusInfo['bgColor'];
                                            $stColor = $statusInfo['color'];
                                            
                                            $fIni = new DateTime($res['dtFechaInicio']);
                                            $fFin = new DateTime($res['dtFechaFin']);
                                            
                                            // Caso especial para host: Pendiente Cancelación (si existe en BD)
                                            $isPendiente = (isset($res['vEstatus']) && strtoupper($res['vEstatus']) === 'PENDIENTE CANCELACION');
                                            if ($isPendiente) {
                                                $status = "Pendiente Cancelación";
                                                $stBg = "#FEF3C7"; $stColor = "#92400E";
                                            }

                                            // Slug normalizado que debe coincidir con data-filter de los botones
                                            $slugMap = [
                                                'Confirmada'            => 'confirmada',
                                                'En curso'              => 'en-curso',
                                                'Finalizada'            => 'finalizada',
                                                'Cancelada'             => 'cancelada',
                                                'Pendiente Cancelación' => 'pendiente-cancelacion',
                                            ];
                                            $slug = $slugMap[$status] ?? 'confirmada';

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
                                        <tr class="reserva-row" data-estado="<?php echo $slug; ?>">
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
                                            <td>
                                                <?php echo renderizarBadgeEstado($res); ?>
                                            </td>
                                            <td><strong style="font-size: 15px; color: var(--primary);">$<?php echo number_format($res['dTotalReserva'], 0); ?></strong></td>
                                            <td>
                                                <div style="display: flex; gap: 0.5rem;">
                                                    <?php if ($status !== 'Cancelada' && $status !== 'Finalizada' && $status !== 'Pendiente Cancelación'): ?>
                                                    <button onclick="cancelarReserva(<?php echo $res['idReserva']; ?>, 'anfitrion', <?php echo $idHost; ?>, '<?php echo $res['dtFechaRegistro']; ?>', <?php echo $res['dTotalReserva']; ?>)" title="Cancelar Reserva" style="border: none; background: #fee2e2; padding: 8px; border-radius: 8px; color: #dc2626; cursor: pointer;">
                                                        <i class="fa-solid fa-ban"></i>
                                                    </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($status === 'Pendiente Cancelación'): ?>
                                                    <button onclick="verSolicitudCancelacion(<?php echo $res['idReserva']; ?>, '<?php echo $jsObs; ?>', <?php echo $idHost; ?>, '<?php echo $res['dtFechaRegistro']; ?>', <?php echo $res['dTotalReserva']; ?>)" style="background: #fef08a; border: 1px solid #eab308; color: #854d0e; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;">
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

                    <!-- Pagination Container -->
                    <div id="paginationContainer" style="padding: 1.5rem 2rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: center; align-items: center; gap: 0.5rem;">
                        <!-- JS generated -->
                    </div>
                </section>

                <style>
                .pendiente-cancelacion-badge {
                    display: inline-block;
                    padding: 6px 12px;
                    border-radius: 12px;
                    font-weight: 700;
                    text-align: center;
                    line-height: 1.1;
                    background: #FEF3C7;
                    color: #92400E;
                    font-size: 0.7rem;
                    text-transform: none !important;
                }
                .pendiente-cancelacion-badge small {
                    font-size: 0.65rem;
                    opacity: 0.8;
                    font-weight: 600;
                }
                .pagination-btn {
                    padding: 8px 16px;
                    border-radius: 10px;
                    border: 1px solid #e2e8f0;
                    background: white;
                    color: #64748b;
                    font-size: 13px;
                    font-weight: 700;
                    cursor: pointer;
                    transition: all 0.2s;
                    text-decoration: none;
                }
                .pagination-btn:hover {
                    background: #f8fafc;
                    border-color: #cbd5e1;
                }
                .pagination-btn.active {
                    background: var(--primary);
                    color: white;
                    border-color: var(--primary);
                }
                .pagination-btn.disabled {
                    opacity: 0.5;
                    cursor: not-allowed;
                    pointer-events: none;
                }
                </style>
                </section>

                <!-- Reviews Section -->
                <section id="reseñas" style="margin-top: 5rem;">
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

                    <div style="background: white; border-radius: 24px; border: 1px solid #f1f5f9; overflow: hidden; box-shadow: var(--shadow-sm);">
                        <div style="overflow-x: auto; scrollbar-width: none;">
                            <table style="width: 100%; border-collapse: collapse; min-width: 1000px;">
                                <thead>
                                    <tr style="background: #f8fafc; border-bottom: 1px solid #f1f5f9;">
                                        <th style="padding: 1.25rem 2rem; text-align: left; font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1.5px;">Huésped</th>
                                        <th style="padding: 1.25rem 2rem; text-align: left; font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1.5px;">Propiedad</th>
                                        <th style="padding: 1.25rem 2rem; text-align: left; font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1.5px;">Calificación</th>
                                        <th style="padding: 1.25rem 2rem; text-align: left; font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1.5px;">Comentario</th>
                                        <th style="padding: 1.25rem 2rem; text-align: left; font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1.5px;">Respuesta</th>
                                        <th style="padding: 1.25rem 2rem; text-align: left; font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1.5px;">Fecha</th>
                                        <th style="padding: 1.25rem 2rem; text-align: center; font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1.5px;">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="reviewsTableBody">
                                    <?php if (count($comentarios) > 0): ?>
                                        <?php foreach ($comentarios as $com): ?>
                                            <tr class="review-row" style="border-bottom: 1px solid #f8fafc; transition: all 0.2s;">
                                                <td style="padding: 1.25rem 2rem;">
                                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                                        <img src="<?php echo !empty($com['guestFoto']) ? '../../' . $com['guestFoto'] : 'https://i.pravatar.cc/100?u=' . $com['idUsuario']; ?>" style="width: 38px; height: 38px; border-radius: 10px; object-fit: cover;">
                                                        <span style="font-size: 14px; font-weight: 700; color: #0f172a;"><?php echo htmlspecialchars($com['guestNombre'] . ' ' . $com['guestApellido']); ?></span>
                                                    </div>
                                                </td>
                                                <td style="padding: 1.25rem 2rem;">
                                                    <span style="font-size: 13px; font-weight: 600; color: #475569;"><?php echo htmlspecialchars($com['nombrePropiedad']); ?></span>
                                                </td>
                                                <td style="padding: 1.25rem 2rem;">
                                                    <div style="color: #f59e0b; font-size: 12px; display: flex; gap: 2px;">
                                                        <?php 
                                                        $calif = intval($com['iCalificacion']);
                                                        for($i=0; $i<$calif; $i++): ?><i class="fa-solid fa-star"></i><?php endfor; 
                                                        for($i=$calif; $i<5; $i++): ?><i class="fa-regular fa-star" style="opacity: 0.3;"></i><?php endfor; ?>
                                                    </div>
                                                </td>
                                                <td style="padding: 1.25rem 2rem;">
                                                    <div style="font-size: 13px; color: #64748b; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($com['vComentario']); ?>">
                                                        "<?php echo htmlspecialchars($com['vComentario']); ?>"
                                                    </div>
                                                </td>
                                                <td style="padding: 1.25rem 2rem;">
                                                    <div id="respCell_<?php echo $com['tipo']; ?>_<?php echo $com['id']; ?>" style="font-size: 13px; color: <?php echo !empty($com['vRespuesta']) ? 'var(--primary)' : '#cbd5e1'; ?>; font-weight: 600;">
                                                        <?php if (!empty($com['vRespuesta'])): ?>
                                                            <div style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($com['vRespuesta']); ?>">
                                                                <i class="fa-solid fa-reply"></i> <?php echo htmlspecialchars($com['vRespuesta']); ?>
                                                            </div>
                                                        <?php else: ?>
                                                            <span style="font-style: italic; font-weight: 500;">Sin respuesta</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td style="padding: 1.25rem 2rem;">
                                                    <span style="font-size: 12px; font-weight: 600; color: #94a3b8;"><?php echo date('d/m/Y', strtotime($com['fecha'])); ?></span>
                                                </td>
                                                <td style="padding: 1.25rem 2rem; text-align: center;">
                                                    <button onclick="abrirModalRespuesta('<?php echo $com['tipo']; ?>', <?php echo $com['id']; ?>, '<?php echo addslashes(htmlspecialchars($com['guestNombre'])); ?>')" 
                                                            style="background: #f8fafc; border: 1px solid #cbd5e1; color: #475569; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; transition: all 0.2s;">
                                                        <i class="fa-solid fa-reply"></i> <?php echo empty($com['vRespuesta']) ? 'Responder' : 'Editar'; ?>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" style="text-align: center; padding: 4rem; color: #94a3b8;">No has recibido comentarios aún.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Reviews Pagination -->
                        <div id="reviewsPagination" style="padding: 1.5rem 2rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: center; align-items: center; gap: 0.5rem;">
                            <!-- JS generated -->
                        </div>
                    </div>
                </section>
            </div>
        </main>
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

            <div style="background: #f8fafc; padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0; margin-bottom: 1.5rem;">
                <h4 style="font-size: 13px; font-weight: 800; color: #475569; margin: 0 0 0.75rem 0; display: flex; align-items: center; gap: 8px;"><i class="fa-solid fa-circle-info" style="color: var(--primary);"></i> Política de Liquidación</h4>
                
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div style="padding: 0.75rem; border-radius: 10px; background: white; border-left: 4px solid #64748b;" id="hostCancelIndicator">
                        <p style="margin: 0; font-size: 13px; font-weight: 700; color: #0f172a;" id="hostCancelStatus">Calculando...</p>
                        <p style="margin: 4px 0 0 0; font-size: 12px; color: #64748b; line-height: 1.4;" id="hostCancelDetail">Cargando información de la reserva...</p>
                    </div>

                    <div style="display: flex; justify-content: space-between; font-size: 12px; color: #64748b; padding: 0 4px; font-weight: 500;">
                        <span>Reembolso al huésped:</span>
                        <span style="font-weight: 700; color: #0f172a;" id="guestRefundTextHostInitiated">$0.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px; color: #0f172a; padding: 0.75rem 4px 0 4px; border-top: 1px dashed #cbd5e1;">
                        <span style="font-weight: 800;">Tu ganancia:</span>
                        <span style="font-weight: 900; color: #10b981;" id="hostEarningsTextHostInitiated">$0.00</span>
                    </div>
                </div>
            </div>

            <div class="modal-actions-row">
                <button onclick="cerrarModalCancelacion()" class="btn-modal-back">Volver</button>
                <button onclick="confirmarCancelacion()" class="btn-modal-confirm">Confirmar la cancelación</button>
            </div>
        </div>
    </div>

    <!-- Approve Cancelation / Details Modal -->
    <div id="modalAprobarCancelacion" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(8px); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: white; border-radius: 24px; width: 100%; max-width: 480px; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15); border: 1px solid rgba(255,255,255,0.1); position: relative;">
            
            <!-- Sticky Header -->
            <div style="padding: 1.25rem 2rem; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 1.25rem; background: white; z-index: 10;">
                <div id="modalIconContainer" style="width: 48px; height: 48px; border-radius: 14px; background: #f1f5f9; color: #475569; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
                    <i id="modalIcon" class="fa-solid fa-circle-info"></i>
                </div>
                <div style="flex: 1;">
                    <h3 id="modalDetallesTitle" style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.5px;">Detalles de la Reserva</h3>
                    <p id="modalDetallesSubtitle" style="font-size: 13px; color: #64748b; margin: 0; margin-top: 2px; font-weight: 500;">Información general de esta reserva</p>
                </div>
                <div style="cursor: pointer; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: #f1f5f9; color: #64748b; font-size: 1rem; transition: all 0.2s; flex-shrink: 0;" onclick="cerrarModalAprobar()">
                    <i class="fa-solid fa-xmark"></i>
                </div>
            </div>

            <!-- Scrollable Content -->
            <div style="flex: 1; overflow-y: auto; padding: 1.5rem 2rem; scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;">
                <div style="margin-bottom: 1.5rem;">
                    <label id="modalDetallesLabel" style="display: block; font-size: 11px; font-weight: 800; color: #94a3b8; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 1.5px;">Información Detallada</label>
                    <div id="motivoSolicitud" style="width: 100%; padding: 1.25rem; border-radius: 16px; border: 1px solid #f1f5f9; font-size: 14px; background: #f8fafc; color: #475569; min-height: 80px; box-sizing: border-box; cursor: default;"></div>
                </div>

                <div id="hostSettlementBox" style="background: #f8fafc; padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0; display: none;">
                    <h4 style="font-size: 13px; font-weight: 800; color: #0f172a; margin: 0 0 0.75rem 0; display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-scale-balanced" style="color: var(--primary);"></i> Política y Liquidación
                    </h4>
                    
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <div style="padding: 0.75rem; border-radius: 10px; background: white; border-left: 4px solid #64748b;" id="hostSettlementIndicator">
                            <p style="margin: 0; font-size: 13px; font-weight: 700; color: #0f172a;" id="hostSettlementStatus">Calculando...</p>
                            <p style="margin: 4px 0 0 0; font-size: 12px; color: #64748b; line-height: 1.4;" id="hostSettlementDetail">Políticas de cancelación de Estancias Digitales.</p>
                        </div>

                        <div style="display: flex; justify-content: space-between; font-size: 12px; color: #64748b; padding: 0 4px; font-weight: 500;">
                            <span>Reembolso al huésped:</span>
                            <span style="font-weight: 700; color: #0f172a;" id="guestRefundTextHost">$0.00</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 14px; color: #0f172a; padding: 0.75rem 4px 0 4px; border-top: 1px dashed #cbd5e1;">
                            <span style="font-weight: 800;">Tu ganancia:</span>
                            <span style="font-weight: 900; color: #10b981;" id="hostEarningsText">$0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sticky Footer -->
            <div style="padding: 1.25rem 2rem; border-top: 1px solid #f1f5f9; background: white; display: flex; gap: 1rem; z-index: 10;">
                <button onclick="cerrarModalAprobar()" style="flex: 1; padding: 0.875rem; border: 1px solid #e2e8f0; background: #ffffff; color: #475569; border-radius: 12px; font-weight: 700; cursor: pointer; transition: all 0.2s; font-size: 13px;">Cerrar Ventana</button>
                <button id="btnAprobarCancelacionHost" onclick="aprobarCancelacion()" style="flex: 1; padding: 0.875rem; border: none; background: #dc2626; color: white; border-radius: 12px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.15); font-size: 13px;">Confirmar Cancelación</button>
            </div>
        </div>
    </div>

    <script src="../../recursos/js/anfitrion/reservas.js?v=<?php echo time(); ?>"></script>
    <script src="../../recursos/js/huesped/detalle_reserva.js?v=<?php echo time(); ?>"></script>

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

    <script>
    // ── Filtros + Paginación de Reservas ──────────────────────────────────────
    (function () {
        var filtroActual   = "todas";
        var paginaActual   = 1;
        var POR_PAGINA     = 5;

        function todasLasFilas() {
            return Array.from(document.querySelectorAll(".reserva-row"));
        }

        function renderReservas() {
            var filas    = todasLasFilas();
            var filtradas = filas.filter(function (row) {
                var est = row.getAttribute("data-estado") || "";
                return filtroActual === "todas" || est === filtroActual;
            });

            // Ocultar todas
            filas.forEach(function (row) { row.style.display = "none"; });

            // Mostrar página actual del filtro
            var inicio = (paginaActual - 1) * POR_PAGINA;
            filtradas.slice(inicio, inicio + POR_PAGINA).forEach(function (row) {
                row.style.display = "";
            });

            renderPaginacion(filtradas.length);
        }

        function renderPaginacion(total) {
            var container = document.getElementById("paginationContainer");
            if (!container) return;

            var totalPags = Math.ceil(total / POR_PAGINA);
            container.innerHTML = "";
            if (totalPags <= 1) return;

            function mkBtn(label, pageDest, activo, disabled) {
                var btn = document.createElement("button");
                btn.innerHTML = label;
                btn.className = "pagination-btn" +
                    (activo  ? " active"   : "") +
                    (disabled? " disabled" : "");
                if (!disabled) {
                    btn.addEventListener("click", function () {
                        paginaActual = pageDest;
                        renderReservas();
                    });
                }
                return btn;
            }

            container.appendChild(mkBtn('<i class="fa-solid fa-chevron-left"></i> Anterior',
                paginaActual - 1, false, paginaActual === 1));

            for (var i = 1; i <= totalPags; i++) {
                if (i === 1 || i === totalPags ||
                    (i >= paginaActual - 1 && i <= paginaActual + 1)) {
                    container.appendChild(mkBtn(i, i, i === paginaActual, false));
                }
            }

            container.appendChild(mkBtn('Siguiente <i class="fa-solid fa-chevron-right"></i>',
                paginaActual + 1, false, paginaActual === totalPags));
        }

        // Conectar botones de filtro
        document.querySelectorAll(".filter-btn").forEach(function (btn) {
            btn.addEventListener("click", function () {
                filtroActual = btn.getAttribute("data-filter");
                paginaActual = 1;

                // Restablecer estilo de todos los botones
                document.querySelectorAll(".filter-btn").forEach(function (b) {
                    b.classList.remove("active");
                    b.style.color      = "#64748b";
                    b.style.background = "#f8fafc";
                });

                // Activar el clicado
                btn.classList.add("active");
                btn.style.color      = "white";
                btn.style.background = "var(--primary)";

                renderReservas();
            });
        });

        // Render inicial
        renderReservas();
    })();
    </script>
</body>
</html>
