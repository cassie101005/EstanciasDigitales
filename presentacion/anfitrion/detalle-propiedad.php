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
    <link rel="stylesheet" href="../../recursos/css/main.css">
    <link rel="stylesheet" href="../../recursos/css/layouts/shared.css">
    <link rel="stylesheet" href="../../recursos/css/components/navbar.css">
    <link rel="stylesheet" href="../../recursos/css/anfitrion/host_main.css">
    <link rel="stylesheet" href="../../recursos/css/anfitrion/detalle_propiedad.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="host-body">

<div class="host-wrapper">

    <!-- Sidebar -->
    <?php include '../../recursos/sidebar-host.php'; ?>

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
