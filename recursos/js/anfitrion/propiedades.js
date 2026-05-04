let allProperties = [];
let currentView = 'grid'; // 'grid' o 'list'
let currentFilter = 'all';

document.addEventListener('DOMContentLoaded', () => {
    setupEventListeners();
    init();
});

async function init() {
    // Primero cargamos las propiedades
    await fetchProperties();
}

async function fetchProperties() {
    const grid = document.getElementById('gridPropiedades');
    try {
        const res = await fetch(`../../apis/anfitrion/propiedades.php?accion=listar&_=${Date.now()}`);
        const data = await res.json();

        if (data.ok) {
            allProperties = data.propiedades;
            // Después de tener las propiedades, cargamos los tipos dinámicos basados en los datos reales
            generarFiltrosTipo(allProperties);
            renderProperties();
        }
    } catch (err) {
        console.error('Error fetching properties:', err);
        grid.innerHTML = '<p style="color:#ef4444; padding:2rem;">Error al cargar propiedades.</p>';
    }
}

function generarFiltrosTipo(propiedades) {
    const menu = document.getElementById('menuTipos');
    if (!menu) return;

    // Limpiar opciones anteriores pero mantener "Todos"
    const existingOptions = menu.querySelectorAll('.tipo-option:not([data-id="all"])');
    existingOptions.forEach(opt => opt.remove());

    // Obtener tipos únicos de las propiedades cargadas
    // Usamos p.tipo que es el alias definido en la consulta SQL
    const tiposUnicos = [...new Set(propiedades.map(p => p.tipo).filter(Boolean))];

    tiposUnicos.sort().forEach(tipo => {
        const opt = document.createElement('div');
        opt.className = 'tipo-option';
        opt.dataset.tipo = tipo; // Usamos el nombre del tipo para filtrar
        opt.innerText = tipo;
        opt.style.cssText = 'padding: 0.6rem 1rem; border-radius: 8px; transition: background 0.2s; cursor: pointer; font-size: 13px; color: #475569; font-weight: 500;';
        
        opt.onmouseenter = () => opt.style.background = '#f1f5f9';
        opt.onmouseleave = () => opt.style.background = 'transparent';
        opt.onclick = (e) => {
            e.stopPropagation();
            applyFilter(tipo, tipo);
            menu.style.display = 'none';
        };
        
        menu.appendChild(opt);
    });
}

function renderProperties() {
    const grid = document.getElementById('gridPropiedades');
    const estadoVacio = document.getElementById('estadoVacio');
    
    const filtered = currentFilter === 'all' 
        ? allProperties 
        : allProperties.filter(p => p.tipo === currentFilter);

    grid.innerHTML = '';
    
    if (filtered.length === 0) {
        grid.style.display = 'none';
        estadoVacio.style.display = 'block';
        document.getElementById('kpi-total-propiedades').innerHTML = '0 <span style="font-size: 11px; background: #f1f5f9; color: #64748b; padding: 4px 10px; border-radius: 8px; margin-left: 10px;">Encontradas</span>';
        return;
    }

    grid.style.display = currentView === 'grid' ? 'grid' : 'flex';
    if (currentView === 'list') {
        grid.classList.add('view-list');
    } else {
        grid.classList.remove('view-list');
    }

    estadoVacio.style.display = 'none';
    document.getElementById('kpi-total-propiedades').innerHTML = filtered.length + ' <span style="font-size: 11px; background: #d1fae5; color: #065f46; padding: 4px 10px; border-radius: 8px; margin-left: 10px;">Activas</span>';

    filtered.forEach(p => {
        const imgSrc = p.imagenPrincipal
            ? `../../${p.imagenPrincipal.replace(/ /g, '%20')}`
            : 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=600&q=80';

        const ubicacion = [p.ciudad, p.estado].filter(Boolean).join(', ') || 'Sin ubicación';
        const precio = parseFloat(p.dPrecioNoche || 0).toLocaleString('es-ES', {minimumFractionDigits: 0});

        grid.innerHTML += `
            <div class="host-prop-card" style="cursor:pointer;"
                 onclick="window.location.href='detalle-propiedad.php?id=${p.idPropiedad}'">
                <div class="card-img-wrapper">
                    <img src="${imgSrc}" alt="${p.vNombre}" loading="lazy"
                         onerror="this.src='https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=600&q=80'">
                    <div class="card-actions">
                        <div class="btn-edit-float" onclick="event.stopPropagation(); window.location.href='editar-propiedad.php?id=${p.idPropiedad}'" title="Editar propiedad">
                            <i class="fa-solid fa-pencil"></i>
                        </div>
                        <div class="btn-delete-prop" onclick="event.stopPropagation(); deleteProperty(${p.idPropiedad})" title="Eliminar propiedad">
                            <i class="fa-solid fa-trash"></i>
                        </div>
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
}

function deleteProperty(id) {
    if (!confirm("¿Seguro que deseas eliminar esta propiedad?")) return;

    fetch(`../../apis/anfitrion/propiedades.php?accion=eliminar&id=${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.ok) {
                // Eliminar del array local y re-renderizar para no recargar toda la página
                allProperties = allProperties.filter(p => p.idPropiedad !== id);
                renderProperties();
            } else {
                alert("Error al eliminar la propiedad: " + (data.error || 'Inténtalo de nuevo.'));
            }
        })
        .catch(err => {
            console.error("Error deleting property:", err);
            alert("Error de red al intentar eliminar la propiedad.");
        });
}

function setupEventListeners() {
    const container = document.getElementById('containerFilterType');
    const menu = document.getElementById('menuTipos');
    
    if (container && menu) {
        container.onclick = (e) => {
            e.stopPropagation();
            const isVisible = menu.style.display === 'block';
            menu.style.display = isVisible ? 'none' : 'block';
        };

        document.addEventListener('click', () => {
            menu.style.display = 'none';
        });
    }

    const optAll = document.querySelector('.tipo-option[data-id="all"]');
    if (optAll) {
        optAll.onclick = (e) => {
            e.stopPropagation();
            applyFilter('all', 'Todos');
            menu.style.display = 'none';
        };
    }

    const btnGrid = document.getElementById('btnViewGrid');
    const btnList = document.getElementById('btnViewList');

    if (btnGrid) {
        btnGrid.onclick = () => {
            currentView = 'grid';
            btnGrid.classList.add('active');
            btnList.classList.remove('active');
            renderProperties();
        };
    }

    if (btnList) {
        btnList.onclick = () => {
            currentView = 'list';
            btnList.classList.add('active');
            btnGrid.classList.remove('active');
            renderProperties();
        };
    }
}

function applyFilter(id, label) {
    currentFilter = id;
    const labelEl = document.getElementById('currentTypeLabel');
    if (labelEl) labelEl.innerText = label;
    renderProperties();
}
