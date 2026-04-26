let fechaActual = new Date();
let mesActual = fechaActual.getMonth() + 1;
let anioActual = fechaActual.getFullYear();
let eventosMes = [];
let propiedadesUsuario = []; // Nueva variable para almacenar propiedades
let rangoSeleccionado = { inicio: null, fin: null };
let bloqueoSeleccionadoId = null;

const meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

async function init() {
    // 1. Cargar propiedades del anfitrión
    try {
        const res = await fetch('../../apis/anfitrion/propiedades.php?accion=listar');
        const data = await res.json();
        const select = document.getElementById('propiedadSelect');
        if (data.ok && data.propiedades.length > 0) {
            propiedadesUsuario = data.propiedades; // Guardamos la lista completa
            select.innerHTML = data.propiedades.map(p => `<option value="${p.idPropiedad}">${p.vNombre}</option>`).join('');
            cargarCalendario();
        } else {
            select.innerHTML = '<option value="">No tienes propiedades</option>';
            document.getElementById('calendarioGrid').innerHTML = '<p style="grid-column: span 7; text-align:center; padding: 2rem;">No tienes propiedades para mostrar el calendario.</p>';
        }
    } catch (e) {
        console.error("Error cargando propiedades", e);
    }
}

// Helper para obtener la propiedad seleccionada
function getPropiedadActual() {
    const select = document.getElementById('propiedadSelect');
    if (!select || !select.value) return null;
    return propiedadesUsuario.find(p => String(p.idPropiedad) === String(select.value));
}

async function cargarCalendario() {
    const idPropiedad = document.getElementById('propiedadSelect').value;
    if (!idPropiedad) return;

    // Reiniciar selección y panel de detalles al cambiar de propiedad
    rangoSeleccionado = { inicio: null, fin: null };
    document.getElementById('detalleFechaLabel').innerText = 'Selecciona un día';
    document.getElementById('detalleEstado').innerHTML = '<span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span> Selecciona un día';
    document.getElementById('detalleEstado').style.color = '#065f46';
    
    // Mostrar tarifa de la propiedad seleccionada inmediatamente
    const prop = getPropiedadActual();
    const detalleTarifa = document.getElementById('detalleTarifa');
    if (prop && prop.dPrecioNoche) {
        const precioVal = parseFloat(prop.dPrecioNoche);
        detalleTarifa.innerText = `$${precioVal.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    } else {
        detalleTarifa.innerText = '$—';
    }

    document.getElementById('detalleDesbloquearBtn').style.display = 'none';

    document.getElementById('mesAnioLabel').innerText = `${meses[mesActual - 1]} ${anioActual}`;

    // Deshabilitar botón de retroceso si es el mes actual
    const hoy = new Date();
    const mesHoy = hoy.getMonth() + 1;
    const anioHoy = hoy.getFullYear();
    const prevBtn = document.querySelector('.fa-chevron-left');
    
    if (anioActual <= anioHoy && mesActual <= mesHoy) {
        prevBtn.style.opacity = '0.2';
        prevBtn.style.cursor = 'not-allowed';
    } else {
        prevBtn.style.opacity = '1';
        prevBtn.style.cursor = 'pointer';
    }

    // 2. Fetch eventos del mes
    try {
        const res = await fetch(`../../apis/anfitrion/calendario.php?accion=obtener_eventos&idPropiedad=${idPropiedad}&mes=${mesActual}&anio=${anioActual}`);
        const data = await res.json();
        eventosMes = data.ok ? data.eventos : [];
    } catch (e) {
        console.error("Error fetching eventos", e);
        eventosMes = [];
    }

    renderGrid();
    renderEventosList();
}

function renderGrid() {
    const grid = document.getElementById('calendarioGrid');
    
    const labelsHTML = `
        <span class="cal-day-label">Lun</span><span class="cal-day-label">Mar</span><span class="cal-day-label">Mié</span>
        <span class="cal-day-label">Jue</span><span class="cal-day-label">Vie</span><span class="cal-day-label">Sáb</span><span class="cal-day-label">Dom</span>
    `;

    let html = labelsHTML;

    const primerDiaMes = new Date(anioActual, mesActual - 1, 1);
    const ultimoDiaMes = new Date(anioActual, mesActual, 0);
    const diasMesAnterior = new Date(anioActual, mesActual - 1, 0).getDate();
    
    let diaSemanaInicio = primerDiaMes.getDay() === 0 ? 7 : primerDiaMes.getDay();
    
    // Días del mes anterior
    let celdasPrevias = diaSemanaInicio - 1;
    for (let i = celdasPrevias; i > 0; i--) {
        const d = diasMesAnterior - i + 1;
        html += `<div class="cal-day-box not-current"><span>${d}</span></div>`;
    }

    // Días del mes actual
    // Obtener precio de la propiedad seleccionada una sola vez para el grid
    const prop = getPropiedadActual();
    const precioBase = (prop && prop.dPrecioNoche) ? `$${parseFloat(prop.dPrecioNoche).toLocaleString('es-MX', { minimumFractionDigits: 0 })}` : '';

    for (let d = 1; d <= ultimoDiaMes.getDate(); d++) {
        const fechaStr = `${anioActual}-${String(mesActual).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
        
        let clase = '';
        let info = precioBase || 'Disponible';
        let resId = null;
        let blqId = null;

        for (let ev of eventosMes) {
            if (fechaStr >= ev.inicio && fechaStr <= ev.fin) {
                if (ev.tipo === 'reserva') {
                    clase = 'reserved';
                    info = `Reserva: ${ev.nombre}`;
                    resId = ev.id;
                } else if (ev.tipo === 'bloqueo') {
                    clase = 'blocked';
                    info = 'Bloqueado';
                    blqId = ev.id;
                }
            }
        }

        html += `<div class="cal-day-box ${clase}" id="dia-${fechaStr}" onclick="seleccionarDia('${fechaStr}', '${clase}', '${info}', ${blqId})">
                    <span>${d}</span>
                    <span class="price">${info}</span>
                 </div>`;
    }

    // Días del mes siguiente para completar 42 celdas (6 filas)
    const celdasActuales = celdasPrevias + ultimoDiaMes.getDate();
    const celdasFaltantes = 42 - celdasActuales;
    
    for (let d = 1; d <= celdasFaltantes; d++) {
        html += `<div class="cal-day-box not-current"><span>${d}</span></div>`;
    }

    grid.innerHTML = html;
    marcarRangoEnGrid();
}

function renderEventosList() {
    const contenedor = document.getElementById('listaEventosMes');
    if (eventosMes.length === 0) {
        contenedor.innerHTML = '<p style="font-size: 13px; color: #94a3b8; text-align: center;">No hay eventos este mes</p>';
        return;
    }
    
    let html = '';
    for (let ev of eventosMes) {
        if (ev.tipo === 'reserva') {
            html += `
            <div class="event-item" style="display: flex; gap: 1rem; padding: 1rem; border-radius: 12px; margin-bottom: 0.5rem; background: #f8fafc;">
                <div style="background: #eff6ff; color: var(--primary); padding: 8px; border-radius: 8px; height: fit-content;"><i class="fa-solid fa-user-check"></i></div>
                <div>
                    <div style="font-size: 13px; font-weight: 700;">Reserva: ${ev.nombre}</div>
                    <div style="font-size: 11px; color: #94a3b8;">${ev.inicio} al ${ev.fin}</div>
                </div>
            </div>`;
        } else if (ev.tipo === 'bloqueo') {
            html += `
            <div class="event-item" style="display: flex; gap: 1rem; padding: 1rem; border-radius: 12px; margin-bottom: 0.5rem; background: #f8fafc;">
                <div style="background: #f1f5f9; color: #64748b; padding: 8px; border-radius: 8px; height: fit-content;"><i class="fa-solid fa-calendar-xmark"></i></div>
                <div>
                    <div style="font-size: 13px; font-weight: 700;">Bloqueo: ${ev.motivo || 'Manual'}</div>
                    <div style="font-size: 11px; color: #94a3b8;">${ev.inicio} al ${ev.fin}</div>
                </div>
            </div>`;
        }
    }
    contenedor.innerHTML = html;
}

function cambiarMes(delta) {
    let nuevoMes = mesActual + delta;
    let nuevoAnio = anioActual;
    
    if (nuevoMes > 12) { nuevoMes = 1; nuevoAnio++; }
    if (nuevoMes < 1) { nuevoMes = 12; nuevoAnio--; }

    // No permitir ir a meses anteriores al actual
    const hoy = new Date();
    const mesHoy = hoy.getMonth() + 1;
    const anioHoy = hoy.getFullYear();

    if (nuevoAnio < anioHoy || (nuevoAnio === anioHoy && nuevoMes < mesHoy)) {
        return;
    }

    mesActual = nuevoMes;
    anioActual = nuevoAnio;
    cargarCalendario();
}

function seleccionarDia(fecha, clase, info, blqId) {
    if (clase === 'reserved' || clase === 'blocked') {
        rangoSeleccionado = { inicio: fecha, fin: fecha };
    } else {
        if (!rangoSeleccionado.inicio || (rangoSeleccionado.inicio && rangoSeleccionado.fin)) {
            rangoSeleccionado.inicio = fecha;
            rangoSeleccionado.fin = null;
        } else {
            if (fecha >= rangoSeleccionado.inicio) {
                rangoSeleccionado.fin = fecha;
            } else {
                rangoSeleccionado.fin = rangoSeleccionado.inicio;
                rangoSeleccionado.inicio = fecha;
            }
        }
    }
    
    marcarRangoEnGrid();
    
    let labelRango = rangoSeleccionado.inicio;
    if (rangoSeleccionado.fin && rangoSeleccionado.fin !== rangoSeleccionado.inicio) {
        labelRango += ' al ' + rangoSeleccionado.fin;
    }
    
    document.getElementById('detalleFechaLabel').innerText = labelRango;
    
    const estadoDiv = document.getElementById('detalleEstado');
    const desbloquearBtn = document.getElementById('detalleDesbloquearBtn');
    const detalleTarifa = document.getElementById('detalleTarifa');
    bloqueoSeleccionadoId = blqId;

    const prop = getPropiedadActual();
    const precio = (prop && prop.dPrecioNoche) ? `$${parseFloat(prop.dPrecioNoche).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` : '$—';

    if (clase === 'reserved') {
        estadoDiv.innerHTML = `<span style="width: 8px; height: 8px; border-radius: 50%; background: #3b82f6;"></span> ${info}`;
        estadoDiv.style.color = '#1e3a8a';
        desbloquearBtn.style.display = 'none';
        detalleTarifa.innerText = precio;
    } else if (clase === 'blocked') {
        estadoDiv.innerHTML = `<span style="width: 8px; height: 8px; border-radius: 50%; background: #94a3b8;"></span> ${info}`;
        estadoDiv.style.color = '#475569';
        desbloquearBtn.style.display = 'block';
        detalleTarifa.innerText = 'Bloqueado';
    } else {
        let txtEstado = rangoSeleccionado.fin ? 'Rango seleccionado' : 'Disponible para reserva';
        estadoDiv.innerHTML = `<span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span> ${txtEstado}`;
        estadoDiv.style.color = '#065f46';
        desbloquearBtn.style.display = 'none';
        detalleTarifa.innerText = precio;
    }
}

function formatearFecha(d) {
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
}

function marcarRangoEnGrid() {
    document.querySelectorAll('.cal-day-box.selected').forEach(el => el.classList.remove('selected'));

    if (rangoSeleccionado.inicio && !rangoSeleccionado.fin) {
        const box = document.getElementById(`dia-${rangoSeleccionado.inicio}`);
        if (box) box.classList.add('selected');
    } else if (rangoSeleccionado.inicio && rangoSeleccionado.fin) {
        let fIni = new Date(rangoSeleccionado.inicio + 'T00:00:00');
        let fFin = new Date(rangoSeleccionado.fin + 'T00:00:00');
        let current = new Date(fIni);
        
        while (current <= fFin) {
            let fStr = formatearFecha(current);
            const box = document.getElementById(`dia-${fStr}`);
            if (box) {
                box.classList.add('selected');
            }
            current.setDate(current.getDate() + 1);
        }
    }
}

// === Modal Bloqueo ===
function abrirModalBloqueo() {
    document.getElementById('modalBloqueo').classList.add('active');
    if (rangoSeleccionado.inicio) {
        document.getElementById('blqInicio').value = rangoSeleccionado.inicio;
        document.getElementById('blqFin').value = rangoSeleccionado.fin || rangoSeleccionado.inicio;
    }
}

function cerrarModalBloqueo() {
    document.getElementById('modalBloqueo').classList.remove('active');
}

async function guardarBloqueo(e) {
    e.preventDefault();
    const idPropiedad = document.getElementById('propiedadSelect').value;
    const fechaInicio = document.getElementById('blqInicio').value;
    const fechaFin = document.getElementById('blqFin').value;
    const motivo = document.getElementById('blqMotivo').value;

    // Validación extra en el cliente
    const hayReserva = eventosMes.some(ev => {
        if (ev.tipo !== 'reserva') return false;
        // Solapamiento: (A.inicio <= B.fin) && (A.fin >= B.inicio)
        return (fechaInicio <= ev.fin && fechaFin >= ev.inicio);
    });

    if (hayReserva) {
        alert('No puedes bloquear fechas que ya tienen una reserva de un huésped.');
        return;
    }

    const formData = new FormData();
    formData.append('accion', 'bloquear_fechas');
    formData.append('idPropiedad', idPropiedad);
    formData.append('fechaInicio', fechaInicio);
    formData.append('fechaFin', fechaFin);
    formData.append('motivo', motivo);

    try {
        const res = await fetch('../../apis/anfitrion/calendario.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.ok) {
            cerrarModalBloqueo();
            cargarCalendario();
            alert('Fechas bloqueadas correctamente.');
        } else {
            alert(data.error || 'Error al bloquear fechas');
        }
    } catch (e) {
        alert('Error de conexión');
    }
}

async function desbloquearFechaActual() {
    if (!bloqueoSeleccionadoId) return;
    if (!confirm('¿Seguro que deseas desbloquear estas fechas?')) return;

    const idPropiedad = document.getElementById('propiedadSelect').value;
    const formData = new FormData();
    formData.append('accion', 'desbloquear');
    formData.append('idPropiedad', idPropiedad);
    formData.append('idDisponibilidad', bloqueoSeleccionadoId);

    try {
        const res = await fetch('../../apis/anfitrion/calendario.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.ok) {
            cargarCalendario();
            document.getElementById('detalleDesbloquearBtn').style.display = 'none';
            document.getElementById('detalleEstado').innerHTML = `<span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span> Disponible para reserva`;
        } else {
            alert(data.error);
        }
    } catch (e) {
        alert('Error al desbloquear');
    }
}

// Start
document.addEventListener('DOMContentLoaded', init);
