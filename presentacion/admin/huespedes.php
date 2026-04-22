<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios | Estancias Admin</title>
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
                    <li class="side-nav-item" onclick="window.location.href='dashboard.php'"><i class="fa-solid fa-house"></i> Inicio</li>
                    <li class="side-nav-item" onclick="window.location.href='propiedades.php'"><i class="fa-solid fa-building"></i> Propiedades</li>
                    <li class="side-nav-item active" onclick="window.location.href='huespedes.php'"><i class="fa-solid fa-users"></i> Huéspedes</li>
                    <li class="side-nav-item" onclick="window.location.href='anfitriones.php'"><i class="fa-solid fa-key"></i> Anfitriones</li>
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
                    <h1 style="font-size: 2.5rem; font-weight: 800; letter-spacing: -1.5px; color: #0f172a; margin-bottom: 0.5rem;">Gestión de Usuarios</h1>
                    <p style="color: #64748b; font-weight: 500; font-size: 1.1rem;">Administra las cuentas de clientes y anfitriones registrados en la plataforma.</p>
                </header>

                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem;">
                    <div style="display: flex; gap: 1rem;">
                        <div style="display: flex; background: #f1f5f9; padding: 4px; border-radius: 12px; margin-right: 1.5rem;">
                            <button class="page-btn active" style="width: 36px;"><i class="fa-solid fa-list"></i></button>
                            <button class="page-btn" style="width: 36px;"><i class="fa-solid fa-grip"></i></button>
                        </div>
                        <button style="background: white; border: 1px solid #e2e8f0; padding: 0 1.25rem; height: 44px; border-radius: 12px; font-weight: 700; font-size: 13px; color: #475569; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-sliders" style="font-size: 14px;"></i> Filtrar
                        </button>
                    </div>
                </div>

                <!-- Users Table -->
                <section style="background: white; border-radius: 24px; border: 1px solid #f1f5f9; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.02); margin-bottom: 2.5rem;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 1px solid #f1f5f9;">
                                <th style="padding: 1.5rem; text-align: left; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Nombre</th>
                                <th style="padding: 1.5rem; text-align: left; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Correo Electrónico</th>
                                <th style="padding: 1.5rem; text-align: left; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Registro</th>
                                <th style="padding: 1.5rem; text-align: center; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Reservas</th>
                                <th style="padding: 1.5rem; text-align: left; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Estado</th>
                                <th style="padding: 1.5rem; text-align: right; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom: 1px solid #f8fafc;">
                                <td style="padding: 1.5rem;">
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <img src="https://i.pravatar.cc/100?u=lucia" style="width: 44px; height: 44px; border-radius: 12px; object-fit: cover;">
                                        <div>
                                            <div style="font-size: 14px; font-weight: 800;">Lucía Méndez</div>
                                            <div class="user-id-sub">ID: CLI-90210</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1.5rem; font-size: 13px; color: #475569; font-weight: 600;">lucia.mendez@example.com</td>
                                <td style="padding: 1.5rem; font-size: 13px; color: #64748b; font-weight: 600;">12 Oct 2023</td>
                                <td style="padding: 1.5rem; text-align: center;">
                                    <span class="reservas-pill">24</span>
                                </td>
                                <td style="padding: 1.5rem;">
                                    <span class="status-pill pill-activo">● ACTIVO</span>
                                </td>
                                <td style="padding: 1.5rem; text-align: right;">
                                    <i class="fa-solid fa-ellipsis" style="color: #94a3b8; cursor: pointer;"></i>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f8fafc;">
                                <td style="padding: 1.5rem;">
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <img src="https://i.pravatar.cc/100?u=carlos" style="width: 44px; height: 44px; border-radius: 12px; object-fit: cover;">
                                        <div>
                                            <div style="font-size: 14px; font-weight: 800;">Carlos Ruiz</div>
                                            <div class="user-id-sub">ID: CLI-44321</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1.5rem; font-size: 13px; color: #475569; font-weight: 600;">carlos.ruiz.dev@gmail.com</td>
                                <td style="padding: 1.5rem; font-size: 13px; color: #64748b; font-weight: 600;">28 Nov 2023</td>
                                <td style="padding: 1.5rem; text-align: center;">
                                    <span class="reservas-pill">8</span>
                                </td>
                                <td style="padding: 1.5rem;">
                                    <span class="status-pill pill-activo">● ACTIVO</span>
                                </td>
                                <td style="padding: 1.5rem; text-align: right;">
                                    <i class="fa-solid fa-ellipsis" style="color: #94a3b8; cursor: pointer;"></i>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </section>

                <!-- Footer Stats -->
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem;">
                    <div class="kpi-card" style="padding: 2rem;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem;">
                            <div style="background: #eff6ff; color: #3b82f6; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-user-plus"></i></div>
                        </div>
                        <span style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Nuevos este mes</span>
                        <div style="font-size: 2.25rem; font-weight: 800; color: #1e293b; margin: 0.5rem 0;">+124</div>
                        <div style="font-size: 11px; font-weight: 700; color: #059669;"><i class="fa-solid fa-arrow-trend-up"></i> 12% incremento</div>
                    </div>

                    <div class="kpi-card" style="padding: 2rem;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem;">
                            <div style="background: #f1f5f9; color: #64748b; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-arrow-rotate-right"></i></div>
                        </div>
                        <span style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Tasa de Conversión</span>
                        <div style="font-size: 2.25rem; font-weight: 800; color: #1e293b; margin: 0.5rem 0;">64.2%</div>
                        <div style="font-size: 11px; color: #94a3b8; font-weight: 600;">De registrados a activos</div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
