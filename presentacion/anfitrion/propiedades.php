<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Propiedades | Modo Anfitrión</title>
    <link rel="stylesheet" href="../../recursos/css/variables.css">
    <link rel="stylesheet" href="../../recursos/css/host/main.css">
    <link rel="stylesheet" href="../../recursos/css/admin/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="host-body">
    <div class="host-wrapper">
        <aside class="sidebar-host">
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div class="host-logo-box">
                    <h2 style="color: #1e40af; font-size: 1.3rem; display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-house-laptop"></i>
                        Estancias Digitales
                    </h2>
                    <p>Modo Anfitrión</p>
                </div>
                
                <nav class="side-nav-host">
                    <li class="side-nav-item" onclick="window.location.href='dashboard.php'"><i class="fa-solid fa-house"></i> Inicio</li>
                    <li class="side-nav-item active" onclick="window.location.href='propiedades.php'"><i class="fa-solid fa-building"></i> Propiedades</li>
                    <li class="side-nav-item" onclick="window.location.href='calendario.php'"><i class="fa-solid fa-calendar-days"></i> Calendario</li>
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
                <header style="display: flex; justify-content: space-between; align-items: flex-end;">
                    <div>
                        <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -1.5px; margin-bottom: 0.5rem;">Mis Propiedades</h1>
                        <p style="color: #64748b; font-size: 14px; max-width: 600px;">Gestiona tus estancias de lujo, actualiza disponibilidades y optimiza tus ingresos desde un solo lugar.</p>
                    </div>
                    <button class="btn btn-primary" onclick="window.location.href='nueva-propiedad.php'" style="padding: 1rem 2rem; font-weight: 800; font-size: 14px; border-radius: 12px; background: #0f172a; color: white; box-shadow: 0 8px 20px rgba(15, 23, 42, 0.2);"><i class="fa-solid fa-plus"></i> Nueva Propiedad</button>
                </header>

                <!-- KPI Grid -->
                <section class="kpi-host-grid" style="margin-top: 3rem;">
                    <div class="kpi-host-card">
                        <span class="label">Propiedades Activas</span>
                        <div class="value">12 <span style="font-size: 11px; background: #d1fae5; color: #065f46; padding: 4px 10px; border-radius: 8px; margin-left: 10px;">+2 este mes</span></div>
                    </div>
                    <div class="kpi-host-card">
                        <span class="label">Ocupación Media</span>
                        <div class="value">84% <span style="font-size: 11px; background: #f0f4ff; color: #1e40af; padding: 4px 10px; border-radius: 8px; margin-left: 10px;">Alto rendimiento</span></div>
                    </div>
                    <div class="kpi-host-card" style="grid-column: span 2;">
                        <span class="label">Ingresos Estimados</span>
                        <div class="value">€14.2k <span style="font-size: 12px; font-weight: 500; color: #94a3b8; margin-left: 10px;">Próximos 30 días</span></div>
                    </div>
                </section>

                <!-- Filters & View Toggle -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 3rem;">
                    <div style="display: flex; gap: 1rem;">
                        <div class="filter-dropdown" style="background: #f1f5f9; padding: 0.75rem 1.5rem; border-radius: 12px; font-size: 13px; font-weight: 700; color: #475569; display: flex; align-items: center; gap: 1rem;">
                            Tipo: <span style="color: #64748b; font-weight: 500;">Todos</span> <i class="fa-solid fa-chevron-down" style="font-size: 10px;"></i>
                        </div>
                        <div class="filter-dropdown" style="background: #f1f5f9; padding: 0.75rem 1.5rem; border-radius: 12px; font-size: 13px; font-weight: 700; color: #475569; display: flex; align-items: center; gap: 1rem;">
                            Estado: <span style="color: #64748b; font-weight: 500;">Todos</span> <i class="fa-solid fa-chevron-down" style="font-size: 10px;"></i>
                        </div>
                    </div>
                    <div style="display: flex; gap: 1rem; font-size: 1.1rem; color: #94a3b8;">
                        <i class="fa-solid fa-grip" style="color: #1e40af; cursor: pointer;"></i>
                        <i class="fa-solid fa-list" style="cursor: pointer;"></i>
                    </div>
                </div>

                <!-- Property Grid -->
                <section class="host-prop-grid">
                    <!-- Card 1: Activa -->
                    <div class="host-prop-card">
                        <div class="card-img-wrapper">
                            <img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=600&q=80">
                            <div class="card-badge-status status-activa">● ACTIVA</div>
                            <div class="btn-edit-float"><i class="fa-solid fa-pencil"></i></div>
                        </div>
                        <div class="host-card-content">
                            <div class="host-card-info-row">
                                <h3 class="host-card-title">Villa Mediterránea Luxe</h3>
                                <span class="host-card-price">€450<span style="font-size: 10px; font-weight: 400; color: #94a3b8;">/noche</span></span>
                            </div>
                            <p style="font-size: 13px; color: #64748b;"><i class="fa-solid fa-location-dot" style="margin-right: 5px; opacity: 0.5;"></i> Ibiza, Islas Baleares</p>
                            <div class="host-card-footer">
                                <span><i class="fa-solid fa-bed"></i> 4 Dorm.</span>
                                <span><i class="fa-solid fa-shower"></i> 3 Baños</span>
                                <span style="color: #1e293b;">9.8 <i class="fa-solid fa-star" style="color: #1e40af;"></i></span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Pausada -->
                    <div class="host-prop-card">
                        <div class="card-img-wrapper">
                            <img src="https://images.unsplash.com/photo-1554995207-c18c203602cb?auto=format&fit=crop&w=600&q=80">
                            <div class="card-badge-status status-pausada">● PAUSADA</div>
                            <div class="btn-edit-float"><i class="fa-solid fa-pencil"></i></div>
                        </div>
                        <div class="host-card-content">
                            <div class="host-card-info-row">
                                <h3 class="host-card-title">Loft Industrial Malasaña</h3>
                                <span class="host-card-price">€180<span style="font-size: 10px; font-weight: 400; color: #94a3b8;">/noche</span></span>
                            </div>
                            <p style="font-size: 13px; color: #64748b;"><i class="fa-solid fa-location-dot" style="margin-right: 5px; opacity: 0.5;"></i> Madrid, Centro</p>
                            <div class="host-card-footer">
                                <span><i class="fa-solid fa-bed"></i> 1 Dorm.</span>
                                <span><i class="fa-solid fa-shower"></i> 1 Baño</span>
                                <span style="color: #1e293b;">8.4 <i class="fa-solid fa-star" style="color: #1e40af;"></i></span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Mantenimiento -->
                    <div class="host-prop-card">
                        <div class="card-img-wrapper">
                            <img src="https://images.unsplash.com/photo-1544984243-ec57ea16fe25?auto=format&fit=crop&w=600&q=80">
                            <div class="card-badge-status status-mantenimiento">● MANTENIMIENTO</div>
                            <div class="btn-edit-float"><i class="fa-solid fa-pencil"></i></div>
                        </div>
                        <div class="host-card-content">
                            <div class="host-card-info-row">
                                <h3 class="host-card-title">Ático Skyline Diagonal</h3>
                                <span class="host-card-price">€320<span style="font-size: 10px; font-weight: 400; color: #94a3b8;">/noche</span></span>
                            </div>
                            <p style="font-size: 13px; color: #64748b;"><i class="fa-solid fa-location-dot" style="margin-right: 5px; opacity: 0.5;"></i> Barcelona, Diagonal Mar</p>
                            <div class="host-card-footer">
                                <span><i class="fa-solid fa-bed"></i> 2 Dorm.</span>
                                <span><i class="fa-solid fa-shower"></i> 2 Baños</span>
                                <span style="color: #1e293b;">9.2 <i class="fa-solid fa-star" style="color: #1e40af;"></i></span>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
