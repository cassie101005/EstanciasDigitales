<?php
require_once '../../negocio/auth/verificar_sesion.php';
validarSesion('anfitrion', '../../');
require_once '../../datos/conexion.php';

$idUsuario = $_SESSION['idUsuario'];

// Obtener todas las notificaciones
$sql = "SELECT * FROM tbl_notificaciones WHERE idUsuario = ? ORDER BY fecha DESC";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$notificaciones = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$is_root = false;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Notificaciones Anfitrión | Estancias Digitales</title>
    <link rel="stylesheet" href="../../recursos/css/variables.css">
    <link rel="stylesheet" href="../../recursos/css/main.css">
    <link rel="stylesheet" href="../../recursos/css/layouts/shared.css">
    <link rel="stylesheet" href="../../recursos/css/components/navbar.css">
    <link rel="stylesheet" href="../../recursos/css/anfitrion/host_main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .notif-page-container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        .notif-full-item {
            background: white;
            border-radius: 1.5rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            gap: 1.5rem;
            align-items: center;
            border: 1px solid #f1f5f9;
            transition: all 0.2s;
            text-decoration: none;
            color: inherit;
        }
        .notif-full-item:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary);
        }
        .notif-full-item.is-read {
            opacity: 0.7;
            background: #fcfcfc;
        }
        .notif-icon-box {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }
    </style>
</head>
<body class="host-body">
    <div class="host-wrapper">
        <?php include '../../recursos/sidebar-host.php'; ?>

        <main class="host-content-main">
            <?php include '../../recursos/navbar.php'; ?>
            
            <div class="notif-page-container">
                <h1 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 2rem; letter-spacing: -1px;">Notificaciones del Panel</h1>
                
                <?php foreach ($notificaciones as $n): ?>
                    <?php 
                        $tipo = $n['tipo'];
                        $leida = $n['leida'];
                        $color = "#7c3aed"; $icon = "fa-bell"; $bg = "#f3e8ff";
                        switch($tipo) {
                            case 'propiedad': $color = "#3b82f6"; $icon = "fa-house-chimney"; $bg = "#dbeafe"; break;
                            case 'reserva_nueva':
                            case 'reserva': $color = "#7c3aed"; $icon = "fa-calendar-plus"; $bg = "#f3e8ff"; break;
                            case 'reserva_cancelada':
                            case 'solicitud_cancelacion': $color = "#ef4444"; $icon = "fa-calendar-xmark"; $bg = "#fee2e2"; break;
                            case 'pago_confirmado':
                            case 'confirmada': $color = "#22c55e"; $icon = "fa-circle-check"; $bg = "#dcfce7"; break;
                            case 'reserva_finalizada': $color = "#64748b"; $icon = "fa-flag-checkered"; $bg = "#f1f5f9"; break;
                            case 'resena_recibida': 
                                $color = "#f59e0b"; $icon = "fa-star"; $bg = "#fef3c7"; 
                                if (strpos($n['url'], 'dashboard.php') !== false) {
                                    $n['url'] = "presentacion/anfitrion/reservas.php#reseñas";
                                }
                                break;
                        }
                        $url = !empty($n['url']) ? $base_path . $n['url'] : "#";
                    ?>
                    <a href="javascript:void(0)" onclick="handleNotifClick(<?php echo $n['idNotificacion']; ?>, '<?php echo $url; ?>')" 
                       class="notif-full-item <?php echo $leida ? 'is-read' : ''; ?>">
                        <div class="notif-icon-box" style="background: <?php echo $bg; ?>; color: <?php echo $color; ?>;">
                            <i class="fa-solid <?php echo $icon; ?>"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 4px;">
                                <h3 style="font-size: 1.1rem; font-weight: 800; margin: 0;"><?php echo htmlspecialchars($n['titulo']); ?></h3>
                                <span style="font-size: 12px; color: #94a3b8;"><?php echo date('d M, Y • H:i', strtotime($n['fecha'])); ?></span>
                            </div>
                            <p style="font-size: 14px; color: #475569; margin: 0;"><?php echo htmlspecialchars($n['mensaje']); ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>

                <?php if (empty($notificaciones)): ?>
                    <div style="text-align: center; padding: 5rem 0;">
                        <i class="fa-regular fa-bell-slash" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 1.5rem; display: block;"></i>
                        <p style="font-size: 1.2rem; color: #94a3b8; font-weight: 600;">No tienes notificaciones registradas.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
