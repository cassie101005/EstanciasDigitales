let fechaActual = new Date();
let mesActual = fechaActual.getMonth() + 1;
let anioActual = fechaActual.getFullYear();
let eventosMes = [];
let tarifasMes = [];
let propiedadesUsuario = [];
let rangoSeleccionado = { inicio: null, fin: null };
let bloqueoSeleccionadoId = null;

const meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

async function init() {
    try {
        const res = await fetch('../../apis/anfitrion/propiedades.php?accion=listar');
        const data = await res.json();
        const select = document.getElementById('propiedadSelect');
        if (data.ok && data.propiedades.length > 0) {
            propiedadesUsuario = data.propiedades;
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

function getPropiedadActual() {
    const select = document.getElementById('propiedadSelect');
    if (!select || !select.value) return null;
    return propiedadesUsuario.find(p => String(p.idPropiedad) === String(select.value));
}

async function cargarCalendario() {
    const idPropiedad = document.getElementById('propiedadSelect').value;
    if (!idPropiedad) return;

    rangoSeleccionado = { inicio: null, fin: null };
    document.getElementById('detalleFechaLabel').innerText = 'Selecciona un día';
    document.getElementById('detalleEstado').innerHTML = '<span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span> Selecciona un día';
    document.getElementById('detalleEstado').style.color = '#065f46';

    const prop = getPropiedadActual();
    const detalleTarifa = document.getElementById('detalleTarifa');
    if (prop && prop.dPrecioNoche) {
        detalleTarifa.innerText = `$${parseFloat(prop.dPrecioNoche).toLocaleString('es-MX', { minimumFractionDigits: 2 })}`;
    } else {
        detalleTarifa.innerText = '$—';
    }

    document.getElementById('detalleDesbloquearBtn').style.display = 'none';
    document.getElementById('mesAnioLabel').innerText = `${meses[mesActual - 1]} ${anioActual}`;

    actualizarBotonesNav();

    try {
        const res = await fetch(`../../apis/anfitrion/calendario.php?accion=obtener_eventos&idPropiedad=${idPropiedad}&mes=${mesActual}&anio=${anioActual}`);
        const data = await res.json();
        eventosMes = data.ok ? data.eventos : [];
        tarifasMes = data.ok ? data.tarifas : [];
    } catch (e) {
        console.error("Error fetching eventos", e);
        eventosMes = [];
        tarifasMes = [];
    }

    renderGrid();
    renderEventosList();
}

function renderGrid() {
    const grid = document.getElementById('calendarioGrid');
    const hoy = new Date();
    hoy.setHours(0,0,0,0);

    const labelsHTML = `
        <span class="cal-day-label">Lun</span><span class="cal-day-label">Mar</span><span class="cal-day-label">Mié</span>
        <span class="cal-day-label">Jue</span><span class="cal-day-label">Vie</span><span class="cal-day-label">Sáb</span><span class="cal-day-label">Dom</span>
    `;

    let html = labelsHTML;

    const primerDiaMes = new Date(anioActual, mesActual - 1, 1);
    const ultimoDiaMes = new Date(anioActual, mesActual, 0);
    const diasMesAnterior = new Date(anioActual, mesActual - 1, 0).getDate();

    let diaSemanaInicio = primerDiaMes.getDay() === 0 ? 7 : primerDiaMes.getDay();

    for (let i = diaSemanaInicio - 1; i > 0; i--) {
        const d = diasMesAnterior - i + 1;
        html += `<div class="cal-day-box not-current"><span>${d}</span></div>`;
    }

    const prop = getPropiedadActual();
    const precioBase = (prop && prop.dPrecioNoche) ? parseFloat(prop.dPrecioNoche) : 0;

    for (let d = 1; d <= ultimoDiaMes.getDate(); d++) {
        const actualDate = new Date(anioActual, mesActual - 1, d);
        const fechaStr = `${anioActual}-${String(mesActual).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
        
        const esPasado = actualDate < hoy;
        
        let clase = esPasado ? 'past disabled' : 'available selectable';
        let infoPrecio = precioBase;
        let infoTxt = `$${precioBase.toLocaleString('es-MX', { minimumFractionDigits: 0 })}`;
        let blqId = null;

        // Buscar tarifas especiales
        for (let t of tarifasMes) {
            if (fechaStr >= t.inicio && fechaStr <= t.fin) {
                infoPrecio = parseFloat(t.precio);
                infoTxt = `$${infoPrecio.toLocaleString('es-MX', { minimumFractionDigits: 0 })}*`;
            }
        }

        // Buscar eventos (reservas/bloqueos)
        for (let ev of eventosMes) {
            if (fechaStr >= ev.inicio && fechaStr <= ev.fin) {
                if (ev.tipo === 'reserva') {
                    clase = 'reserved';
                    infoTxt = 'Reservado';
                } else if (ev.tipo === 'bloqueo') {
                    clase = 'blocked';
                    infoTxt = 'Bloqueado';
                    blqId = ev.id;
                }
            }
        }

        const clickHandler = esPasado ? '' : `onclick="seleccionarDia('${fechaStr}', '${clase}', '${infoTxt}', ${blqId}, ${infoPrecio})"`;

        html += `<div class="cal-day-box ${clase}" id="dia-${fechaStr}" ${clickHandler}>
                    <span>${d}</span>
                    <span class="price">${infoTxt}</span>
                 </div>`;
    }

    const celdasFaltantes = 42 - (diaSemanaInicio - 1 + ultimoDiaMes.getDate());
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
    eventosMes.sort((a,b) => a.inicio.localeCompare(b.inicio)).forEach(ev => {
        const icon = ev.tipo === 'reserva' ? 'fa-user-check' : 'fa-calendar-xmark';
        const bg = ev.tipo === 'reserva' ? '#eff6ff' : '#f1f5f9';
        const color = ev.tipo === 'reserva' ? 'var(--primary)' : '#64748b';
        
        html += `
        <div class="event-item" style="display: flex; gap: 1rem; padding: 1rem; border-radius: 12px; margin-bottom: 0.5rem; background: #f8fafc;">
            <div style="background: ${bg}; color: ${color}; padding: 8px; border-radius: 8px; height: fit-content;"><i class="fa-solid ${icon}"></i></div>
            <div>
                <div style="font-size: 13px; font-weight: 700;">${ev.tipo === 'reserva' ? 'Reserva: ' + ev.nombre : 'Bloqueo: ' + (ev.motivo || 'Manual')}</div>
                <div style="font-size: 11px; color: #94a3b8;">${ev.inicio} al ${ev.fin}</div>
            </div>
        </div>`;
    });
    contenedor.innerHTML = html;
}

function seleccionarDia(fecha, clase, info, blqId, precio) {
    if (clase.includes('disabled')) return;

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
    actualizarPanelDetalle(clase, info, blqId, precio);
}

function actualizarPanelDetalle(clase, info, blqId, precio) {
    let labelRango = rangoSeleccionado.inicio;
    if (rangoSeleccionado.fin && rangoSeleccionado.fin !== rangoSeleccionado.inicio) {
        labelRango += ' al ' + rangoSeleccionado.fin;
    }
    document.getElementById('detalleFechaLabel').innerText = labelRango;

    const estadoDiv = document.getElementById('detalleEstado');
    const desbloquearBtn = document.getElementById('detalleDesbloquearBtn');
    const detalleTarifa = document.getElementById('detalleTarifa');
    bloqueoSeleccionadoId = blqId;

    if (clase === 'reserved') {
        estadoDiv.innerHTML = `<span style="width: 8px; height: 8px; border-radius: 50%; background: #3b82f6;"></span> ${info}`;
        estadoDiv.style.color = '#1e3a8a';
        desbloquearBtn.style.display = 'none';
        detalleTarifa.innerText = 'Reservado';
    } else if (clase === 'blocked') {
        estadoDiv.innerHTML = `<span style="width: 8px; height: 8px; border-radius: 50%; background: #94a3b8;"></span> ${info}`;
        estadoDiv.style.color = '#475569';
        desbloquearBtn.style.display = 'block';
        detalleTarifa.innerText = 'Bloqueado';
    } else {
        const txtEstado = rangoSeleccionado.fin ? 'Rango seleccionado' : 'Disponible';
        estadoDiv.innerHTML = `<span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span> ${txtEstado}`;
        estadoDiv.style.color = '#065f46';
        desbloquearBtn.style.display = 'none';
        detalleTarifa.innerText = `$${precio.toLocaleString('es-MX', { minimumFractionDigits: 2 })}`;
    }
}

function marcarRangoEnGrid() {
    document.querySelectorAll('.cal-day-box.selected').forEach(el => el.classList.remove('selected'));
    if (!rangoSeleccionado.inicio) return;

    const start = new Date(rangoSeleccionado.inicio + 'T00:00:00');
    const end = rangoSeleccionado.fin ? new Date(rangoSeleccionado.fin + 'T00:00:00') : start;
    
    let current = new Date(start);
    while (current <= end) {
        const fStr = `${current.getFullYear()}-${String(current.getMonth() + 1).padStart(2, '0')}-${String(current.getDate()).padStart(2, '0')}`;
        const box = document.getElementById(`dia-${fStr}`);
        if (box) box.classList.add('selected');
        current.setDate(current.getDate() + 1);
    }
}

function actualizarBotonesNav() {
    const prevBtn = document.getElementById('prevMonthBtn');
    const hoy = new Date();
    const mesHoy = hoy.getMonth() + 1;
    const anioHoy = hoy.getFullYear();

    if (anioActual <= anioHoy && mesActual <= mesHoy) {
        prevBtn.style.opacity = '0.35';
        prevBtn.style.cursor = 'not-allowed';
        prevBtn.classList.add('disabled-nav');
    } else {
        prevBtn.style.opacity = '1';
        prevBtn.style.cursor = 'pointer';
        prevBtn.classList.remove('disabled-nav');
    }
}

function cambiarMes(delta) {
    if (delta === -1) {
        const hoy = new Date();
        const mesHoy = hoy.getMonth() + 1;
        const anioHoy = hoy.getFullYear();
        if (anioActual <= anioHoy && mesActual <= mesHoy) return;
    }

    let nuevoMes = mesActual + delta;
    let nuevoAnio = anioActual;
    if (nuevoMes > 12) { nuevoMes = 1; nuevoAnio++; }
    if (nuevoMes < 1) { nuevoMes = 12; nuevoAnio--; }

    mesActual = nuevoMes;
    anioActual = nuevoAnio;
    cargarCalendario();
}

// === Modales ===
function abrirModalBloqueo() {
    if (!rangoSeleccionado.inicio) { alert('Selecciona una fecha o rango primero.'); return; }
    document.getElementById('modalBloqueo').classList.add('active');
    document.getElementById('blqInicio').value = rangoSeleccionado.inicio;
    document.getElementById('blqFin').value = rangoSeleccionado.fin || rangoSeleccionado.inicio;
}

function cerrarModalBloqueo() { document.getElementById('modalBloqueo').classList.remove('active'); }

function abrirModalTarifa() {
    if (!rangoSeleccionado.inicio) { alert('Selecciona una fecha o rango primero.'); return; }
    document.getElementById('modalTarifa').classList.add('active');
    document.getElementById('trfInicio').value = rangoSeleccionado.inicio;
    document.getElementById('trfFin').value = rangoSeleccionado.fin || rangoSeleccionado.inicio;
    
    const prop = getPropiedadActual();
    document.getElementById('trfPrecio').value = prop ? prop.dPrecioNoche : 0;
}

function cerrarModalTarifa() { document.getElementById('modalTarifa').classList.remove('active'); }

async function guardarBloqueo(e) {
    e.preventDefault();
    const idProp = document.getElementById('propiedadSelect').value;
    const fIni = document.getElementById('blqInicio').value;
    const fFin = document.getElementById('blqFin').value;
    const motivo = document.getElementById('blqMotivo').value;

    const res = await fetchApi('bloquear_fechas', { idPropiedad: idProp, fechaInicio: fIni, fechaFin: fFin, motivo });
    if (res.ok) { cerrarModalBloqueo(); cargarCalendario(); alert('Fechas bloqueadas.'); }
    else alert(res.error || 'Error');
}

async function guardarTarifa(e) {
    e.preventDefault();
    const idProp = document.getElementById('propiedadSelect').value;
    const fIni = document.getElementById('trfInicio').value;
    const fFin = document.getElementById('trfFin').value;
    const precio = document.getElementById('trfPrecio').value;

    if (!confirm('¿Confirmas que deseas aplicar esta tarifa especial?')) return;

    const res = await fetchApi('ajustar_tarifa', { idPropiedad: idProp, fechaInicio: fIni, fechaFin: fFin, precio });
    if (res.ok) { cerrarModalTarifa(); cargarCalendario(); alert('Tarifa actualizada.'); }
    else alert(res.error || 'Error');
}

async function desbloquearFechaActual() {
    if (!bloqueoSeleccionadoId) return;
    if (!confirm('¿Seguro que deseas eliminar este bloqueo?')) return;
    const idProp = document.getElementById('propiedadSelect').value;
    const res = await fetchApi('desbloquear', { idPropiedad: idProp, idDisponibilidad: bloqueoSeleccionadoId });
    if (res.ok) cargarCalendario();
    else alert(res.error);
}

async function fetchApi(accion, params) {
    const fd = new FormData();
    fd.append('accion', accion);
    for (let k in params) fd.append(k, params[k]);
    try {
        const r = await fetch('../../apis/anfitrion/calendario.php', { method: 'POST', body: fd });
        return await r.json();
    } catch(e) { return { error: 'Error de conexión' }; }
}

document.addEventListener('DOMContentLoaded', init);
