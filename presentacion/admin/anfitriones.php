<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Anfitriones | Estancias Admin</title>
    <link rel="stylesheet" href="../../recursos/css/variables.css">
    <link rel="stylesheet" href="../../recursos/css/host/main.css">
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
                    <h2 style="color: #1e40af; font-size: 1.3rem; display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-shield-halved"></i>
                        Estancias Digitales
                    </h2>
                    <p>Panel Administrativo</p>
                </div>
                
                <nav class="side-nav-host">
                    <li class="side-nav-item" onclick="window.location.href='dashboard.php'"><i class="fa-solid fa-house"></i> Inicio</li>
                    <li class="side-nav-item" onclick="window.location.href='propiedades.php'"><i class="fa-solid fa-building"></i> Propiedades</li>
                    <li class="side-nav-item" onclick="window.location.href='huespedes.php'"><i class="fa-solid fa-users"></i> Huéspedes</li>
                    <li class="side-nav-item active" onclick="window.location.href='anfitriones.php'"><i class="fa-solid fa-key"></i> Anfitriones</li>
                </nav>
            </div>

            <div style="margin-top: auto; padding-top: 2rem; border-top: 1px solid #f1f5f9; list-style: none;">
                <li class="side-nav-item" style="color: #ef4444;" onclick="window.location.href='../../index.php'"><i class="fa-solid fa-power-off"></i> Cerrar Sesión</li>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="host-content-main">
            <?php include '../../recursos/navbar.php'; ?>
            
            <div style="padding: 3rem 4rem;">
                <header style="margin-bottom: 2.5rem;">
                    <h1 style="font-size: 2.5rem; font-weight: 800; letter-spacing: -1.5px; color: #0f172a; margin-bottom: 0.5rem;">Gestión de Anfitriones</h1>
                    <p style="color: #64748b; font-weight: 500; font-size: 1.1rem;">Clasifica y audita la red de propietarios y su rendimiento en la plataforma.</p>
                </header>

                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem;">

                    <div style="display: flex; gap: 1rem;">
                        <button style="background: white; border: 1px solid #e2e8f0; padding: 0 1.25rem; height: 44px; border-radius: 12px; font-weight: 700; font-size: 13px; color: #475569; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-ranking-star" style="font-size: 14px;"></i> Por Nivel
                        </button>
                        <button style="background: #1e293b; border: none; padding: 0 1.25rem; height: 44px; border-radius: 12px; font-weight: 700; font-size: 13px; color: white; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-plus" style="font-size: 14px;"></i> Nuevo Registro
                        </button>
                    </div>
                </div>

                <!-- Hosts Table -->
                <section style="background: white; border-radius: 24px; border: 1px solid #f1f5f9; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.02); margin-bottom: 2.5rem;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 1px solid #f1f5f9;">
                                <th style="padding: 1.5rem; text-align: left; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Anfitrión</th>
                                <th style="padding: 1.5rem; text-align: left; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Nivel</th>
                                <th style="padding: 1.5rem; text-align: center; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Propiedades</th>
                                <th style="padding: 1.5rem; text-align: center; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Calificación</th>
                                <th style="padding: 1.5rem; text-align: right; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Ganancias Totales</th>
                                <th style="padding: 1.5rem; text-align: right; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom: 1px solid #f8fafc;">
                                <td style="padding: 1.5rem;">
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <img src="https://i.pravatar.cc/100?u=javier" style="width: 44px; height: 44px; border-radius: 12px; object-fit: cover;">
                                        <div>
                                            <div style="font-size: 14px; font-weight: 800;">Javier Jiménez</div>
                                            <div class="user-id-sub">ID: HOST-1029</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1.5rem;">
                                    <span class="status-pill pill-premium" style="background: #fff7ed; color: #9a3412;"><i class="fa-solid fa-certificate"></i> SUPERHOST</span>
                                </td>
                                <td style="padding: 1.5rem; text-align: center;">
                                    <span class="reservas-pill" style="background: #f1f5f9; color: #1e293b; width: auto; padding: 0 10px; border-radius: 8px;">24 Casas</span>
                                </td>
                                <td style="padding: 1.5rem; text-align: center;">
                                    <div style="font-size: 14px; font-weight: 800; color: #f59e0b;"><i class="fa-solid fa-star"></i> 4.98</div>
                                </td>
                                <td style="padding: 1.5rem; text-align: right; font-size: 15px; font-weight: 800;">€84,250</td>
                                <td style="padding: 1.5rem; text-align: right;">
                                    <span class="status-pill pill-activo">● Verificado</span>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f8fafc;">
                                <td style="padding: 1.5rem;">
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <img src="https://i.pravatar.cc/100?u=marta" style="width: 44px; height: 44px; border-radius: 12px; object-fit: cover;">
                                        <div>
                                            <div style="font-size: 14px; font-weight: 800;">Marta Domínguez</div>
                                            <div class="user-id-sub">ID: HOST-2287</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1.5rem;">
                                    <span class="status-pill pill-neutral">ANFITRIÓN PRO</span>
                                </td>
                                <td style="padding: 1.5rem; text-align: center;">
                                    <span class="reservas-pill" style="background: #f1f5f9; color: #1e293b; width: auto; padding: 0 10px; border-radius: 8px;">18 Casas</span>
                                </td>
                                <td style="padding: 1.5rem; text-align: center;">
                                    <div style="font-size: 14px; font-weight: 800; color: #f59e0b;"><i class="fa-solid fa-star"></i> 4.92</div>
                                </td>
                                <td style="padding: 1.5rem; text-align: right; font-size: 15px; font-weight: 800;">€52,900</td>
                                <td style="padding: 1.5rem; text-align: right;">
                                    <span class="status-pill pill-activo">● Verificado</span>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f8fafc;">
                                <td style="padding: 1.5rem;">
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <img src="https://i.pravatar.cc/100?u=rodrigo" style="width: 44px; height: 44px; border-radius: 12px; object-fit: cover;">
                                        <div>
                                            <div style="font-size: 14px; font-weight: 800;">Rodrigo Mendoza</div>
                                            <div class="user-id-sub">ID: HOST-0551</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1.5rem;">
                                    <span class="status-pill pill-neutral" style="background: #f0fdf4; color: #166534;">NUEVO</span>
                                </td>
                                <td style="padding: 1.5rem; text-align: center;">
                                    <span class="reservas-pill" style="background: #f1f5f9; color: #1e293b; width: auto; padding: 0 10px; border-radius: 8px;">2 Casas</span>
                                </td>
                                <td style="padding: 1.5rem; text-align: center;">
                                    <div style="font-size: 14px; font-weight: 800; color: #f59e0b;"><i class="fa-solid fa-star"></i> 4.70</div>
                                </td>
                                <td style="padding: 1.5rem; text-align: right; font-size: 15px; font-weight: 800;">€4,120</td>
                                <td style="padding: 1.5rem; text-align: right;">
                                    <span class="status-pill pill-pending">● En Revisión</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </section>

                <!-- Footer Stats for Hosts -->
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem;">
                    <div class="kpi-card" style="padding: 2rem;">
                        <div style="background: #fff7ed; color: #f97316; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 2rem;"><i class="fa-solid fa-award"></i></div>
                        <span style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Superhosts Activos</span>
                        <div style="font-size: 2.25rem; font-weight: 800; color: #1e293b; margin: 0.5rem 0;">42</div>
                        <div style="font-size: 11px; font-weight: 700; color: #ea580c;">Top 15% de la red</div>
                    </div>

                    <div class="kpi-card" style="padding: 2rem;">
                        <div style="background: #fdf2f8; color: #db2777; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 2rem;"><i class="fa-solid fa-star-half-stroke"></i></div>
                        <span style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Satisfacción Promedio</span>
                        <div style="font-size: 2.25rem; font-weight: 800; color: #1e293b; margin: 0.5rem 0;">4.85</div>
                        <div style="font-size: 11px; color: #db2777; font-weight: 700;">Basado en 2,400 reseñas</div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
