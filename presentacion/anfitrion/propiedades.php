<?php 
session_start();
require_once '../../datos/conexion.php';
$idHost = $_SESSION['idUsuario'] ?? 1;

// Calcular ingresos totales omitiendo las reservas canceladas
$sqlIngresos = "SELECT SUM(r.dTotalReserva) as totalIngresos 
                FROM tbl_reserva r 
                JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad 
                WHERE p.idUsuario = ? 
                AND (r.vEstatus IS NULL OR (UPPER(r.vEstatus) != 'CANCELADA' AND UPPER(r.vEstatus) != 'CANCELADO'))";
$stmtIng = $conexion->prepare($sqlIngresos);
if ($stmtIng) {
    $stmtIng->bind_param("i", $idHost);
    $stmtIng->execute();
    $resIng = $stmtIng->get_result()->fetch_assoc();
    $totalIngresos = $resIng['totalIngresos'] ?? 0;
} else {
    // Si la columna vEstatus no existe en esta db, intentamos con vEstado
    $sqlIngresos2 = "SELECT SUM(r.dTotalReserva) as totalIngresos 
                     FROM tbl_reserva r 
                     JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad 
                     WHERE p.idUsuario = ? 
                     AND (r.vEstado IS NULL OR (UPPER(r.vEstado) != 'CANCELADA' AND UPPER(r.vEstado) != 'CANCELADO'))";
    $stmtIng2 = $conexion->prepare($sqlIngresos2);
    if ($stmtIng2) {
        $stmtIng2->bind_param("i", $idHost);
        $stmtIng2->execute();
        $resIng2 = $stmtIng2->get_result()->fetch_assoc();
        $totalIngresos = $resIng2['totalIngresos'] ?? 0;
    } else {
        $totalIngresos = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Propiedades | Modo Anfitrión</title>
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
                    <li class="side-nav-item active" onclick="window.location.href='propiedades.php'"><i class="fa-solid fa-building"></i> Propiedades</li>
                    <li class="side-nav-item" onclick="window.location.href='calendario.php'"><i class="fa-solid fa-calendar-days"></i> Calendario</li>
                    <li class="side-nav-item" onclick="window.location.href='reservas.php'"><i class="fa-solid fa-receipt"></i> Reservas</li>
                </nav>
            </div>


        </aside>

        <!-- Main Content -->
        <main class="host-content-main">
            <?php include '../../recursos/navbar.php'; ?>
            
            <div style="padding: 2.5rem 4rem; max-width: 1600px; margin: 0 auto;">
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
                        <div class="value">$<?php echo number_format($totalIngresos, 2); ?> <span style="font-size: 11px; background: #f0f4ff; color: var(--primary); padding: 4px 10px; border-radius: 8px; margin-left: 10px;">Ganancia neta acumulada</span></div>
                    </div>
                </section>

                <!-- Filters & View Toggle -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 3rem;">
                    <div style="display: flex; gap: 1rem;">
                        <div class="filter-dropdown" style="background: #f1f5f9; padding: 0.75rem 1.5rem; border-radius: 12px; font-size: 13px; font-weight: 700; color: #475569; display: flex; align-items: center; gap: 1rem;">
                            Tipo: <span style="color: #64748b; font-weight: 500;">Todos</span> <i class="fa-solid fa-chevron-down" style="font-size: 10px;"></i>
                        </div>
                        <div class="filter-dropdown" style="background: #f1f5f9; padding: 0.75rem 1.5rem; border-radius: 12px; font-size: 13px; font-weight: 700; color: #475569; display: flex; align-items: center; gap: 1rem;">
                            Estado: <span style="color: #64748b; font-weight: 500;">Todos</span> <i class="fa-solid fa-chevron-down" style="font-size: 10px;"></i>
                        </div>
                    </div>
                    <div style="display: flex; gap: 1rem; font-size: 1.1rem; color: #94a3b8;">
                        <i class="fa-solid fa-grip" style="color: var(--primary); cursor: pointer;"></i>
                        <i class="fa-solid fa-list" style="cursor: pointer;"></i>
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

<style>
.prop-skeleton {
    height: 380px;
    background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
    background-size: 200% 100%;
    animation: shimmerProp 1.4s infinite;
}
@keyframes shimmerProp {
    0%   { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
</style>

<script>
(async function cargarPropiedades() {
    const grid        = document.getElementById('gridPropiedades');
    const estadoVacio = document.getElementById('estadoVacio');

    try {
        const res  = await fetch(`../../apis/anfitrion/propiedades.php?accion=listar&_=${new Date().getTime()}`);
        const data = await res.json();

        grid.innerHTML = ''; // limpia skeletons

        if (!data.ok || data.propiedades.length === 0) {
            grid.style.display = 'none';
            estadoVacio.style.display = 'block';
            document.getElementById('kpi-total-propiedades').innerHTML = '0 <span style="font-size: 11px; background: #f1f5f9; color: #64748b; padding: 4px 10px; border-radius: 8px; margin-left: 10px;">Ninguna</span>';
            return;
        }

        document.getElementById('kpi-total-propiedades').innerHTML = data.propiedades.length + ' <span style="font-size: 11px; background: #d1fae5; color: #065f46; padding: 4px 10px; border-radius: 8px; margin-left: 10px;">Activas</span>';

        data.propiedades.forEach(p => {
            const imgSrc = p.imagenPrincipal
                ? `../../${p.imagenPrincipal.replace(/ /g, '%20')}`
                : 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=600&q=80';

            const ubicacion = [p.ciudad, p.estado].filter(Boolean).join(', ') || 'Sin ubicación';
            const precio    = parseFloat(p.dPrecioNoche || 0).toLocaleString('es-ES', {minimumFractionDigits: 0});

            grid.innerHTML += `
                <div class="host-prop-card" style="cursor:pointer;"
                     onclick="window.location.href='detalle-propiedad.php?id=${p.idPropiedad}'">
                    <div class="card-img-wrapper">
                        <img src="${imgSrc}" alt="${p.vNombre}" loading="lazy"
                             onerror="this.src='https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=600&q=80'">
                        <div class="btn-edit-float" onclick="event.stopPropagation(); window.location.href='editar-propiedad.php?id=${p.idPropiedad}'">
                            <i class="fa-solid fa-pencil"></i>
                        </div>
                    </div>
                    <div class="host-card-content">
                        <div class="host-card-info-row">
                            <h3 class="host-card-title">${p.vNombre}</h3>
                            <span class="host-card-price">$${precio}<span style="font-size:10px;font-weight:400;color:#94a3b8;">/noche</span></span>
                        </div>
                        <p style="font-size:13px; color:#64748b;">
                            <i class="fa-solid fa-location-dot" style="margin-right:5px; opacity:0.5;"></i>${ubicacion}
                        </p>
                        <div class="host-card-footer">
                            <span><i class="fa-solid fa-bed"></i> ${p.iNumeroHabitaciones || '—'} Dorm.</span>
                            <span><i class="fa-solid fa-users"></i> ${p.iCapacidadHuespedes || '—'} Huésp.</span>
                            <span style="font-size:11px; background:#f0f4ff; color:var(--primary); padding:3px 8px; border-radius:6px; font-weight:700;">
                                ${p.tipo || 'Propiedad'}
                            </span>
                        </div>
                    </div>
                </div>`;
        });

    } catch (err) {
        console.error('Error cargando propiedades:', err);
        grid.innerHTML = '<p style="color:#ef4444; padding:2rem;">Error al cargar propiedades.</p>';
    }
})();
</script>
</body>
</html>
