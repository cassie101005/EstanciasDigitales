(async function cargarDetalle() {
    const body = document.getElementById('dpBody');
    const titleBar = document.getElementById('dpTitleBar');

    try {
        const res  = await fetch(`../../apis/anfitrion/propiedades.php?accion=detalle&id=${window.ID_PROPIEDAD}`);
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
        const imgPrincipal = imagenes[0] ? `${BASE_IMG}${imagenes[0].replace(/ /g, '%20')}` : 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80';
        const img2 = imagenes[1] ? `${BASE_IMG}${imagenes[1].replace(/ /g, '%20')}` : null;
        const img3 = imagenes[2] ? `${BASE_IMG}${imagenes[2].replace(/ /g, '%20')}` : null;

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
                <div class="dp-precio">$${precio}</div>
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
                        <img src="${BASE_IMG}${img.replace(/ /g, '%20')}" style="width:100%;height:100%;object-fit:cover;"
                             onerror="this.parentNode.style.background='#F3F4F6'">
                    </div>`).join('')}
            </div>
        </div>` : '';

        // ── Reseñas ──
        const resHTML = `
        <div class="dp-section">
            <h2 class="dp-section-title"><i class="fa-solid fa-comments"></i> Reseñas de huéspedes (${data.resenias.length})</h2>
            ${data.resenias.length ? `
                <div style="display:grid; gap:1.5rem;">
                    ${data.resenias.map(r => `
                        <div style="padding:1.25rem; background:#F9FAFB; border-radius:14px; border:1px solid #F3F4F6;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <img src="${r.vFoto ? '../../'+r.vFoto : 'https://i.pravatar.cc/100?u='+r.idUsuario}" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">
                                    <div>
                                        <p style="font-size:13px;font-weight:700;margin:0;">${r.vNombre} ${r.vApellido}</p>
                                        <p style="font-size:11px;color:#9CA3AF;margin:0;">${new Date(r.dtFechaResenia).toLocaleDateString()}</p>
                                    </div>
                                </div>
                                <div style="color:#FBBF24; font-size:12px;">
                                    ${'<i class="fa-solid fa-star"></i>'.repeat(r.iCalificacion)}
                                </div>
                            </div>
                            <p style="font-size:13px; color:#475569; margin:0; line-height:1.5; font-style:italic;">"${r.vComentario}"</p>
                        </div>
                    `).join('')}
                </div>
            ` : '<p style="color:#9CA3AF;font-size:13px;">No hay reseñas aún.</p>'}
        </div>`;

        body.innerHTML = galeriaHTML + infoHTML + statsHTML + descHTML + servHTML + regHTML + polHTML + allImgsHTML + resHTML;

    } catch (err) {
        console.error(err);
        body.innerHTML = `<p style="color:#ef4444; padding:2rem;">Error al cargar el detalle.</p>`;
    }
})();
