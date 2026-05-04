<?php
$idUsuarioNotif = $_SESSION['idUsuario'] ?? 1;
$notificaciones_nav = [];

if (!isset($conexion)) {
    $path_conexion = __DIR__ . '/../datos/conexion.php';
    if (file_exists($path_conexion)) { require_once $path_conexion; }
}

$is_host_nav = $is_host ?? false;

if (isset($conexion)) {
    // Consulta unificada para Huésped y Anfitrión usando tbl_notificaciones
    // Ordenar por prioridad: primero no leídas, luego por fecha reciente
    $sqlNotifNav = "SELECT * FROM tbl_notificaciones 
                    WHERE idUsuario = ? 
                    ORDER BY leida ASC, fecha DESC LIMIT 3";
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
?>

<div id="notificationsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 999999; background: transparent;" onclick="closeNotificationsModal(event)">
    <div class="quick-actions-box" style="position: absolute; top: 70px; right: 80px; width: 420px; background: white; border-radius: 2rem; box-shadow: 0 20px 50px rgba(0,0,0,0.2); padding: 2rem; border: 1px solid #f1f5f9; cursor: default;" onclick="event.stopPropagation()">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h4 style="font-size: 1.3rem; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.5px;">Notificaciones</h4>
            <?php 
                $nuevas_count = array_reduce($notificaciones_nav, function($carry, $item) {
                    return $carry + (!($item['leida'] ?? 1) ? 1 : 0);
                }, 0);
                if ($nuevas_count > 0): 
            ?>
                <span style="background: var(--primary); color: white; font-size: 11px; font-weight: 800; padding: 4px 12px; border-radius: 99px;"><?php echo $nuevas_count; ?> Nuevas</span>
            <?php endif; ?>
        </div>
        
        <div style="display: flex; flex-direction: column; gap: 0.75rem; max-height: 500px; overflow-y: auto; padding-right: 5px; margin: 0 -1rem;">
            <?php foreach ($notificaciones_nav as $n): ?>
                <?php 
                    $tipo = $n['tipo'] ?? 'default';
                    $leida = ($n['leida'] == 1);
                    $color = "#7c3aed"; // Default morado
                    $icon = "fa-bell";
                    $mensaje = $n['mensaje'];
                    
                    // Lógica de diseño y mensajes dinámicos
                    switch($tipo) {
                        case 'propiedad':
                            $color = "#3b82f6"; // Azul
                            $icon = "fa-house-chimney";
                            break;
                        case 'reserva_nueva':
                        case 'reserva':
                            $color = "#7c3aed"; // Morado
                            $icon = "fa-calendar-plus";
                            break;
                        case 'reserva_cancelada':
                        case 'solicitud_cancelacion':
                            $color = "#ef4444"; // Rojo
                            $icon = "fa-calendar-xmark";
                            break;
                        case 'pago_confirmado':
                        case 'confirmada':
                            $color = "#22c55e"; // Verde
                            $icon = "fa-circle-check";
                            break;
                        case 'reserva_finalizada':
                            $color = "#64748b"; // Gris
                            $icon = "fa-flag-checkered";
                            break;
                        case 'resena_recibida':
                            $color = "#f59e0b"; // Amarillo/Naranja
                            $icon = "fa-star";
                            // Fallback: Si por alguna razón la URL en BD apunta al dashboard, forzamos a reservas
                            if (strpos($n['url'], 'dashboard.php') !== false) {
                                $n['url'] = "presentacion/anfitrion/reservas.php#reseñas";
                            }
                            break;
                        case 'respuesta_resena':
                            $color = "#8b5cf6"; // Violeta
                            $icon = "fa-comment-dots";
                            break;
                    }
                    
                    $url = !empty($n['url']) ? $base_path . $n['url'] : "#";
                ?>
                <div class="noti-item-v2 <?php echo $tipo; ?> <?php echo $leida ? 'is-read' : ''; ?>" 
                     onclick="handleNotifClick(<?php echo $n['idNotificacion']; ?>, '<?php echo $url; ?>')"
                     style="display: flex; gap: 1rem; align-items: start; padding: 1rem 1.25rem; border-radius: 1rem; cursor: pointer; transition: all 0.2s; border-left: 4px solid <?php echo $color; ?>; background: <?php echo $leida ? 'transparent' : $color.'05'; ?>; opacity: <?php echo $leida ? '0.6' : '1'; ?>;">
                    
                    <div style="width: 38px; height: 38px; background: <?php echo $color; ?>15; color: <?php echo $color; ?>; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.1rem;">
                        <i class="fa-solid <?php echo $icon; ?>"></i>
                    </div>
                    
                    <div style="flex: 1;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 2px;">
                            <span style="font-size: 13px; font-weight: 800; color: #0f172a;"><?php echo htmlspecialchars($n['titulo']); ?></span>
                            <span style="font-size: 10px; color: #94a3b8;"><?php echo date('H:i', strtotime($n['fecha'])); ?></span>
                        </div>
                        <p style="font-size: 13px; color: #475569; margin: 0; line-height: 1.4;">
                            <?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($notificaciones_nav)): ?>
                <div style="text-align: center; padding: 3rem 1rem;">
                    <i class="fa-regular fa-bell-slash" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 1rem; display: block;"></i>
                    <p style="font-size: 14px; color: #94a3b8; font-weight: 600;">No tienes notificaciones nuevas.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($notificaciones_nav)): ?>
            <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #f1f5f9; text-align: center;">
                <a href="<?php echo $base_path; ?>presentacion/<?php echo ($is_host_nav ? 'anfitrion' : 'huesped'); ?>/notificaciones.php" style="text-decoration: none; color: var(--primary); font-size: 12px; font-weight: 800; cursor: pointer;">Ver todas las notificaciones</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.noti-item-v2:hover {
    background: #f8fafc !important;
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
.noti-item-v2.is-read {
    border-left-color: #cbd5e1 !important;
}
</style>

<script>
function toggleNotificationsModal() {
    const modal = document.getElementById('notificationsModal');
    if (!modal) return;
    modal.style.display = (modal.style.display === 'none' || modal.style.display === '') ? 'block' : 'none';
}

function closeNotificationsModal(e) {
    if (e) e.preventDefault();
    document.getElementById('notificationsModal').style.display = 'none';
}

function handleNotifClick(id, link) {
    // 1. Actualizar visualmente de inmediato (Modal y Página completa)
    const selectors = [
        `.noti-item-v2[onclick*="${id}"]`,
        `.notif-full-item[onclick*="${id}"]`
    ];
    
    selectors.forEach(sel => {
        const item = document.querySelector(sel);
        if (item && !item.classList.contains('is-read')) {
            item.classList.add('is-read');
            item.style.opacity = '0.6';
            if (item.classList.contains('noti-item-v2')) {
                item.style.background = 'transparent';
                item.style.borderLeftColor = '#cbd5e1';
            }
        }
    });

    // 2. Decrementar contadores visualmente
    // Navbar Badge
    const navBadge = document.querySelector('.nav-notif-badge');
    if (navBadge) {
        let count = parseInt(navBadge.innerText);
        if (!isNaN(count) && count > 0) {
            count--;
            if (count === 0) {
                navBadge.style.display = 'none';
            } else {
                navBadge.innerText = count > 9 ? '9+' : count;
            }
        }
    }
    
    // Modal "Nuevas" span
    const modalSpan = document.querySelector('#notificationsModal span[style*="background: var(--primary)"]');
    if (modalSpan) {
        let mCount = parseInt(modalSpan.innerText);
        if (!isNaN(mCount) && mCount > 0) {
            mCount--;
            if (mCount === 0) {
                modalSpan.style.display = 'none';
            } else {
                modalSpan.innerText = mCount + ' Nuevas';
            }
        }
    }

    // 3. Enviar al servidor y redireccionar
    fetch('<?php echo $base_path; ?>apis/huesped/marcar_notificacion_leida.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'idNotificacion=' + id
    })
    .then(() => {
        if (link && link !== "#" && link !== "javascript:void(0)") {
            window.location.href = link;
        }
    })
    .catch(err => {
        console.error("Error:", err);
        if (link && link !== "#" && link !== "javascript:void(0)") {
            window.location.href = link;
        }
    });
}
</script>
