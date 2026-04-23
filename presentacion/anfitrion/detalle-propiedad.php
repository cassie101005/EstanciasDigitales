<?php
session_start();
if (!isset($_SESSION['idUsuario'])) {
    header('Location: ../../index.php');
    exit;
}
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
    <style>
        /* ── Layout detalle ── */
        .dp-topbar {
            display: flex; align-items: center; gap: 1.25rem;
            padding: 1.5rem 3.5rem;
            background: #fff; border-bottom: 1px solid #E5E7EB;
            position: sticky; top: 0; z-index: 100;
        }
        .dp-back-btn {
            width: 40px; height: 40px; border-radius: 10px;
            background: #F3F4F6; border: 1px solid #E5E7EB;
            display: flex; align-items: center; justify-content: center;
            color: #374151; font-size: 14px; text-decoration: none;
            transition: all 0.2s; flex-shrink: 0;
        }
        .dp-back-btn:hover { background:#E5E7EB; color: var(--primary); }
        .dp-topbar-title { font-size: 1.25rem; font-weight: 800; color: #111827; margin: 0; }
        .dp-topbar-sub   { font-size: 12px; color: #9CA3AF; margin: 2px 0 0; }

        .dp-body { padding: 2.5rem 3.5rem 5rem; max-width: 1000px; margin: 0 auto; }

        /* Galería */
        .dp-gallery { display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; border-radius: 20px; overflow: hidden; height: 420px; margin-bottom: 2rem; }
        .dp-gallery-main { height: 100%; overflow: hidden; }
        .dp-gallery-main img { width: 100%; height: 100%; object-fit: cover; }
        .dp-gallery-side { display: grid; grid-template-rows: 1fr 1fr; gap: 1rem; }
        .dp-gallery-side img { width: 100%; height: 100%; object-fit: cover; border-radius: 0; }
        .dp-gallery-placeholder { background: #F3F4F6; display: flex; align-items: center; justify-content: center; color: #9CA3AF; font-size: 2rem; }
        @media (max-width: 700px) { .dp-gallery { grid-template-columns: 1fr; height: auto; } .dp-gallery-side { display: none; } }

        /* Info header */
        .dp-info-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 2rem; margin-bottom: 2rem; flex-wrap: wrap; }
        .dp-nombre { font-size: 1.75rem; font-weight: 800; color: #111827; letter-spacing: -0.5px; margin: 0 0 0.5rem; }
        .dp-ubicacion { font-size: 14px; color: #64748b; display: flex; align-items: center; gap: 6px; }
        .dp-tipo-badge { font-size: 12px; font-weight: 700; padding: 0.4rem 1rem; background: #F5F3FF; color: var(--primary); border-radius: 99px; border: 1px solid #DDD6FE; }
        .dp-precio-box { text-align: right; flex-shrink: 0; }
        .dp-precio { font-size: 2rem; font-weight: 800; color: var(--primary); }
        .dp-precio-sub { font-size: 12px; color: #9CA3AF; }

        /* Stats row */
        .dp-stats { display: flex; gap: 1.5rem; flex-wrap: wrap; padding: 1.5rem 0; border-top: 1px solid #F3F4F6; border-bottom: 1px solid #F3F4F6; margin-bottom: 2rem; }
        .dp-stat { display: flex; align-items: center; gap: 10px; font-size: 14px; color: #374151; font-weight: 600; }
        .dp-stat i { color: var(--primary); font-size: 1rem; }

        /* Sección card */
        .dp-section { background: #fff; border: 1px solid #E5E7EB; border-radius: 18px; padding: 2rem 2.25rem; margin-bottom: 1.5rem; }
        .dp-section-title { font-size: 1rem; font-weight: 800; color: #111827; margin: 0 0 1rem; display: flex; align-items: center; gap: 10px; }
        .dp-section-title i { color: var(--primary); }
        .dp-descripcion { font-size: 14px; color: #475569; line-height: 1.8; white-space: pre-wrap; }

        /* Chips */
        .dp-chip-grid { display: flex; flex-wrap: wrap; gap: 0.6rem; }
        .dp-chip { padding: 0.45rem 1rem; background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 99px; font-size: 13px; font-weight: 500; color: #374151; display: flex; align-items: center; gap: 6px; }
        .dp-chip i { color: var(--primary); font-size: 11px; }

        /* Loading skeleton */
        .dp-skeleton { background: linear-gradient(90deg, #F3F4F6 25%, #E5E7EB 50%, #F3F4F6 75%); background-size: 200% 100%; animation: dpShimmer 1.4s infinite; border-radius: 18px; }
        @keyframes dpShimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
    </style>
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
const ID_PROPIEDAD = <?= $idPropiedad ?>;

(async function cargarDetalle() {
    const body = document.getElementById('dpBody');
    const titleBar = document.getElementById('dpTitleBar');

    try {
        const res  = await fetch(`../../apis/anfitrion/propiedades.php?accion=detalle&id=${ID_PROPIEDAD}`);
        const data = await res.json();

        if (!data.ok) {
            body.innerHTML = `<div style="text-align:center; padding:4rem; color:#9CA3AF;">
                <i class="fa-solid fa-circle-exclamation" style="font-size:3rem; margin-bottom:1rem; display:block; color:#E11D48;"></i>
                <h3 style="color:#111827;">${data.error || 'Propiedad no encontrada'}</h3>
                <a href="propiedades.php" style="color:var(--primary); font-weight:700;">← Volver a mis propiedades</a>
            </div>`;
            return;
        }

        const p         = data.propiedad;
        const imagenes  = data.imagenes  || [];
        const servicios = data.servicios || [];
        const reglas    = data.reglas    || [];
        const politicas = data.politicas || [];

        titleBar.textContent = p.vNombre;

        const precio    = parseFloat(p.dPrecioNoche || 0).toLocaleString('es-ES', {minimumFractionDigits: 0});
        const ubicacion = [p.ciudad, p.estado, p.pais].filter(Boolean).join(', ') || 'Sin ubicación';
        const BASE_IMG  = '../../';

        // ── Galería ──
        const imgPrincipal = imagenes[0] ? `${BASE_IMG}${imagenes[0]}` : 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80';
        const img2 = imagenes[1] ? `${BASE_IMG}${imagenes[1]}` : null;
        const img3 = imagenes[2] ? `${BASE_IMG}${imagenes[2]}` : null;

        const sideSlot = (src) => src
            ? `<img src="${src}" onerror="this.parentNode.innerHTML='<div class=dp-gallery-placeholder><i class=\\'fa-solid fa-image\\'></i></div>'">`
            : `<div class="dp-gallery-placeholder"><i class="fa-solid fa-image"></i></div>`;

        const galeriaHTML = `
        <div class="dp-gallery">
            <div class="dp-gallery-main">
                <img src="${imgPrincipal}" alt="${p.vNombre}"
                     onerror="this.src='https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80'">
            </div>
            <div class="dp-gallery-side">
                ${sideSlot(img2)}
                ${sideSlot(img3)}
            </div>
        </div>`;

        // ── Info header ──
        const infoHTML = `
        <div class="dp-info-header">
            <div>
                <h1 class="dp-nombre">${p.vNombre}</h1>
                <p class="dp-ubicacion"><i class="fa-solid fa-location-dot"></i>${ubicacion}</p>
                ${p.tipo ? `<span class="dp-tipo-badge" style="display:inline-block; margin-top:0.6rem;">${p.tipo}</span>` : ''}
            </div>
            <div class="dp-precio-box">
                <div class="dp-precio">€${precio}</div>
                <div class="dp-precio-sub">por noche</div>
            </div>
        </div>`;

        // ── Stats ──
        const statsHTML = `
        <div class="dp-stats">
            <div class="dp-stat"><i class="fa-solid fa-bed"></i> ${p.iNumeroHabitaciones || '—'} Habitaciones</div>
            <div class="dp-stat"><i class="fa-solid fa-users"></i> ${p.iCapacidadHuespedes || '—'} Huéspedes</div>
            ${p.vDireccion ? `<div class="dp-stat"><i class="fa-solid fa-map-pin"></i> ${p.vDireccion}</div>` : ''}
        </div>`;

        // ── Descripción ──
        const descHTML = (p.vDescripcion || p.vEspecificaciones) ? `
        <div class="dp-section">
            <h2 class="dp-section-title"><i class="fa-solid fa-pen-nib"></i> Descripción</h2>
            ${p.vDescripcion ? `<p class="dp-descripcion">${p.vDescripcion}</p>` : ''}
            ${p.vEspecificaciones ? `<p class="dp-descripcion" style="margin-top:1rem; padding-top:1rem; border-top:1px solid #F3F4F6;"><strong>Especificaciones:</strong><br>${p.vEspecificaciones}</p>` : ''}
        </div>` : '';

        // ── Chips helper ──
        const chips = (arr, icon) => arr.length
            ? arr.map(n => `<span class="dp-chip"><i class="fa-solid ${icon}"></i>${n}</span>`).join('')
            : '<span style="color:#9CA3AF; font-size:13px;">No registrados</span>';

        // ── Servicios ──
        const servHTML = `
        <div class="dp-section">
            <h2 class="dp-section-title"><i class="fa-solid fa-list-check"></i> Servicios</h2>
            <div class="dp-chip-grid">${chips(servicios, 'fa-check')}</div>
        </div>`;

        // ── Reglas ──
        const regHTML = `
        <div class="dp-section">
            <h2 class="dp-section-title"><i class="fa-solid fa-shield-halved"></i> Reglas</h2>
            <div class="dp-chip-grid">${chips(reglas, 'fa-circle-dot')}</div>
        </div>`;

        // ── Políticas ──
        const polHTML = `
        <div class="dp-section">
            <h2 class="dp-section-title"><i class="fa-solid fa-file-contract"></i> Políticas</h2>
            <div class="dp-chip-grid">${chips(politicas, 'fa-file-lines')}</div>
        </div>`;

        // ── Todas las imágenes ──
        const allImgsHTML = imagenes.length > 1 ? `
        <div class="dp-section">
            <h2 class="dp-section-title"><i class="fa-solid fa-images"></i> Todas las fotos (${imagenes.length})</h2>
            <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap:1rem;">
                ${imagenes.map(img => `
                    <div style="aspect-ratio:1; border-radius:12px; overflow:hidden; border:1px solid #E5E7EB;">
                        <img src="${BASE_IMG}${img}" style="width:100%;height:100%;object-fit:cover;"
                             onerror="this.parentNode.style.background='#F3F4F6'">
                    </div>`).join('')}
            </div>
        </div>` : '';

        body.innerHTML = galeriaHTML + infoHTML + statsHTML + descHTML + servHTML + regHTML + polHTML + allImgsHTML;

    } catch (err) {
        console.error(err);
        body.innerHTML = `<p style="color:#ef4444; padding:2rem;">Error al cargar el detalle.</p>`;
    }
})();
</script>
</body>
</html>
