<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control | Modo Anfitrión</title>
    <link rel="stylesheet" href="../../recursos/css/variables.css">
    <link rel="stylesheet" href="../../recursos/css/layouts/shared.css">
    <link rel="stylesheet" href="../../recursos/css/components/navbar.css">
    <link rel="stylesheet" href="../../recursos/css/anfitrion/host_main.css">
    <link rel="stylesheet" href="../../recursos/css/admin/main.css">
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
                    <li class="side-nav-item active" onclick="window.location.href='dashboard.php'"><i class="fa-solid fa-house"></i> Inicio</li>
                    <li class="side-nav-item" onclick="window.location.href='propiedades.php'"><i class="fa-solid fa-building"></i> Propiedades</li>
                    <li class="side-nav-item" onclick="window.location.href='calendario.php'"><i class="fa-solid fa-calendar-days"></i> Calendario</li>
                    <li class="side-nav-item" onclick="window.location.href='reservas.php'"><i class="fa-solid fa-receipt"></i> Reservas</li>
                </nav>
            </div>

        </aside>

        <main class="host-content-main">
            <?php include '../../recursos/navbar.php'; ?>
            
            <div class="host-dashboard-container">
                <section class="main-stats-section">
                    <header style="margin-bottom: 2.5rem;">
                        <h1 style="font-size: 2.2rem; font-weight: 800; letter-spacing: -1px; color: var(--text-main);">Bienvenido, Carlos</h1>
                        <p style="color: #94a3b8; font-size: 14px; font-weight: 500;">Aquí tienes el rendimiento de tus propiedades para este mes.</p>
                    </header>

                    <!-- Side Mini KPIs -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem;">
                        <div class="mini-stat-card">
                            <div>
                                <span style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; display: block; margin-bottom: 5px;">Ocupación media</span>
                                <div style="font-size: 1.75rem; font-weight: 800;">84%</div>
                            </div>
                            <div class="mini-stat-icon" style="background: #ecfdf5; color: #10b981;">%</div>
                        </div>
                        <div class="mini-stat-card">
                            <div>
                                <span style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; display: block; margin-bottom: 5px;">Próximas llegadas</span>
                                <div style="font-size: 1.75rem; font-weight: 800;">12</div>
                            </div>
                            <div class="mini-stat-icon" style="background: #eff6ff; color: var(--primary);"><i class="fa-solid fa-plane-arrival"></i></div>
                        </div>
                    </div>

                    <!-- Recent Reservations -->
                    <div style="margin-top: 4rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                            <h3 style="font-size: 1.5rem; font-weight: 800; letter-spacing: -1px;">Reservas recientes</h3>
                            <a href="reservas.php" style="font-size: 13px; font-weight: 800; color: var(--primary); text-decoration: none;">Ver todas</a>
                        </div>

                        <div class="reservation-list-item">
                            <div style="display: flex; gap: 1.5rem; align-items: center;">
                                <img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=60&q=80" style="width: 52px; height: 52px; border-radius: 12px; object-fit: cover;">
                                <div>
                                    <div style="font-size: 15px; font-weight: 800;">Sofia Martínez</div>
                                    <div style="font-size: 12px; color: #94a3b8; margin-top: 2px;">Villa Marítima • 12 - 18 Oct</div>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <span class="status-tag tag-confirmed" style="font-size: 9px; padding: 4px 10px;">CONFIRMADA</span>
                                <div style="margin-top: 8px; font-size: 14px; font-weight: 800;">€1,240</div>
                            </div>
                        </div>

                        <div class="reservation-list-item">
                            <div style="display: flex; gap: 1.5rem; align-items: center;">
                                <img src="https://images.unsplash.com/photo-1544984243-ec57ea16fe25?auto=format&fit=crop&w=60&q=80" style="width: 52px; height: 52px; border-radius: 12px; object-fit: cover;">
                                <div>
                                    <div style="font-size: 15px; font-weight: 800;">Erik Johannsen</div>
                                    <div style="font-size: 12px; color: #94a3b8; margin-top: 2px;">Ático Skyview • 20 - 25 Oct</div>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <span class="status-tag tag-pending" style="font-size: 9px; padding: 4px 10px; background: #fef3c7; color: #92400e;">PENDIENTE</span>
                                <div style="margin-top: 8px; font-size: 14px; font-weight: 800;">€850</div>
                            </div>
                        </div>

                        <div class="reservation-list-item">
                            <div style="display: flex; gap: 1.5rem; align-items: center;">
                                <img src="https://images.unsplash.com/photo-1510798831971-661eb04b3739?auto=format&fit=crop&w=60&q=80" style="width: 52px; height: 52px; border-radius: 12px; object-fit: cover;">
                                <div>
                                    <div style="font-size: 15px; font-weight: 800;">Lucía Fernández</div>
                                    <div style="font-size: 12px; color: #94a3b8; margin-top: 2px;">Refugio Alpino • 02 - 05 Nov</div>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <span class="status-tag tag-confirmed" style="font-size: 9px; padding: 4px 10px;">CONFIRMADA</span>
                                <div style="margin-top: 8px; font-size: 14px; font-weight: 800;">€420</div>
                            </div>
                        </div>
                    </div>
                </section>

                <aside>
                    <!-- Quick Actions -->
                    <div class="quick-actions-box">
                        <h4 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 2rem; letter-spacing: -0.5px;">Acciones rápidas</h4>
                        <div class="action-grid">
                            <div class="action-btn-card">
                                <i class="fa-solid fa-house-medical"></i>
                                <span>Publicar Propiedad</span>
                            </div>
                            <div class="action-btn-card">
                                <i class="fa-solid fa-bullhorn"></i>
                                <span>Crear Oferta</span>
                            </div>
                            <div class="action-btn-card">
                                <i class="fa-solid fa-headset"></i>
                                <span>Soporte Anfitrión</span>
                            </div>
                            <div class="action-btn-card">
                                <i class="fa-solid fa-star-half-stroke"></i>
                                <span>Ver Reseñas</span>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Chart (Simulated) -->
                    <div style="margin-top: 4rem; background: white; border-radius: 2rem; padding: 2rem; border: 1px solid #f1f5f9;">
                         <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                            <h4 style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Rendimiento</h4>
                            <i class="fa-solid fa-ellipsis-vertical" style="opacity: 0.3;"></i>
                        </div>
                        <div style="height: 120px; display: flex; align-items: flex-end; gap: 10px; padding: 0 10px;">
                            <div style="height: 40%; width: 25px; background: #f1f5f9; border-radius: 6px;"></div>
                            <div style="height: 60%; width: 25px; background: #eff6ff; border-radius: 6px;"></div>
                            <div style="height: 90%; width: 25px; background: #2563eb; border-radius: 6px; position: relative;">
                                <div style="position: absolute; top: -35px; left: 50%; transform: translateX(-50%); background: #000; color: white; font-size: 10px; padding: 4px 8px; border-radius: 6px;">Hoy</div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    <!-- Floating Button -->
    <div class="floating-add-btn" onclick="window.location.href='nueva-propiedad.php'" title="Nueva Propiedad - Subir al sistema">
        <i class="fa-solid fa-plus"></i>
    </div>
</body>
</html>
