<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calendario de Disponibilidad | Modo Anfitrión</title>
    <link rel="stylesheet" href="../../recursos/css/variables.css">
    <link rel="stylesheet" href="../../recursos/css/layouts/shared.css">
    <link rel="stylesheet" href="../../recursos/css/components/navbar.css">
    <link rel="stylesheet" href="../../recursos/css/host/main.css">
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
                    <li class="side-nav-item" onclick="window.location.href='propiedades.php'"><i class="fa-solid fa-building"></i> Propiedades</li>
                    <li class="side-nav-item active" onclick="window.location.href='calendario.php'"><i class="fa-solid fa-calendar-days"></i> Calendario</li>
                    <li class="side-nav-item" onclick="window.location.href='reservas.php'"><i class="fa-solid fa-receipt"></i> Reservas</li>
                </nav>
            </div>

            <div style="margin-top: auto; padding-top: 2rem; border-top: 1px solid #f1f5f9; list-style: none;">
                <li class="side-nav-item" style="color: #ef4444;" onclick="window.location.href='../../index.php'"><i class="fa-solid fa-power-off"></i> Cerrar Sesión</li>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="host-content-main">
            <?php include '../../recursos/navbar.php'; ?>
            
            <div style="padding: 2.5rem 4rem; max-width: 1600px; margin: 0 auto;">
                <header style="margin-bottom: 3rem;">
                    <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -1.5px; margin-bottom: 1rem;">Calendario de Disponibilidad</h1>
                    <div style="display: flex; gap: 1rem;">
                        <div class="filter-dropdown" style="background: #f1f5f9; padding: 0.6rem 1.25rem; border-radius: 99px; font-size: 13px; font-weight: 700; color: #1e40af; display: flex; align-items: center; gap: 1rem;">
                            Villa Mediterránea - Costa del Sol <i class="fa-solid fa-chevron-down" style="font-size: 10px;"></i>
                        </div>
                        <div class="filter-dropdown" style="background: #f1f5f9; padding: 0.6rem 1.25rem; border-radius: 99px; font-size: 13px; font-weight: 700; color: #475569; display: flex; align-items: center; gap: 1rem;">
                            <i class="fa-solid fa-sliders" style="font-size: 14px;"></i> Filtros
                        </div>
                    </div>
                </header>

                <div class="calendar-layout-grid">
                    <!-- Column 1: Calendar Grid -->
                    <section class="calendar-main-box" style="box-shadow: 0 10px 40px rgba(0,0,0,0.03);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <div style="font-size: 1.25rem; font-weight: 800; display: flex; align-items: center; gap: 2.5rem;">
                                <i class="fa-solid fa-chevron-left" style="font-size: 14px; cursor: pointer; opacity: 0.3;"></i>
                                Septiembre 2024
                                <i class="fa-solid fa-chevron-right" style="font-size: 14px; cursor: pointer;"></i>
                            </div>
                        </div>

                        <div class="calendar-days-grid">
                            <span class="cal-day-label">Lun</span>
                            <span class="cal-day-label">Mar</span>
                            <span class="cal-day-label">Mié</span>
                            <span class="cal-day-label">Jue</span>
                            <span class="cal-day-label">Vie</span>
                            <span class="cal-day-label">Sáb</span>
                            <span class="cal-day-label">Dom</span>

                            <!-- Day Boxes -->
                            <div class="cal-day-box not-current"><span>26</span></div>
                            <div class="cal-day-box not-current"><span>27</span></div>
                            <div class="cal-day-box not-current"><span>28</span></div>
                            <div class="cal-day-box not-current"><span>29</span></div>
                            <div class="cal-day-box not-current"><span>30</span></div>
                            <div class="cal-day-box not-current"><span>31</span></div>
                            <div class="cal-day-box"><span>1</span><span class="price">€120</span></div>

                            <div class="cal-day-box"><span>2</span><span class="price">€120</span></div>
                            <div class="cal-day-box reserved" style="border-radius: 12px 0 0 12px;"><span>3</span><span class="price" style="color: #94a3b8; font-size: 9px;">Reserva</span></div>
                            <div class="cal-day-box reserved"><span>4</span><span class="price" style="color: #94a3b8; font-size: 9px;">Alex T.</span></div>
                            <div class="cal-day-box reserved" style="border-radius: 0 12px 12px 0;"><span>5</span><span class="price" style="color: #94a3b8; font-size: 9px;">Alex T.</span></div>
                            <div class="cal-day-box"><span>6</span><span class="price">€145</span></div>
                            <div class="cal-day-box"><span>7</span><span class="price">€145</span></div>
                            <div class="cal-day-box blocked"><span>8</span><span class="price">Bloqueado</span></div>

                            <div class="cal-day-box selected"><span>9</span><span class="price">€120</span></div>
                            <div class="cal-day-box"><span>10</span><span class="price">€120</span></div>
                            <div class="cal-day-box"><span>11</span><span class="price">€120</span></div>
                            <div class="cal-day-box"><span>12</span><span class="price">€120</span></div>
                            <div class="cal-day-box"><span>13</span><span class="price">€120</span></div>
                            <div class="cal-day-box"><span>14</span><span class="price">€120</span></div>
                            <div class="cal-day-box"><span>15</span><span class="price">€120</span></div>

                            <div class="cal-day-box"><span>16</span><span class="price">€120</span></div>
                            <div class="cal-day-box"><span>17</span><span class="price">€120</span></div>
                            <div class="cal-day-box"><span>18</span><span class="price">€120</span></div>
                            <div class="cal-day-box"><span>19</span><span class="price">€120</span></div>
                            <div class="cal-day-box"><span>20</span><span class="price">€120</span></div>
                            <div class="cal-day-box"><span>21</span><span class="price">€150</span></div>
                            <div class="cal-day-box"><span>22</span><span class="price">€150</span></div>
                        </div>

                        <div class="cal-legend">
                            <span><span class="legend-dot" style="background: white; border: 1px solid #eee;"></span> Disponible</span>
                            <span><span class="legend-dot" style="background: #eff6ff;"></span> Reservado</span>
                            <span><span class="legend-dot" style="background: #f1f5f9;"></span> Bloqueado</span>
                        </div>
                    </section>

                    <!-- Column 2: Dashboard/Details -->
                    <aside>
                        <div class="cal-detail-card" style="box-shadow: 0 4px 20px rgba(0,0,0,0.02); border: 1px solid #f1f5f9;">
                            <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                                <h3 style="font-size: 1.1rem; font-weight: 800;">Detalles del Día</h3>
                                <span style="font-size: 10px; font-weight: 800; background: #eff6ff; color: #1e40af; padding: 4px 10px; border-radius: 8px; text-transform: uppercase;">9 Sep 2024</span>
                            </header>

                            <div style="background: #fcfcfc; border: 1px solid #f1f1f1; padding: 1.25rem; border-radius: 16px; margin-bottom: 1.5rem;">
                                <span style="display: block; font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 10px;">Estado</span>
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 700; color: #065f46;">
                                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span> Disponible para reserva
                                </div>
                            </div>

                            <div style="background: #eff6ff; padding: 1.5rem; border-radius: 16px; margin-bottom: 2rem; position: relative;">
                                <span style="display: block; font-size: 11px; font-weight: 800; color: #1e40af; text-transform: uppercase; margin-bottom: 10px;">Tarifa por noche</span>
                                <div style="font-size: 1.5rem; font-weight: 800; color: #000;">€120.00</div>
                                <i class="fa-solid fa-pencil" style="position: absolute; right: 1.5rem; top: 50%; transform: translateY(-50%); color: #1e40af; opacity: 0.5; cursor: pointer;"></i>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <button style="background: #1e40af; color: white; border: none; padding: 1rem; border-radius: 12px; font-weight: 800; font-size: 13px; cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                                    <i class="fa-regular fa-money-bill-1" style="font-size: 1.25rem;"></i> Ajustar tarifa
                                </button>
                                <button style="background: #f1f5f9; color: #64748b; border: none; padding: 1rem; border-radius: 12px; font-weight: 800; font-size: 13px; cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                                    <i class="fa-solid fa-calendar-xmark" style="font-size: 1.25rem;"></i> Bloquear fechas
                                </button>
                            </div>

                            <hr style="border: none; border-top: 1px solid #f1f5f9; margin: 2rem 0;">

                            <h4 style="font-size: 12px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 1.5rem;">Próximos Eventos</h4>
                            <div class="event-item" style="margin-bottom: 0.5rem;">
                                <div class="event-icon"><img src="https://i.pravatar.cc/100?u=ate" style="width: 24px; height: 24px; border-radius: 4px;"></div>
                                <div>
                                    <div style="font-size: 13px; font-weight: 700;">Reserva: Alex Thompson</div>
                                    <div style="font-size: 11px; color: #94a3b8;">3 sep - 5 sep (2 noches)</div>
                                </div>
                            </div>
                            <div class="event-item">
                                <div class="event-icon" style="background: #fff7ed; color: #f97316;"><i class="fa-solid fa-broom" style="font-size: 1.1rem;"></i></div>
                                <div>
                                    <div style="font-size: 13px; font-weight: 700;">Limpieza programada</div>
                                    <div style="font-size: 11px; color: #94a3b8;">5 sep • 11:00 AM</div>
                                </div>
                            </div>
                        </div>

                    </aside>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
