<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control | Estancias Admin</title>
    <link rel="stylesheet" href="../../recursos/css/variables.css">
    <link rel="stylesheet" href="../../recursos/css/layouts/shared.css">
    <link rel="stylesheet" href="../../recursos/css/components/navbar.css">
    <link rel="stylesheet" href="../../recursos/css/admin/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="admin-body">
    <div class="host-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar-host">
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div class="host-logo-box">
                    <h2 style="font-size: 1.3rem; display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-shield-halved"></i>
                        Estancias Digitales
                    </h2>
                    <p>Panel Administrativo</p>
                </div>
                
                <nav class="side-nav-host">
                    <li class="side-nav-item active" onclick="window.location.href='dashboard.php'"><i class="fa-solid fa-house"></i> Inicio</li>
                    <li class="side-nav-item" onclick="window.location.href='propiedades.php'"><i class="fa-solid fa-building"></i> Propiedades</li>
                    <li class="side-nav-item" onclick="window.location.href='huespedes.php'"><i class="fa-solid fa-users"></i> Huéspedes</li>
                    <li class="side-nav-item" onclick="window.location.href='anfitriones.php'"><i class="fa-solid fa-key"></i> Anfitriones</li>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="host-content-main">
            <?php include '../../recursos/navbar.php'; ?>
            
            <div style="padding: 3rem 4rem;">
                <header style="margin-bottom: 2.5rem;">
                <h1 style="font-size: 2.5rem; font-weight: 800; letter-spacing: -1.5px; color: #0f172a; margin-bottom: 0.5rem;">Bienvenido de nuevo, Admin</h1>
                <p style="color: #64748b; font-weight: 500; font-size: 1.1rem;">Aquí tienes el resumen del rendimiento de hoy.</p>
            </header>

            <!-- KPIs -->
            <section class="kpi-grid">
                <div class="kpi-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <div style="background: #eff6ff; color: var(--primary); width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 14px;"><i class="fa-solid fa-chart-simple"></i></div>
                        <span class="trend-badge trend-up"><i class="fa-solid fa-arrow-trend-up"></i> +12.5%</span>
                    </div>
                    <span class="kpi-label">Total Reservas</span>
                    <div class="kpi-value">1,284</div>
                    <div class="kpi-footer">Promedio mensual: <strong>142</strong></div>
                </div>


                <div class="kpi-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <div style="background: #f1f5f9; color: #475569; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 14px;"><i class="fa-solid fa-house"></i></div>
                        <span class="trend-badge trend-stable">− Estable</span>
                    </div>
                    <span class="kpi-label">Propiedades Activas</span>
                    <div class="kpi-value">156</div>
                    <div class="kpi-footer"><strong>9 nuevas</strong> este mes</div>
                </div>
            </section>

            <div class="admin-dashboard-layout">
                <!-- Left: Recent Reservations -->
                <section>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                        <h2 style="font-size: 1.5rem; font-weight: 800; letter-spacing: -0.5px;">Reservas Recientes</h2>
                        <a href="#" style="font-size: 13px; font-weight: 700; color: var(--primary); text-decoration: none;">Ver todas <i class="fa-solid fa-arrow-right" style="font-size: 10px; margin-left: 4px;"></i></a>
                    </div>

                    <div style="background: white; border-radius: 20px; border: 1px solid #f1f5f9; overflow: hidden;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f8fafc; border-bottom: 1px solid #f1f5f9;">
                                    <th style="padding: 1.25rem 1.5rem; text-align: left; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Propiedad</th>
                                    <th style="padding: 1.25rem 1.5rem; text-align: left; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Cliente</th>
                                    <th style="padding: 1.25rem 1.5rem; text-align: left; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Fecha</th>
                                    <th style="padding: 1.25rem 1.5rem; text-align: center; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Estado</th>
                                    <th style="padding: 1.25rem 1.5rem; text-align: right; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="border-bottom: 1px solid #f8fafc;">
                                    <td style="padding: 1.5rem;">
                                        <div style="display: flex; align-items: center; gap: 1rem;">
                                            <img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=60&q=80" style="width: 48px; height: 48px; border-radius: 10px; object-fit: cover;">
                                            <div>
                                                <div style="font-size: 14px; font-weight: 800;">Villa Maravilla</div>
                                                <div style="font-size: 11px; color: #94a3b8;">Ibiza, España</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding: 1.5rem;">
                                        <div style="font-size: 14px; font-weight: 700;">Marco Polo</div>
                                        <div style="font-size: 11px; color: #94a3b8; font-style: italic;">Verified Guest</div>
                                    </td>
                                    <td style="padding: 1.5rem;">
                                        <div style="font-size: 13px; font-weight: 700;">12 - 18 Oct</div>
                                        <div style="font-size: 11px; color: #94a3b8;">6 noches</div>
                                    </td>
                                    <td style="padding: 1.5rem; text-align: center;">
                                        <span class="status-pill pill-completed">Completado</span>
                                    </td>
                                    <td style="padding: 1.5rem; text-align: right; font-size: 15px; font-weight: 800;">€2,450</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #f8fafc;">
                                    <td style="padding: 1.5rem;">
                                        <div style="display: flex; align-items: center; gap: 1rem;">
                                            <img src="https://images.unsplash.com/photo-1554995207-c18c203602cb?auto=format&fit=crop&w=60&q=80" style="width: 48px; height: 48px; border-radius: 10px; object-fit: cover;">
                                            <div>
                                                <div style="font-size: 14px; font-weight: 800;">Urban Loft Central</div>
                                                <div style="font-size: 11px; color: #94a3b8;">Madrid, España</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding: 1.5rem;">
                                        <div style="font-size: 14px; font-weight: 700;">Elena Sorolla</div>
                                        <div style="font-size: 11px; color: #94a3b8; font-style: italic;">Premium Member</div>
                                    </td>
                                    <td style="padding: 1.5rem;">
                                        <div style="font-size: 13px; font-weight: 700;">24 - 26 Oct</div>
                                        <div style="font-size: 11px; color: #94a3b8;">2 noches</div>
                                    </td>
                                    <td style="padding: 1.5rem; text-align: center;">
                                        <span class="status-pill pill-confirmed" style="background: #f0f4ff; color: var(--primary);">Confirmado</span>
                                    </td>
                                    <td style="padding: 1.5rem; text-align: right; font-size: 15px; font-weight: 800;">€890</td>
                                </tr>
                                <tr>
                                    <td style="padding: 1.5rem;">
                                        <div style="display: flex; align-items: center; gap: 1rem;">
                                            <img src="https://images.unsplash.com/photo-1544984243-ec57ea16fe25?auto=format&fit=crop&w=60&q=80" style="width: 48px; height: 48px; border-radius: 10px; object-fit: cover;">
                                            <div>
                                                <div style="font-size: 14px; font-weight: 800;">Rustic Alpine Retreat</div>
                                                <div style="font-size: 11px; color: #94a3b8;">Pirineos, España</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding: 1.5rem;">
                                        <div style="font-size: 14px; font-weight: 700;">Carlos Ruiz</div>
                                        <div style="font-size: 11px; color: #94a3b8;">New Customer</div>
                                    </td>
                                    <td style="padding: 1.5rem;">
                                        <div style="font-size: 13px; font-weight: 700;">01 - 05 Nov</div>
                                        <div style="font-size: 11px; color: #94a3b8;">4 noches</div>
                                    </td>
                                    <td style="padding: 1.5rem; text-align: center;">
                                        <span class="status-pill pill-pending">Pendiente</span>
                                    </td>
                                    <td style="padding: 1.5rem; text-align: right; font-size: 15px; font-weight: 800;">€1,200</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Right: Featured and Best Hosts -->
                <aside>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                        <h2 style="font-size: 1.5rem; font-weight: 800; letter-spacing: -0.5px;">Propiedad Destacada</h2>
                    </div>

                    <div class="featured-card">
                        <div class="featured-img-box">
                            <span class="featured-badge">MÁS RESERVADA</span>
                            <img src="https://images.unsplash.com/photo-1613490493576-7fde63acd811?auto=format&fit=crop&w=400&q=80">
                        </div>
                        <div class="featured-info">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <h3 style="font-size: 1.25rem; font-weight: 800;">Palacio del Sol</h3>
                                <div style="font-size: 14px; font-weight: 800; color: var(--primary);">€550<span style="font-size: 11px; color: #94a3b8; font-weight: 600;">/noche</span></div>
                            </div>
                            <p style="font-size: 12px; line-height: 1.6; color: #64748b; margin-top: 0.75rem;">Málaga, Costa del Sol. 5 habitaciones, piscina privada, acceso exclusivo a la playa.</p>
                            <div class="tag-row">
                                <span class="mini-tag">WiFi 6</span>
                                <span class="mini-tag">Gimnasio</span>
                                <span class="mini-tag">Concierge</span>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 3rem; display: flex; justify-content: space-between; align-items: center;">
                        <h2 style="font-size: 1.5rem; font-weight: 800; letter-spacing: -0.5px;">Mejores Anfitriones</h2>
                    </div>

                    <div class="best-hosts-list">
                        <div class="host-rank-card top">
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <img src="https://i.pravatar.cc/100?u=javier" style="width: 40px; height: 40px; border-radius: 12px; object-fit: cover;">
                                <div>
                                    <div style="font-size: 13px; font-weight: 800;">Javier Jiménez</div>

                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 13px; font-weight: 800;">24</div>
                                <div style="font-size: 10px; color: #94a3b8; font-weight: 700;">Propiedades</div>
                            </div>
                        </div>

                        <div class="host-rank-card">
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <img src="https://i.pravatar.cc/100?u=marta" style="width: 40px; height: 40px; border-radius: 12px; object-fit: cover;">
                                <div>
                                    <div style="font-size: 13px; font-weight: 800;">Marta Domínguez</div>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 13px; font-weight: 800;">18</div>
                                <div style="font-size: 10px; color: #94a3b8; font-weight: 700;">Propiedades</div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </main>
    </div>
</body>
</html>
