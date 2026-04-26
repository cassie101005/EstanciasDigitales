<?php
require_once '../../negocio/auth/verificar_sesion.php';
validarSesion('anfitrion', '../../');
require_once '../../datos/conexion.php';
$idPropiedad = intval($_GET['id'] ?? 0);
if ($idPropiedad <= 0) {
    header('Location: propiedades.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Propiedad | Estancias Digitales</title>
    <link rel="stylesheet" href="../../recursos/css/variables.css">
    <link rel="stylesheet" href="../../recursos/css/layouts/shared.css">
    <link rel="stylesheet" href="../../recursos/css/anfitrion/host_main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../recursos/css/anfitrion/detalle_propiedad.css">
</head>
<body class="host-body">

<div class="host-wrapper">

    <!-- Sidebar -->
    <aside class="sidebar-host">
        <div class="host-logo-box">
            <h2><i class="fa-solid fa-house-laptop"></i> Estancias Digitales</h2>
            <p>Modo Anfitrión</p>
        </div>
        <nav class="side-nav-host">
            <li class="side-nav-item" onclick="window.location.href='dashboard.php'"><i class="fa-solid fa-house"></i> Inicio</li>
            <li class="side-nav-item active" onclick="window.location.href='propiedades.php'"><i class="fa-solid fa-building"></i> Propiedades</li>
            <li class="side-nav-item" onclick="window.location.href='calendario.php'"><i class="fa-solid fa-calendar-days"></i> Calendario</li>
            <li class="side-nav-item" onclick="window.location.href='reservas.php'"><i class="fa-solid fa-receipt"></i> Reservas</li>
        </nav>
        <div style="margin-top:auto; padding-top:1rem; border-top:1px solid #F3F4F6; list-style:none; padding-left:0;">
            <li class="side-nav-item" style="color:#EF4444;" onclick="window.location.href='../../index.php'">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Salir
            </li>
        </div>
    </aside>

    <!-- Main -->
    <main class="host-content-main">

        <!-- Top bar -->
        <header class="dp-topbar">
            <a href="propiedades.php" class="dp-back-btn"><i class="fa-solid fa-chevron-left"></i></a>
            <div>
                <p class="dp-topbar-title" id="dpTitleBar">Cargando propiedad...</p>
                <p class="dp-topbar-sub">Vista completa del registro</p>
            </div>
        </header>

        <!-- Contenido -->
        <div class="dp-body" id="dpBody">
            <!-- Skeleton mientras carga -->
            <div class="dp-skeleton" style="height:420px; margin-bottom:2rem;"></div>
            <div class="dp-skeleton" style="height:80px; margin-bottom:2rem;"></div>
            <div class="dp-skeleton" style="height:160px; margin-bottom:1.5rem;"></div>
            <div class="dp-skeleton" style="height:120px;"></div>
        </div>

    </main>
</div>

<script>
    window.ID_PROPIEDAD = <?= $idPropiedad ?>;
</script>
<script src="../../recursos/js/anfitrion/detalle_propiedad.js"></script>
</body>
</html>
