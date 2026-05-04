/**
 * home.js — Lógica del Marketplace (Huésped)
 * recursos/js/huesped/home.js
 */
document.addEventListener('DOMContentLoaded', function () {

    // ── Validación de fechas en el buscador ─────────────────────────────
    const startInput = document.querySelector('input[name="fecha_inicio"]');
    const endInput   = document.querySelector('input[name="fecha_fin"]');

    if (startInput && endInput) {
        const now   = new Date();
        const today = now.toISOString().split('T')[0];

        startInput.setAttribute('min', today);

        const updateMinEnd = () => {
            if (startInput.value) {
                endInput.setAttribute('min', startInput.value);
                if (endInput.value && endInput.value < startInput.value) {
                    endInput.value = startInput.value;
                }
            } else {
                endInput.setAttribute('min', today);
            }
        };

        startInput.addEventListener('change', updateMinEnd);
        updateMinEnd();
    }

    // ── Estado de filtros (inyectado desde PHP) ──────────────────────────
    const filters = window._homeFilters || {
        ubicacion: '', huespedes: '', fecha_inicio: '', fecha_fin: '', categoria: ''
    };

    // ── Grid y estado actual ─────────────────────────────────────────────
    const grid = document.getElementById('mainGrid');

    // ── Renderizar tarjetas de propiedades ───────────────────────────────
    function renderCards(properties) {
        if (!properties || properties.length === 0) {
            grid.innerHTML = `
                <div style="grid-column: 1 / -1; text-align: center; padding: 4rem;">
                    <i class="fa-solid fa-house-circle-xmark" style="font-size: 3rem; color: #ddd; margin-bottom: 1.5rem;"></i>
                    <h2 style="color: #64748b;">No encontramos propiedades que coincidan con tu búsqueda.</h2>
                    <p style="color: #94a3b8; margin-top: 0.5rem;">Intenta cambiar los filtros o la ubicación.</p>
                    <button onclick="clearCategory()" style="display: inline-block; margin-top: 2rem; color: var(--primary); font-weight: 700; background: none; border: none; cursor: pointer; text-decoration: underline; font-size: 1rem;">Limpiar filtros</button>
                </div>`;
            return;
        }

        grid.innerHTML = properties.map(p => `
            <div class="prop-card-v2" onclick="window.location.href='detalle.php?id=${p.id}'">
                <div class="img-container">
                    <img src="${escapeHtml(p.img)}" alt="${escapeHtml(p.loc)}">
                </div>
                <div class="card-content">
                    <div class="card-content-top">
                        <div class="card-title">${escapeHtml(p.loc)}</div>
                        <div class="card-rating"><i class="fa-solid fa-star" style="font-size: 0.8rem;"></i> ${escapeHtml(String(p.rating))}</div>
                    </div>
                    <div class="card-desc">${escapeHtml(p.desc)}</div>
                    <div class="card-dates">${escapeHtml(p.dates)}</div>
                    <div class="card-price"><strong>${escapeHtml(p.price)}</strong> noche</div>
                </div>
            </div>`).join('');
    }

    function escapeHtml(text) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(text || ''));
        return d.innerHTML;
    }

    // ── Spinner de carga ─────────────────────────────────────────────────
    function showLoading() {
        grid.style.opacity = '0.4';
        grid.style.pointerEvents = 'none';
        grid.style.transition = 'opacity 0.2s';
    }

    function hideLoading() {
        grid.style.opacity = '1';
        grid.style.pointerEvents = '';
    }

    // ── Fetch de propiedades vía API ─────────────────────────────────────
    async function fetchProperties(categoria) {
        showLoading();

        const params = new URLSearchParams();
        if (filters.ubicacion)    params.set('ubicacion',    filters.ubicacion);
        if (filters.huespedes)    params.set('huespedes',    filters.huespedes);
        if (filters.fecha_inicio) params.set('fecha_inicio', filters.fecha_inicio);
        if (filters.fecha_fin)    params.set('fecha_fin',    filters.fecha_fin);
        if (categoria)            params.set('categoria',    categoria);

        try {
            const res  = await fetch('../../apis/huesped/propiedades.php?' + params.toString());
            const data = await res.json();
            hideLoading();
            if (data.ok) {
                renderCards(data.properties);
            }
        } catch (e) {
            hideLoading();
            console.error('Error al filtrar propiedades:', e);
        }
    }

    // ── Manejadores de categorías ─────────────────────────────────────────
    document.querySelectorAll('.cat-pill').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.cat-pill').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const categoria = this.dataset.categoria || '';
            filters.categoria = categoria;
            fetchProperties(categoria);
        });
    });

    // ── Función global para "limpiar filtros" desde la tarjeta vacía ──────
    window.clearCategory = function () {
        filters.categoria = '';
        document.querySelectorAll('.cat-pill').forEach(b => b.classList.remove('active'));
        const allBtn = document.querySelector('.cat-pill[data-categoria=""]');
        if (allBtn) allBtn.classList.add('active');
        fetchProperties('');
    };
});
