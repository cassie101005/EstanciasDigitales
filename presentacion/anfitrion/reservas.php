<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Reservas | Modo Anfitrión</title>
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
                    <li class="side-nav-item" onclick="window.location.href='calendario.php'"><i class="fa-solid fa-calendar-days"></i> Calendario</li>
                    <li class="side-nav-item active" onclick="window.location.href='reservas.php'"><i class="fa-solid fa-receipt"></i> Reservas</li>
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
                        <h1 style="font-size: 2rem; font-weight: 800; letter-spacing: -1px; margin-bottom: 0.5rem;">Gestión de Reservas</h1>
                        <p style="color: #64748b; font-size: 14px; max-width: 600px;">Supervise sus reservas entrantes y mantenga una comunicación fluida con sus huéspedes para asegurar una experiencia de cinco estrellas.</p>
                    </div>
                    <div style="display: flex; gap: 1rem;">
                        <button class="btn btn-primary" style="padding: 0.8rem 1.5rem; font-weight: 800; font-size: 13px; border-radius: 12px; background: var(--primary); color: white;"><i class="fa-solid fa-plus"></i> Nueva Reserva</button>
                    </div>
                </header>

                <!-- KPI Grid -->
                <section class="kpi-host-grid">
                    <div class="kpi-host-card">
                        <span class="label">Total Reservas</span>
                        <div class="value">124 <span style="font-size: 12px; color: #10b981; margin-left: 10px;">+12%</span></div>
                    </div>
                    <div class="kpi-host-card">
                        <span class="label">Calificación Media</span>
                        <div class="value">4.9 <i class="fa-solid fa-star" style="color: var(--primary); font-size: 1rem;"></i></div>
                    </div>
                    <div class="kpi-host-card">
                        <span class="label">Ocupación este mes</span>
                        <div class="value">88%</div>
                    </div>
                </section>

                <!-- Reservations Table -->
                <section class="admin-table-container" style="border-radius: 1.5rem; margin-top: 2rem;">
                    <div style="padding: 2rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; flex-wrap: wrap; gap: 1.5rem;">
                        <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--text-main);">Reservas Recientes</h3>
                        <div style="display: flex; gap: 1rem; font-size: 12px; font-weight: 800;">
                            <span style="color: white; background: var(--primary); padding: 8px 16px; border-radius: 99px; cursor: pointer;">Todas</span>
                            <span style="color: #64748b; background: #f8fafc; padding: 8px 16px; border-radius: 99px; cursor: pointer;">Confirmadas</span>
                            <span style="color: #64748b; background: #f8fafc; padding: 8px 16px; border-radius: 99px; cursor: pointer;">Finalizadas</span>
                        </div>
                    </div>
                    
                    <div style="overflow-x: auto;">
                        <table class="table-v2">
                            <thead>
                                <tr style="background: #f8fafc;">
                                    <th>Cliente</th>
                                    <th>Propiedad</th>
                                    <th>Fechas</th>
                                    <th>Estado</th>
                                    <th>Total</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 1rem;">
                                            <img src="https://i.pravatar.cc/100?u=e" style="width: 40px; height: 40px; border-radius: 12px; object-fit: cover;">
                                            <div>
                                                <div style="font-size: 14px; font-weight: 800; color: var(--text-main);">Elena Rodríguez</div>
                                                <div style="font-size : 11px; color: #64748b; font-weight: 600;">Súper Huésped • 12 estancias</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 14px; font-weight: 700; color: var(--text-main);">Villa Mediterránea</div>
                                        <div style="font-size: 11px; color: #94a3b8; font-weight: 600;">Alicante, ES</div>
                                    </td>
                                    <td>
                                        <div style="font-size: 14px; font-weight: 700; color: var(--text-main);">12 May - 18 May</div>
                                        <div style="font-size: 11px; color: #64748b; font-weight: 600;">6 noches</div>
                                    </td>
                                    <td><span class="status-tag" style="background: #d1fae5; color: #065f46;">Confirmada</span></td>
                                    <td><strong style="font-size: 15px; color: var(--primary);">€1,240.00</strong></td>
                                    <td>
                                        <button style="border: none; background: #f1f5f9; padding: 8px; border-radius: 8px; color: #64748b; cursor: pointer;">
                                            <i class="fa-solid fa-ellipsis"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 1rem;">
                                            <img src="https://i.pravatar.cc/100?u=m" style="width: 40px; height: 40px; border-radius: 12px; object-fit: cover;">
                                            <div>
                                                <div style="font-size: 14px; font-weight: 800; color: var(--text-main);">Marco Jansen</div>
                                                <div style="font-size : 11px; color: #64748b; font-weight: 600;">Huésped Nuevo</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 14px; font-weight: 700; color: var(--text-main);">Loft Industrial</div>
                                        <div style="font-size: 11px; color: #94a3b8; font-weight: 600;">Madrid, ES</div>
                                    </td>
                                    <td>
                                        <div style="font-size: 14px; font-weight: 700; color: var(--text-main);">05 May - 08 May</div>
                                        <div style="font-size: 11px; color: #64748b; font-weight: 600;">3 noches</div>
                                    </td>
                                    <td><span class="status-tag" style="background: #f1f5f9; color: #64748b;">Finalizada</span></td>
                                    <td><strong style="font-size: 15px; color: var(--text-main);">€450.00</strong></td>
                                    <td>
                                        <button style="border: none; background: #f1f5f9; padding: 8px; border-radius: 8px; color: #64748b; cursor: pointer;">
                                            <i class="fa-solid fa-ellipsis"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Reviews Section -->
                <section style="margin-top: 5rem;">
                    <header style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 3rem; flex-wrap: wrap; gap: 1.5rem;">
                         <div>
                            <h2 style="font-size: 2rem; font-weight: 800; color: var(--text-main); letter-spacing: -1px;">Reseñas de Huéspedes</h2>
                            <p style="color: #64748b; font-size: 15px; font-weight: 500; margin-top: 0.5rem;">Gestione el feedback de sus clientes y mejore su reputación.</p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--primary); font-weight: 800; background: #f0f4ff; padding: 10px 20px; border-radius: 12px;">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <span style="font-size: 15px; margin-left: 10px;">4.9 / 5.0</span>
                        </div>
                    </header>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem;">
                        <div class="review-card">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem;">
                                <div style="display: flex; gap: 1rem;">
                                    <img src="https://i.pravatar.cc/100?u=l" style="width: 48px; height: 48px; border-radius: 14px; object-fit: cover;">
                                    <div>
                                        <div style="font-size: 15px; font-weight: 800; color: var(--text-main);">Lucía Méndez</div>
                                        <div style="font-size: 12px; color: #94a3b8; font-weight: 600;">Villa Mediterránea • Abril 2024</div>
                                    </div>
                                </div>
                                <div style="color: var(--primary); font-size: 12px; display: flex; gap: 2px;">
                                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                                </div>
                            </div>
                            <p style="font-style: italic; color: #475569; font-size: 15px; line-height: 1.7; margin-bottom: 2rem; background: #fcfdfe; padding: 1rem; border-radius: 12px;">"Una estancia maravillosa. La casa estaba impecable y los detalles de bienvenida fueron un toque excelente. Volveremos sin duda."</p>
                            <div style="text-align: right; border-top: 1px solid #f1f5f9; padding-top: 1.5rem;">
                                <a href="#" style="font-size: 13px; font-weight: 800; color: var(--primary); text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">Responder comentario <i class="fa-solid fa-reply"></i></a>
                            </div>
                        </div>

                        <div class="review-card">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem;">
                                <div style="display: flex; gap: 1rem;">
                                    <img src="https://i.pravatar.cc/100?u=t" style="width: 48px; height: 48px; border-radius: 14px; object-fit: cover;">
                                    <div>
                                        <div style="font-size: 15px; font-weight: 800; color: var(--text-main);">Thomas Müller</div>
                                        <div style="font-size: 12px; color: #94a3b8; font-weight: 600;">Loft Industrial • Marzo 2024</div>
                                    </div>
                                </div>
                                <div style="color: var(--primary); font-size: 12px; display: flex; gap: 2px;">
                                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i>
                                </div>
                            </div>
                            <p style="font-style: italic; color: #475569; font-size: 15px; line-height: 1.7; margin-bottom: 1.5rem; background: #fcfdfe; padding: 1rem; border-radius: 12px;">"El apartamento es muy céntrico y moderno. Solo tuvimos un pequeño problema con el Wi-Fi el primer día, pero el anfitrión lo resolvió rápido."</p>
                            
                            <div style="background: #f8fafc; padding: 1.5rem; border-radius: 16px; border: 1px solid #f1f5f9;">
                                <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: var(--primary); margin-bottom: 0.75rem; letter-spacing: 0.5px;">Tu respuesta</div>
                                <p style="font-size: 14px; color: #64748b; line-height: 1.6;">"Gracias Thomas, nos alegra que pudieras disfrutar del loft a pesar del contratiempo técnico."</p>
                                <div style="text-align: right; margin-top: 1.25rem;">
                                    <a href="#" style="font-size: 12px; font-weight: 800; color: #94a3b8; text-decoration: none;">Editar respuesta</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
