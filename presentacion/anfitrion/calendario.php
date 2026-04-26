<?php
require_once '../../negocio/auth/verificar_sesion.php';
validarSesion('anfitrion', '../../');
require_once '../../datos/conexion.php';
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
    <link rel="stylesheet" href="../../recursos/css/anfitrion/calendario.css">
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

<script src="../../recursos/js/anfitrion/calendario.js"></script>
</body>
</html>
