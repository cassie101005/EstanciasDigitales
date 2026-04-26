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
