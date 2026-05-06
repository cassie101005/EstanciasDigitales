<?php
require_once '../../negocio/auth/verificar_sesion.php';
validarSesion('anfitrion', '../../');
require_once '../../datos/conexion.php';
$idHost = $_SESSION['idUsuario'] ?? 1;

require_once '../../negocio/anfitrion/propiedades_view.php';
$totalIngresos = getHostIngresos($idHost, $conexion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Propiedades | Modo Anfitrión</title>
    <link rel="stylesheet" href="../../recursos/css/variables.css">
    <link rel="stylesheet" href="../../recursos/css/main.css">
    <link rel="stylesheet" href="../../recursos/css/layouts/shared.css">
    <link rel="stylesheet" href="../../recursos/css/components/navbar.css">
    <link rel="stylesheet" href="../../recursos/css/anfitrion/host_main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="host-body">
    <div class="host-wrapper">
        <?php include '../../recursos/sidebar-host.php'; ?>

        <!-- Main Content -->
        <main class="host-content-main">
            <?php include '../../recursos/navbar.php'; ?>
            
            <div class="host-dashboard-container">
                <header style="display: flex; justify-content: space-between; align-items: flex-end;">
                    <div>
                        <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -1.5px; margin-bottom: 0.5rem;">Mis Propiedades</h1>
                        <p style="color: #64748b; font-size: 14px; max-width: 600px;">Gestiona tus estancias de lujo, actualiza disponibilidades y optimiza tus ingresos desde un solo lugar.</p>
                    </div>
                    <button class="btn btn-primary" onclick="window.location.href='nueva-propiedad.php'" style="padding: 1rem 2rem; font-weight: 800; font-size: 14px; border-radius: 12px; background: var(--primary); color: white; box-shadow: 0 8px 20px rgba(30, 64, 175, 0.2);"><i class="fa-solid fa-plus"></i> Nueva Propiedad</button>
                </header>

                <!-- KPI Grid -->
                <section class="kpi-host-grid" style="margin-top: 3rem;">
                    <div class="kpi-host-card">
                        <span class="label">Propiedades Activas</span>
                        <div class="value" id="kpi-total-propiedades">0</div>
                    </div>
                    <div class="kpi-host-card">
                        <span class="label">Ingresos Totales</span>
                        <div class="value">$<?php echo number_format($totalIngresos, 2, '.', ','); ?> <span style="font-size: 11px; background: #f0f4ff; color: var(--primary); padding: 4px 10px; border-radius: 8px; margin-left: 10px;">Ganancia neta acumulada</span></div>
                    </div>
                </section>

                <!-- Filters & View Toggle -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 3rem;">
                    <div style="display: flex; gap: 1rem;">
                        <div class="filter-dropdown" id="containerFilterType" style="position: relative; background: #f1f5f9; padding: 0.75rem 1.5rem; border-radius: 12px; font-size: 13px; font-weight: 700; color: #475569; display: flex; align-items: center; gap: 1rem; cursor: pointer;">
                            Tipo: <span id="currentTypeLabel" style="color: #64748b; font-weight: 500;">Todos</span> <i class="fa-solid fa-chevron-down" style="font-size: 10px;"></i>
                            
                            <!-- Dropdown Menu -->
                            <div id="menuTipos" style="display: none; position: absolute; top: 110%; left: 0; background: white; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); z-index: 100; min-width: 180px; padding: 0.5rem;">
                                <div class="tipo-option" data-id="all" style="padding: 0.6rem 1rem; border-radius: 8px; transition: background 0.2s; cursor: pointer; font-weight: 500;">Todos</div>
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 1.25rem; font-size: 1.15rem; color: #94a3b8; align-items: center;">
                        <i class="fa-solid fa-grip view-toggle-btn active" id="btnViewGrid" title="Vista Cuadrícula"></i>
                        <i class="fa-solid fa-list view-toggle-btn" id="btnViewList" title="Vista Lista"></i>
                    </div>
                </div>

                <!-- Property Grid -->
                <section class="host-prop-grid" id="gridPropiedades">
                    <!-- Skeletons mientras carga -->
                    <div class="host-prop-card prop-skeleton"></div>
                    <div class="host-prop-card prop-skeleton"></div>
                    <div class="host-prop-card prop-skeleton"></div>
                </section>

                <!-- Estado vacío -->
                <div id="estadoVacio" style="display:none; text-align:center; padding:5rem 2rem; color:#94a3b8;">
                    <i class="fa-solid fa-house-circle-xmark" style="font-size:3rem; margin-bottom:1.5rem; display:block;"></i>
                    <h3 style="font-size:1.25rem; font-weight:800; color:#1e293b; margin-bottom:0.5rem;">Sin propiedades registradas</h3>
                    <p style="font-size:14px; margin-bottom:2rem;">Aún no has registrado ninguna propiedad. ¡Empieza ahora!</p>
                    <button onclick="window.location.href='nueva-propiedad.php'"
                        style="padding:0.9rem 2rem; background:var(--primary); color:white; border:none; border-radius:12px; font-weight:800; font-size:14px; cursor:pointer;">
                        <i class="fa-solid fa-plus"></i> Registrar primera propiedad
                    </button>
                </div>

            </div>
        </main>
    </div>

<link rel="stylesheet" href="../../recursos/css/anfitrion/propiedades.css">
<script src="../../recursos/js/anfitrion/propiedades.js"></script>
</body>
</html>
