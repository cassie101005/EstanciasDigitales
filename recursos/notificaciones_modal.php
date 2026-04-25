<?php
$idUsuarioNotif = $_SESSION['idUsuario'] ?? 1;

// Obtener las notificaciones recientes de reservas para el anfitrión
$notificaciones_nav = [];
// Asegurar que exista la conexión
if (!isset($conexion)) {
    $path_conexion = __DIR__ . '/../datos/conexion.php';
    if (file_exists($path_conexion)) {
        require_once $path_conexion;
    }
}

$is_host_nav = $is_host ?? false;

if (isset($conexion)) {
    if ($is_host_nav) {
        $sqlNotifNav = "SELECT r.idReserva, r.dtFechaRegistro, r.dtFechaInicio, r.dtFechaFin, r.vEstatus, u.vNombre, u.vApellido, u.vFoto, p.vNombre as propiedad
                        FROM tbl_reserva r 
                        JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad 
                        JOIN tbl_usuarios u ON r.idUsuario = u.idUsuario
                        WHERE p.idUsuario = ? 
                        ORDER BY r.dtFechaRegistro DESC LIMIT 4";
        $stmtNotifNav = $conexion->prepare($sqlNotifNav);
        if ($stmtNotifNav) {
            $stmtNotifNav->bind_param("i", $idUsuarioNotif);
            $stmtNotifNav->execute();
            $resNotifNav = $stmtNotifNav->get_result();
            while ($row = $resNotifNav->fetch_assoc()) {
                $notificaciones_nav[] = $row;
            }
        }
    } else {
        // Notificaciones para huéspedes: Nuevas propiedades y actualizaciones de sus propias reservas
        $sqlNotifNav = "(SELECT p.idPropiedad as id, p.vNombre as propiedad, t.vNombreCategoria as categoria, p.dtFechaRegistro as fecha, 
                               (SELECT vImagen FROM tbl_imagen_propiedad WHERE idPropiedad = p.idPropiedad LIMIT 1) as vFoto,
                               'propiedad' as tipo, NULL as vEstatus, NULL as dReembolso
                        FROM tbl_propiedad p
                        JOIN tbl_tipo_propiedad t ON p.idTipoPropiedad = t.idTipoPropiedad)
                        UNION ALL
                        (SELECT r.idReserva as id, p.vNombre as propiedad, 'Actualización de Reserva' as categoria, r.dtFechaRegistro as fecha,
                               (SELECT vImagen FROM tbl_imagen_propiedad WHERE idPropiedad = p.idPropiedad LIMIT 1) as vFoto,
                               'reserva' as tipo, r.vEstatus, c.dReembolso
                        FROM tbl_reserva r
                        JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad
                        LEFT JOIN tbl_cancelacion c ON r.idReserva = c.idReserva
                        WHERE r.idUsuario = ? AND (r.vEstatus = 'Cancelada' OR r.vEstatus = 'Confirmada' OR r.vEstatus = 'Pendiente Cancelacion'))

                        ORDER BY fecha DESC LIMIT 4";
        $stmtNotifNav = $conexion->prepare($sqlNotifNav);
        if ($stmtNotifNav) {
            $stmtNotifNav->bind_param("i", $idUsuarioNotif);
            $stmtNotifNav->execute();
            $resNotifNav = $stmtNotifNav->get_result();
            while ($row = $resNotifNav->fetch_assoc()) {
                $notificaciones_nav[] = $row;
            }
        }
    }
}
?>

<div id="notificationsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 999999; background: transparent;" onclick="closeNotificationsModal(event)">
    <div class="quick-actions-box" style="position: absolute; top: 70px; right: 80px; width: 400px; background: white; border-radius: 2rem; box-shadow: 0 20px 40px rgba(0,0,0,0.15); padding: 2rem; border: 1px solid #f1f5f9; cursor: default;" onclick="event.stopPropagation()">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h4 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.5px;">Notificaciones</h4>
            <?php if (!empty($notificaciones_nav)): ?>
                <span style="background: var(--primary); color: white; font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 99px;"><?php echo count($notificaciones_nav); ?> Nuevas</span>
            <?php endif; ?>
        </div>
        
        <div style="display: flex; flex-direction: column; gap: 1.5rem; max-height: 450px; overflow-y: auto; padding-right: 5px;">
            <?php foreach ($notificaciones_nav as $n): ?>
                <?php if ($is_host_nav): ?>
                    <?php 
                        $foto = !empty($n['vFoto']) ? $base_path . $n['vFoto'] : 'https://i.pravatar.cc/100?u=' . md5($n['vNombre']);
                        
                        // Fecha exacta en que se registró la reserva
                        $fechaRegistro = !empty($n['dtFechaRegistro']) ? date('d M Y - H:i', strtotime($n['dtFechaRegistro'])) : date('d M Y', strtotime($n['dtFechaInicio']));
                        
                        $estatus = strtolower($n['vEstatus'] ?? 'pendiente');
                        
                        if ($estatus == 'confirmada') {
                            $mensaje = "ha realizado y confirmado una reserva en";
                            $color = "#10b981";
                        } else if ($estatus == 'cancelada') {
                            $mensaje = "ha cancelado su reserva en";
                            $color = "#ef4444";
                        } else {
                            $mensaje = "ha solicitado una reserva en";
                            $color = "#f59e0b";
                        }
                    ?>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <div class="nav-profile-avatar" style="width: 45px; height: 45px; flex-shrink: 0; cursor: pointer; border: 2px solid <?php echo $color; ?>; background: white; border-radius: 50%;">
                            <img src="<?php echo $foto; ?>" alt="Perfil" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                        </div>
                        <div>
                            <p style="font-size: 13px; color: #475569; margin: 0; line-height: 1.4;">
                                <strong style="color: #0f172a;"><?php echo htmlspecialchars($n['vNombre'] . ' ' . $n['vApellido']); ?></strong> 
                                <?php echo $mensaje; ?> 
                                <strong style="color: var(--primary);"><?php echo htmlspecialchars($n['propiedad']); ?></strong>
                            </p>
                            <span style="font-size: 11px; color: #94a3b8; display: block; margin-top: 4px;">
                                <i class="fa-regular fa-clock" style="margin-right: 4px;"></i> Realizada el <?php echo $fechaRegistro; ?>
                            </span>
                        </div>
                    </div>
                <?php else: ?>
                    <?php 
                        $tipo = $n['tipo'] ?? 'propiedad';
                        $foto_item = !empty($n['vFoto']) ? $base_path . $n['vFoto'] : ($tipo == 'reserva' ? 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=100&q=80' : 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=100&q=80');
                        $fecha_item = !empty($n['fecha']) ? date('d M Y', strtotime($n['fecha'])) : 'Recientemente';
                        $estatus = strtolower($n['vEstatus'] ?? '');
                        $color_borde = "var(--primary)";
                        
                        if ($tipo == 'reserva') {
                            if ($estatus == 'cancelada') {
                                $reembolso = isset($n['dReembolso']) ? " con un reembolso de <strong>$" . number_format($n['dReembolso'], 2) . "</strong>" : "";
                                $msg_huesped = "Tu reservación en <strong style='color: var(--primary);'>" . htmlspecialchars($n['propiedad']) . "</strong> ha sido <strong style='color: #ef4444;'>cancelada</strong>" . $reembolso . ".";
                                $color_borde = "#ef4444";
                            } else if ($estatus == 'confirmada') {

                                $msg_huesped = "¡Tu reservación en <strong style='color: var(--primary);'>" . htmlspecialchars($n['propiedad']) . "</strong> ha sido <strong style='color: #10b981;'>confirmada</strong>!";
                                $color_borde = "#10b981";
                            } else {
                                $msg_huesped = "Tu solicitud de reserva en <strong style='color: var(--primary);'>" . htmlspecialchars($n['propiedad']) . "</strong> está siendo procesada.";
                                $color_borde = "#f59e0b";
                            }
                        } else {
                            $msg_huesped = "¡Nueva propiedad descubierta! <strong style='color: var(--primary);'>" . htmlspecialchars($n['propiedad']) . "</strong> en la categoría <strong style='color: #0f172a;'>" . htmlspecialchars($n['categoria']) . "</strong>.";
                        }
                    ?>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <div class="nav-profile-avatar" style="width: 45px; height: 45px; flex-shrink: 0; cursor: pointer; border: 2px solid <?php echo $color_borde; ?>; background: white; border-radius: 10px; overflow: hidden;">
                            <img src="<?php echo $foto_item; ?>" alt="Item" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div>
                            <p style="font-size: 13px; color: #475569; margin: 0; line-height: 1.4;">
                                <?php echo $msg_huesped; ?>
                            </p>
                            <span style="font-size: 11px; color: #94a3b8; display: block; margin-top: 4px;">
                                <i class="fa-regular <?php echo $tipo == 'reserva' ? 'fa-calendar-check' : 'fa-star'; ?>" style="margin-right: 4px;"></i> <?php echo $fecha_item; ?>
                            </span>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            
            <?php if (empty($notificaciones_nav)): ?>
                <div style="text-align: center; padding: 2rem 0;">
                    <i class="fa-regular fa-bell-slash" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
                    <p style="font-size: 13px; color: #94a3b8; margin: 0;">No tienes notificaciones recientes.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function toggleNotificationsModal() {
    const modal = document.getElementById('notificationsModal');
    if (modal.style.display === 'none' || modal.style.display === '') {
        modal.style.display = 'block';
    } else {
        modal.style.display = 'none';
    }
}

function closeNotificationsModal(e) {
    if (e) e.preventDefault();
    document.getElementById('notificationsModal').style.display = 'none';
}
</script>
