 <?php
session_start();
if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calendario de Disponibilidad | Modo Anfitrión</title>
    <link rel="stylesheet" href="../../recursos/css/variables.css">
    <link rel="stylesheet" href="../../recursos/css/layouts/shared.css">
    <link rel="stylesheet" href="../../recursos/css/components/navbar.css">
    <link rel="stylesheet" href="../../recursos/css/anfitrion/host_main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .modal-bloqueo { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; }
        .modal-bloqueo.active { display: flex; }
        .modal-bloqueo-content { background: white; padding: 2rem; border-radius: 16px; width: 400px; max-width: 90%; }
        .modal-bloqueo-content h3 { margin-top: 0; margin-bottom: 1.5rem; font-size: 1.25rem; font-weight: 800; }
        .form-group-modal { margin-bottom: 1rem; }
        .form-group-modal label { display: block; font-size: 12px; font-weight: 700; margin-bottom: 5px; color: #475569; }
        .modal-input { width: 100%; padding: 0.8rem; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; }
        .modal-actions { display: flex; gap: 1rem; margin-top: 1.5rem; }
        .btn-bloquear { flex: 1; background: #e11d48; color: white; border: none; padding: 0.8rem; border-radius: 8px; font-weight: 700; cursor: pointer; }
        .btn-cancelar { flex: 1; background: #f1f5f9; color: #475569; border: none; padding: 0.8rem; border-radius: 8px; font-weight: 700; cursor: pointer; }
        .calendar-days-grid { gap: 0; border-top: 1px solid #e2e8f0; border-left: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-top: 1rem; }
        .cal-day-label { background: white; padding: 1rem 0; margin-bottom: 0 !important; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; }
        .cal-day-label:last-child { border-right: none; }
        .cal-day-box { cursor: pointer; transition: all 0.2s; position: relative; border: none !important; border-right: 1px solid #e2e8f0 !important; border-bottom: 1px solid #e2e8f0 !important; }
        .cal-day-box:nth-child(7n) { border-right: none !important; }
        .cal-day-box:hover { background: #f8fafc; }
        .cal-day-box.not-current { background: #f8fafc; color: #94a3b8 !important; }
        .cal-day-box.selected { border: 2px solid var(--primary) !important; background: #f0f4ff; z-index: 10; }
        .cal-day-box.reserved { background: #eff6ff !important; color: var(--primary); }
        .cal-day-box.blocked { background: #f1f5f9 !important; color: #94a3b8; }
        .cal-day-box.blocked .price { color: #94a3b8; }
        .cal-day-box.reserved .price { font-weight: 700; }

        .price { display: block; font-size: 10px; margin-top: 5px; color: #64748b; }
        
        #propiedadSelect { border: none; background: transparent; font-size: 13px; font-weight: 700; color: var(--primary); outline: none; cursor: pointer; }
    </style>
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
                    <li class="side-nav-item" onclick="window.location.href='propiedades.php'"><i class="fa-solid fa-building"></i> Propiedades</li>
                    <li class="side-nav-item active" onclick="window.location.href='calendario.php'"><i class="fa-solid fa-calendar-days"></i> Calendario</li>
                    <li class="side-nav-item" onclick="window.location.href='reservas.php'"><i class="fa-solid fa-receipt"></i> Reservas</li>
                </nav>
                
            </div>
        </aside>

        <!-- Main Content -->
        <main class="host-content-main">
            <div style="padding: 2.5rem 4rem; max-width: 1600px; margin: 0 auto;">
                <header style="margin-bottom: 3rem;">
                    <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -1.5px; margin-bottom: 1rem;">Calendario de Disponibilidad</h1>
                    <div style="display: flex; gap: 1rem;">
                        <div class="filter-dropdown" style="background: #f1f5f9; padding: 0.6rem 1.25rem; border-radius: 99px; display: flex; align-items: center; gap: 0.5rem;">
                            <select id="propiedadSelect" onchange="cargarCalendario()"></select>
                        </div>
                    </div>
                </header>

                <div class="calendar-layout-grid">
                    <!-- Column 1: Calendar Grid -->
                    <section class="calendar-main-box" style="box-shadow: 0 10px 40px rgba(0,0,0,0.03);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <div style="font-size: 1.25rem; font-weight: 800; display: flex; align-items: center; gap: 2.5rem;">
                                <i class="fa-solid fa-chevron-left" style="font-size: 14px; cursor: pointer; opacity: 0.3;" onclick="cambiarMes(-1)"></i>
                                <span id="mesAnioLabel">Cargando...</span>
                                <i class="fa-solid fa-chevron-right" style="font-size: 14px; cursor: pointer;" onclick="cambiarMes(1)"></i>
                            </div>
                        </div>

                        <div class="calendar-days-grid" id="calendarioGrid">
                            <span class="cal-day-label">Lun</span>
                            <span class="cal-day-label">Mar</span>
                            <span class="cal-day-label">Mié</span>
                            <span class="cal-day-label">Jue</span>
                            <span class="cal-day-label">Vie</span>
                            <span class="cal-day-label">Sáb</span>
                            <span class="cal-day-label">Dom</span>
                            <!-- Días inyectados por JS -->
                        </div>

                        <div class="cal-legend">
                            <span><span class="legend-dot" style="background: white; border: 1px solid #eee;"></span> Disponible</span>
                            <span><span class="legend-dot" style="background: #eff6ff;"></span> Reservado</span>
                            <span><span class="legend-dot" style="background: #f1f5f9;"></span> Bloqueado</span>
                        </div>
                    </section>

                    <!-- Column 2: Dashboard/Details -->
                    <aside>
                        <div class="cal-detail-card" style="box-shadow: 0 4px 20px rgba(0,0,0,0.02); border: 1px solid #f1f5f9;" id="detallePanel">
                            <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                                <h3 style="font-size: 1.1rem; font-weight: 800;">Detalles del Día</h3>
                                <span id="detalleFechaLabel" style="font-size: 10px; font-weight: 800; background: #eff6ff; color: var(--primary); padding: 4px 10px; border-radius: 8px; text-transform: uppercase;">Selecciona un día</span>
                            </header>

                            <div id="detalleEstadoContenedor" style="background: #fcfcfc; border: 1px solid #f1f1f1; padding: 1.25rem; border-radius: 16px; margin-bottom: 1.5rem;">
                                <span style="display: block; font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 10px;">Estado</span>
                                <div id="detalleEstado" style="display: flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 700; color: #065f46;">
                                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span> Selecciona un día
                                </div>
                            </div>

                            <div style="background: #eff6ff; padding: 1.5rem; border-radius: 16px; margin-bottom: 2rem; position: relative;">
                                <span style="display: block; font-size: 11px; font-weight: 800; color: var(--primary); text-transform: uppercase; margin-bottom: 10px;">Tarifa por noche</span>
                                <div id="detalleTarifa" style="font-size: 1.5rem; font-weight: 800; color: #000;">$—</div>
                                <i class="fa-solid fa-pencil" style="position: absolute; right: 1.5rem; top: 50%; transform: translateY(-50%); color: var(--primary); opacity: 0.5; cursor: pointer;"></i>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <button style="background: var(--primary); color: white; border: none; padding: 1rem; border-radius: 12px; font-weight: 800; font-size: 13px; cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                                    <i class="fa-regular fa-money-bill-1" style="font-size: 1.25rem;"></i> Ajustar tarifa
                                </button>
                                <button onclick="abrirModalBloqueo()" style="background: #f1f5f9; color: #64748b; border: none; padding: 1rem; border-radius: 12px; font-weight: 800; font-size: 13px; cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                                    <i class="fa-solid fa-calendar-xmark" style="font-size: 1.25rem;"></i> Bloquear fechas
                                </button>
                            </div>
                            
                            <div id="detalleDesbloquearBtn" style="display: none; margin-top: 1rem;">
                                <button onclick="desbloquearFechaActual()" style="width: 100%; background: #e11d48; color: white; border: none; padding: 1rem; border-radius: 12px; font-weight: 800; font-size: 13px; cursor: pointer;">
                                    <i class="fa-solid fa-unlock"></i> Eliminar Bloqueo
                                </button>
                            </div>

                            <hr style="border: none; border-top: 1px solid #f1f5f9; margin: 2rem 0;">

                            <h4 style="font-size: 12px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 1.5rem;">Eventos del Mes</h4>
                            <div id="listaEventosMes">
                                <p style="font-size: 13px; color: #94a3b8; text-align: center;">No hay eventos este mes</p>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal para Bloquear Fechas -->
    <div class="modal-bloqueo" id="modalBloqueo">
        <div class="modal-bloqueo-content">
            <h3>Bloquear Fechas</h3>
            <form id="formBloqueo" onsubmit="guardarBloqueo(event)">
                <div class="form-group-modal">
                    <label>Fecha Inicio</label>
                    <input type="date" id="blqInicio" class="modal-input" required>
                </div>
                <div class="form-group-modal">
                    <label>Fecha Fin</label>
                    <input type="date" id="blqFin" class="modal-input" required>
                </div>
                <div class="form-group-modal">
                    <label>Motivo (opcional)</label>
                    <input type="text" id="blqMotivo" class="modal-input" placeholder="Ej. Mantenimiento, Uso personal...">
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-cancelar" onclick="cerrarModalBloqueo()">Cancelar</button>
                    <button type="submit" class="btn-bloquear">Bloquear</button>
                </div>
            </form>
        </div>
    </div>

<script>
let fechaActual = new Date();
let mesActual = fechaActual.getMonth() + 1;
let anioActual = fechaActual.getFullYear();
let eventosMes = [];
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

async function cargarCalendario() {
    const idPropiedad = document.getElementById('propiedadSelect').value;
    if (!idPropiedad) return;

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
    for (let d = 1; d <= ultimoDiaMes.getDate(); d++) {
        const fechaStr = `${anioActual}-${String(mesActual).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
        
        let clase = '';
        let info = 'Disponible';
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
        let txtEstado = rangoSeleccionado.fin ? 'Rango seleccionado' : 'Disponible para reserva';
        estadoDiv.innerHTML = `<span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span> ${txtEstado}`;
        estadoDiv.style.color = '#065f46';
        desbloquearBtn.style.display = 'none';
        detalleTarifa.innerText = '—';
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
</script>
</body>
</html>
