<?php
require_once '../../negocio/auth/verificar_sesion.php';
validarSesion('admin', '../../');
require_once '../../datos/conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Propiedades | Estancias Admin</title>
    <link rel="stylesheet" href="../../recursos/css/variables.css">
    <link rel="stylesheet" href="../../recursos/css/layouts/shared.css">
    <link rel="stylesheet" href="../../recursos/css/components/navbar.css">
    <link rel="stylesheet" href="../../recursos/css/admin/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="admin-body">
    <div class="host-wrapper">
        <!-- Sidebar (Mirroring the Dashboard design as requested) -->
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
                    <li class="side-nav-item" onclick="window.location.href='dashboard.php'"><i class="fa-solid fa-house"></i> Inicio</li>
                    <li class="side-nav-item active" onclick="window.location.href='propiedades.php'"><i class="fa-solid fa-building"></i> Propiedades</li>
                    <li class="side-nav-item" onclick="window.location.href='huespedes.php'"><i class="fa-solid fa-users"></i> Huéspedes</li>
                    <li class="side-nav-item" onclick="window.location.href='anfitriones.php'"><i class="fa-solid fa-key"></i> Anfitriones</li>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="host-content-main">
            <?php include '../../recursos/navbar.php'; ?>
            
            <div style="padding: 3rem 4rem;">
                <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem;">
                    <div>
                        <h1 style="font-size: 2.5rem; font-weight: 800; letter-spacing: -1.5px; color: #0f172a; margin-bottom: 0.5rem;">Gestión de Propiedades</h1>
                        <p style="color: #64748b; font-weight: 500; font-size: 1.1rem;">Administra el inventario global de alojamientos y sus estados.</p>
                    </div>
                    <div style="display: flex; background: #f1f5f9; padding: 4px; border-radius: 12px;">
                        <button class="page-btn active" style="width: auto; padding: 0 1rem; height: 36px; gap: 8px;"><i class="fa-solid fa-list"></i> Lista</button>
                        <button class="page-btn" style="width: auto; padding: 0 1rem; height: 36px; gap: 8px;"><i class="fa-solid fa-grip"></i> Mosaico</button>
                    </div>
                </header>

                <!-- Filter Bar -->
                <section class="filter-container">
                    <div class="filter-group">
                        <label>Ciudad / Ubicación</label>
                        <div class="filter-input-wrapper">
                            <i class="fa-solid fa-location-dot"></i>
                            <select>
                                <option>Todas las ciudades</option>
                                <option>Madrid</option>
                                <option>Barcelona</option>
                                <option>Ibiza</option>
                            </select>
                        </div>
                    </div>
                    <div class="filter-group" style="flex: 1.5;">
                        <label>Rango de Precio (Noche)</label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <div class="filter-input-wrapper">
                                <span style="font-size: 13px; font-weight: 700; color: #94a3b8;">€</span>
                                <input type="text" placeholder="Min">
                            </div>
                            <span style="color: #e2e8f0;">—</span>
                            <div class="filter-input-wrapper">
                                <span style="font-size: 13px; font-weight: 700; color: #94a3b8;">€</span>
                                <input type="text" placeholder="Máx">
                            </div>
                        </div>
                    </div>
                    <div class="filter-group">
                        <label>Categoría</label>
                        <div class="filter-input-wrapper">
                            <select>
                                <option>Cualquiera</option>
                                <option>Premium</option>
                                <option>Apartamento</option>
                                <option>Rural</option>
                            </select>
                        </div>
                    </div>
                    <button style="background: #1e293b; color: white; border: none; padding: 0 2rem; height: 48px; border-radius: 12px; font-weight: 800; font-size: 13px; cursor: pointer;">Aplicar Filtros</button>
                </section>

                <!-- Properties Table -->
                <section style="background: white; border-radius: 24px; border: 1px solid #f1f5f9; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.02); margin-bottom: 2rem;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 1px solid #f1f5f9;">
                                <th style="padding: 1.5rem; text-align: left; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Propiedad</th>
                                <th style="padding: 1.5rem; text-align: left; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Anfitrión</th>
                                <th style="padding: 1.5rem; text-align: left; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Categoría</th>
                                <th style="padding: 1.5rem; text-align: left; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Precio / Noche</th>
                                <th style="padding: 1.5rem; text-align: left; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Estado</th>
                                <th style="padding: 1.5rem; text-align: right; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom: 1px solid #f8fafc;">
                                <td style="padding: 1.5rem;">
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=60&q=80" style="width: 52px; height: 52px; border-radius: 12px; object-fit: cover;">
                                        <div>
                                            <div style="font-size: 14px; font-weight: 800;">Villa Serena con Vistas al Mar</div>
                                            <div style="font-size: 11px; color: #94a3b8;"><i class="fa-solid fa-location-dot"></i> Ibiza, Baleares</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1.5rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <img src="https://i.pravatar.cc/100?u=elena" style="width: 32px; height: 32px; border-radius: 50%;">
                                        <span style="font-size: 13px; font-weight: 700;">Elena Martínez</span>
                                    </div>
                                </td>
                                <td style="padding: 1.5rem;">
                                    <span class="status-pill pill-premium">PREMIUM</span>
                                </td>
                                <td style="padding: 1.5rem; font-size: 15px; font-weight: 800;">450€<span style="font-size: 10px; color: #94a3b8; font-weight: 600;">/noche</span></td>
                                <td style="padding: 1.5rem;">
                                    <span class="status-pill pill-disponible">● Disponible</span>
                                </td>
                                <td style="padding: 1.5rem; text-align: right;">
                                    <i class="fa-solid fa-ellipsis" style="color: #94a3b8; cursor: pointer;"></i>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f8fafc;">
                                <td style="padding: 1.5rem;">
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <img src="https://images.unsplash.com/photo-1554995207-c18c203602cb?auto=format&fit=crop&w=60&q=80" style="width: 52px; height: 52px; border-radius: 12px; object-fit: cover;">
                                        <div>
                                            <div style="font-size: 14px; font-weight: 800;">Ático Industrial en Eixample</div>
                                            <div style="font-size: 11px; color: #94a3b8;"><i class="fa-solid fa-location-dot"></i> Barcelona, Cataluña</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1.5rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <img src="https://i.pravatar.cc/100?u=marc" style="width: 32px; height: 32px; border-radius: 50%;">
                                        <span style="font-size: 13px; font-weight: 700;">Marc Soler</span>
                                    </div>
                                </td>
                                <td style="padding: 1.5rem;">
                                    <span class="status-pill pill-neutral">APARTAMENTO</span>
                                </td>
                                <td style="padding: 1.5rem; font-size: 15px; font-weight: 800;">185€<span style="font-size: 10px; color: #94a3b8; font-weight: 600;">/noche</span></td>
                                <td style="padding: 1.5rem;">
                                    <span class="status-pill pill-ocupada">● Ocupada</span>
                                </td>
                                <td style="padding: 1.5rem; text-align: right;">
                                    <i class="fa-solid fa-ellipsis" style="color: #94a3b8; cursor: pointer;"></i>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f8fafc;">
                                <td style="padding: 1.5rem;">
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <img src="https://images.unsplash.com/photo-1544984243-ec57ea16fe25?auto=format&fit=crop&w=60&q=80" style="width: 52px; height: 52px; border-radius: 12px; object-fit: cover;">
                                        <div>
                                            <div style="font-size: 14px; font-weight: 800;">Casa de Campo La Dehesa</div>
                                            <div style="font-size: 11px; color: #94a3b8;"><i class="fa-solid fa-location-dot"></i> Ronda, Andalucía</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1.5rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <img src="https://i.pravatar.cc/100?u=ana" style="width: 32px; height: 32px; border-radius: 50%;">
                                        <span style="font-size: 13px; font-weight: 700;">Ana Belén Ruiz</span>
                                    </div>
                                </td>
                                <td style="padding: 1.5rem;">
                                    <span class="status-pill pill-neutral" style="background: #fef3c7; color: #92400e;">RURAL</span>
                                </td>
                                <td style="padding: 1.5rem; font-size: 15px; font-weight: 800;">120€<span style="font-size: 10px; color: #94a3b8; font-weight: 600;">/noche</span></td>
                                <td style="padding: 1.5rem;">
                                    <span class="status-pill pill-disponible">● Disponible</span>
                                </td>
                                <td style="padding: 1.5rem; text-align: right;">
                                    <i class="fa-solid fa-ellipsis" style="color: #94a3b8; cursor: pointer;"></i>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div style="padding: 1.5rem 2rem; background: #fff; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 13px; color: #94a3b8; font-weight: 600;">Mostrando 1-10 de 128 propiedades</span>
                        <div class="pagination">
                            <div class="page-btn"><i class="fa-solid fa-chevron-left"></i></div>
                            <div class="page-btn active">1</div>
                            <div class="page-btn">2</div>
                            <div class="page-btn">3</div>
                            <span style="color: #e2e8f0;">...</span>
                            <div class="page-btn">13</div>
                            <div class="page-btn"><i class="fa-solid fa-chevron-right"></i></div>
                        </div>
                    </div>
                </section>

                <!-- Bottom Stats -->
                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
                    <div class="kpi-card" style="padding: 2rem;">
                        <div style="background: #ecfdf5; color: #065f46; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 2rem;"><i class="fa-solid fa-hotel"></i></div>
                        <span style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Total Propiedades</span>
                        <div style="font-size: 2.25rem; font-weight: 800; margin: 0.5rem 0; color: var(--text-main);">128</div>
                        <div style="font-size: 11px; color: #94a3b8; font-weight: 600;"><strong>12 nuevas</strong> este mes</div>
                    </div>

                    <div class="kpi-card" style="padding: 2rem;">
                        <h4 style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 1.5rem;">Distribución Geográfica</h4>
                        <div class="progress-stat">
                            <div class="progress-stat-header">
                                <span style="font-size: 13px; font-weight: 800;">Madrid</span>
                                <span style="font-size: 13px; font-weight: 700; color: #64748b;">42%</span>
                            </div>
                            <div class="progress-bar-bg"><div class="progress-bar-fill" style="width: 42%;"></div></div>
                        </div>
                        <div class="progress-stat">
                            <div class="progress-stat-header">
                                <span style="font-size: 13px; font-weight: 800;">Barcelona</span>
                                <span style="font-size: 13px; font-weight: 700; color: #64748b;">35%</span>
                            </div>
                            <div class="progress-bar-bg"><div class="progress-bar-fill" style="width: 35%; background: #6366f1;"></div></div>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem;">
                            <a href="#" style="font-size: 12px; font-weight: 800; color: var(--primary); text-decoration: none;">Ver reporte detallado</a>
                            <i class="fa-solid fa-arrow-right" style="color: var(--primary); font-size: 12px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
