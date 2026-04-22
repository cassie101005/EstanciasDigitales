<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Villa Serena | Estancias Digitales</title>
    <link rel="stylesheet" href="../../recursos/css/variables.css">
    <link rel="stylesheet" href="../../recursos/css/main.css">
    <link rel="stylesheet" href="../../recursos/css/components/navbar.css">
    <link rel="stylesheet" href="../../recursos/css/huesped/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background: var(--surface);">
    <?php include '../../recursos/navbar.php'; ?>

    <div class="detail-container" style="max-width: 1600px; width: 95%; padding: 4rem 0;">
        <header class="detail-header">
            <h1 class="detail-title">Villa Paraíso</h1>
            <p class="detail-subtitle">Puerto Vallarta, México • <span style="color: var(--primary);">Residencia de Élite</span></p>
        </header>

        <section class="gallery-section" style="height: 700px;">
            <div class="main-img"><img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80" style="width:100%; height:100%; object-fit:cover;"></div>
            <div class="gallery-grid" style="display: grid; grid-template-rows: 1fr 1fr; gap: 0.5rem;">
                <img src="https://images.unsplash.com/photo-1544984243-ec57ea16fe25?auto=format&fit=crop&w=600&q=80" style="width:100%; height:100%; object-fit:cover;">
                <img src="https://images.unsplash.com/photo-1518780664697-55e3ad937233?auto=format&fit=crop&w=600&q=80" style="width:100%; height:100%; object-fit:cover;">
            </div>
        </section>

        <div class="detail-main-grid" style="grid-template-columns: 1fr 450px; gap: 8rem;">
            <main>
                <div class="host-badge" style="border-bottom: 1px solid var(--surface-container-high); padding-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h2 style="font-size: 1.8rem; font-weight: 800; margin-bottom: 0.5rem;">Anfitrión: Carlos Javier</h2>
                        <p style="color: var(--on-surface-variant); font-weight: 600;">10 huéspedes · 5 habitaciones · 7 camas · 5.5 baños</p>
                    </div>
                    <div style="width: 64px; height: 64px; background: #ddd; border-radius: 50%; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <img src="https://i.pravatar.cc/100?u=host" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>

                <div class="info-highlights">
                    <div class="highlight-card">
                        <i class="fa-solid fa-medal"></i>
                        <h3>Superanfitrión</h3>
                        <p>Los Superanfitriones tienen mucha experiencia y valoraciones excelentes.</p>
                    </div>
                    <div class="highlight-card">
                        <i class="fa-solid fa-location-dot"></i>
                        <h3>Ubicación Ideal</h3>
                        <p>El 100% de los huéspedes recientes calificaron la ubicación con 5 estrellas.</p>
                    </div>
                    <div class="highlight-card">
                        <i class="fa-solid fa-calendar-check"></i>
                        <h3>Cancelación Gratuita</h3>
                        <p>Obtén un reembolso completo si cancelas antes de las 48 horas.</p>
                    </div>
                </div>

                <article style="padding: 2rem 0; border-top: 1px solid #eee;">
                    <h2 style="font-size: 1.5rem; font-weight: 800; text-transform: uppercase; margin-bottom: 2rem;">Descripción</h2>
                    <div style="font-size: 1.15rem; line-height: 1.8; color: var(--on-surface-variant); max-width: 800px;">
                        <p style="margin-bottom: 1.5rem;">Sumérgete en el lujo absoluto en esta impresionante villa frente al mar. Villa Paraíso combina la arquitectura contemporánea con el encanto tropical de Puerto Vallarta. Cada rincón ha sido diseñado para maximizar las vistas panorámicas del océano y permitir que la brisa marina fluya naturalmente a través de sus espacios abiertos.</p>
                        <p style="margin-bottom: 1.5rem;">Disfruta de atardeceres inolvidables desde la alberca infinita o relájate en la amplia terraza privada. Esta propiedad es el refugio perfecto para familias o grupos de amigos que buscan exclusividad y confort total.</p>
                        <a href="#" style="color: var(--primary); font-weight: 800; text-decoration: underline;">Mostrar más</a>
                    </div>
                </article>

                <section style="padding: 4rem 0; border-top: 1px solid #eee;">
                    <h2 style="font-size: 1.5rem; font-weight: 800; text-transform: uppercase; margin-bottom: 2.5rem;">Servicios e Instalaciones</h2>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; font-weight: 600;">
                        <div style="display:flex; align-items:center; gap: 1.25rem;"><i class="fa-solid fa-wifi" style="width: 24px;"></i> Conexión Wifi de alta velocidad</div>
                        <div style="display:flex; align-items:center; gap: 1.25rem;"><i class="fa-solid fa-kitchen-set" style="width: 24px;"></i> Cocina gourmet equipada</div>
                        <div style="display:flex; align-items:center; gap: 1.25rem;"><i class="fa-solid fa-water-ladder" style="width: 24px;"></i> Alberca privada climatizada</div>
                        <div style="display:flex; align-items:center; gap: 1.25rem;"><i class="fa-solid fa-snowflake" style="width: 24px;"></i> Aire acondicionado central</div>
                        <div style="display:flex; align-items:center; gap: 1.25rem;"><i class="fa-solid fa-broom" style="width: 24px;"></i> Servicio de limpieza diario</div>
                        <div style="display:flex; align-items:center; gap: 1.25rem;"><i class="fa-solid fa-car" style="width: 24px;"></i> Estacionamiento gratuito (3 autos)</div>
                    </div>
                    <button class="btn" style="margin-top: 3rem; background: white; border: 1px solid #191b23; border-radius: 8px; padding: 0.8rem 2rem;">Mostrar los 45 servicios</button>
                </section>

                <section class="rules-section">
                    <h2 style="font-size: 1.5rem; font-weight: 800; text-transform: uppercase; margin-bottom: 2rem;">Reglas de la Casa</h2>
                    <div class="rules-grid">
                        <div class="rules-times">
                            <div class="rule-item"><span>Llegada</span> <strong>15:00 - 20:00</strong></div>
                            <div class="rule-item"><span>Salida</span> <strong>11:00</strong></div>
                        </div>
                        <div class="rules-prohibitions">
                            <div class="rule-icon-text"><i class="fa-solid fa-ban-smoking"></i> No se permite fumar</div>
                            <div class="rule-icon-text"><i class="fa-solid fa-paw"></i> No se aceptan mascotas</div>
                            <div class="rule-icon-text"><i class="fa-solid fa-champagne-glasses"></i> No se permiten fiestas</div>
                        </div>
                    </div>
                </section>

                <section class="rules-section" style="border-top: 1px solid #eee;">
                    <h2 style="font-size: 1.5rem; font-weight: 800; text-transform: uppercase; margin-bottom: 2rem;">Políticas de la Estancia</h2>
                    <div class="rules-grid">
                        <div class="rules-times">
                            <div class="rule-item"><span>Check-in Autónomo</span> <strong>Caja de seguridad</strong></div>
                            <div class="rule-item"><span>Deposito Reembolsable</span> <strong>$5,000 MXN</strong></div>
                        </div>
                        <div class="rules-prohibitions">
                            <div class="rule-icon-text" style="color: var(--on-surface);"><i class="fa-solid fa-circle-info" style="color: var(--primary);"></i> Identificación oficial requerida</div>
                            <div class="rule-icon-text" style="color: var(--on-surface);"><i class="fa-solid fa-shield-halved" style="color: var(--primary);"></i> Seguro de daños incluido</div>
                            <div class="rule-icon-text" style="color: var(--on-surface);"><i class="fa-solid fa-clock-rotate-left" style="color: var(--primary);"></i> Cancelación flexible Premium</div>
                        </div>
                    </div>
                </section>
            </main>

            <aside>
                <div class="tonal-card reservation-sidebar" style="width: 100%;">
                    <!-- Sidebar content replicated as before but integrated -->
                    <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 2rem;">
                        <span style="font-size: 1.75rem; font-weight: 800;">$14,500 <span style="font-size: 0.9rem; font-weight: 400; color: #6d7083;">MXN / noche</span></span>
                        <span style="font-weight: 700; font-size: 14px;"><i class="fa-solid fa-star" style="color: var(--primary);"></i> 4.98</span>
                    </div>

                    <div style="border: 1px solid #ddd; border-radius: 0.75rem; overflow: hidden; margin-bottom: 1.5rem;">
                        <div style="display: flex; border-bottom: 1px solid #ddd;">
                            <div style="flex:1; padding: 0.75rem;">
                                <label style="display:block; font-size: 10px; font-weight: 800; text-transform: uppercase;">Llegada</label>
                                <span style="font-size: 14px;">12/06/2024</span>
                            </div>
                            <div style="flex:1; padding: 0.75rem; border-left: 1px solid #ddd;">
                                <label style="display:block; font-size: 10px; font-weight: 800; text-transform: uppercase;">Salida</label>
                                <span style="font-size: 14px;">17/06/2024</span>
                            </div>
                        </div>
                        <div style="padding: 0.75rem; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <label style="display:block; font-size: 10px; font-weight: 800; text-transform: uppercase;">Huéspedes</label>
                                <span style="font-size: 14px;">4 huéspedes</span>
                            </div>
                            <i class="fa-solid fa-chevron-down" style="font-size: 12px;"></i>
                        </div>
                    </div>

                    <div class="calendar-widget">
                        <div class="cal-head">
                            <strong style="text-transform: uppercase; font-size: 12px;">Junio 2024</strong>
                            <div style="display: flex; gap: 0.75rem;">
                                <i class="fa-solid fa-chevron-left" style="font-size: 11px;"></i>
                                <i class="fa-solid fa-chevron-right" style="font-size: 11px;"></i>
                            </div>
                        </div>
                        <div class="cal-grid">
                            <div class="cal-day-label">D</div><div class="cal-day-label">L</div><div class="cal-day-label">M</div><div class="cal-day-label">M</div><div class="cal-day-label">J</div><div class="cal-day-label">V</div><div class="cal-day-label">S</div>
                            <div></div><div></div><div></div><div></div><div></div>
                            <div class="cal-date" style="color: #ddd;">1</div>
                            <div class="cal-date" style="color: #ddd;">2</div>
                            <div class="cal-date" style="color: #ddd;">3</div>
                            <div class="cal-date" style="color: #ddd;">4</div>
                            <div class="cal-date" style="color: #ddd;">5</div>
                            <div class="cal-date" style="color: #ddd;">6</div>
                            <div class="cal-date" style="color: #ddd;">7</div>
                            <div class="cal-date" style="color: #ddd;">8</div>
                            <div class="cal-date" style="color: #ddd;">9</div>
                            <div class="cal-date">10</div>
                            <div class="cal-date">11</div>
                            <div class="cal-date" style="background: var(--primary); color: white; border-radius: 0.5rem 0 0 0.5rem;">12</div>
                            <div class="cal-date" style="background: var(--primary); color: white;">13</div>
                            <div class="cal-date" style="background: var(--primary); color: white;">14</div>
                            <div class="cal-date" style="background: var(--primary); color: white;">15</div>
                            <div class="cal-date" style="background: var(--primary); color: white;">16</div>
                            <div class="cal-date" style="background: var(--primary); color: white; border-radius: 0 0.5rem 0.5rem 0;">17</div>
                            <div class="cal-date">18</div>
                            <div class="cal-date">19</div>
                            <div class="cal-date">20</div>
                        </div>
                    </div>

                    <button class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1.25rem;">Reservar Estancia</button>
                    <p style="text-align: center; margin-top: 1rem; font-size: 12px; color: #6d7083;">No se te cobrará nada aún</p>

                    <ul style="border-top: 1px solid #eee; margin-top: 2rem; padding-top: 2rem; list-style: none;">
                        <li style="display:flex; justify-content: space-between; margin-bottom: 1rem;">
                            <span style="text-decoration: underline;">$14,500 MXN x 5 noches</span> <span>$72,500 MXN</span>
                        </li>
                        <li style="display:flex; justify-content: space-between; margin-bottom: 1rem;">
                            <span style="text-decoration: underline;">Tarifa de limpieza</span> <span>$1,200 MXN</span>
                        </li>
                        <li style="display:flex; justify-content: space-between; padding-top: 1.5rem; border-top: 1px solid #eee; font-weight: 800; font-size: 1.25rem;">
                            <span>Total</span> <span>$82,200 MXN</span>
                        </li>
                    </ul>
                </div>
            </aside>
        </div>
    </div>

    <footer class="main-footer" style="padding: 4rem 5%; background: white;">
        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <div>Estancias Digitales © 2024. Empresa líder en rentas vacacionales de lujo.</div>
            <div class="footer-links" style="display: flex; gap: 2rem;">
                <a href="#">Privacidad</a>
                <a href="#">Términos</a>
                <a href="#">Mapa del sitio</a>
            </div>
        </div>
    </footer>
</body>
</html>
