<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Reservaciones | Estancias Digitales</title>
    <link rel="stylesheet" href="../../recursos/css/variables.css">
    <link rel="stylesheet" href="../../recursos/css/main.css">
    <link rel="stylesheet" href="../../recursos/css/components/navbar.css">
    <link rel="stylesheet" href="../../recursos/css/huesped/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background: #f8f9fa;">
    <?php include '../../recursos/navbar.php'; ?>

    <div class="reservation-container">
        <header>
            <h1 style="font-size: 2.5rem; font-weight: 800; color: #191b23; margin-bottom: 0.5rem;">Mis Reservaciones</h1>
            <p style="color: #64748b; font-size: 1.1rem;">Gestiona tus estancias actuales y revisa tus experiencias pasadas.</p>
        </header>

        <div class="filter-pills">
            <button class="filter-pill active">Todas</button>
            <button class="filter-pill">Próximas</button>
            <button class="filter-pill">En curso</button>
            <button class="filter-pill">Completadas</button>
        </div>

        <div class="reservations-list">
            
            <!-- Reservation Card: EN CURSO -->
            <div class="res-card-v2">
                <div class="res-img-box">
                    <img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=600&q=80">
                    <div class="status-badge-v2" style="background: #008a60;">EN CURSO</div>
                </div>
                <div class="res-content-box">
                    <h2 style="font-size: 1.5rem; font-weight: 700;">Villa Horizonte Azul</h2>
                    <div style="font-size: 14px; color: #64748b; display: flex; flex-direction: column; gap: 0.5rem;">
                        <span style="display: flex; align-items: center; gap: 0.75rem;"><i class="fa-regular fa-calendar"></i> 15 Oct - 20 Oct, 2024</span>
                        <span style="display: flex; align-items: center; gap: 0.75rem;"><i class="fa-solid fa-location-dot"></i> Ibiza, España</span>
                    </div>
                    <div class="res-actions">
                        <button class="btn btn-primary" onclick="window.location.href='detalle.php'">Ver detalle</button>
                        <button class="btn btn-res-grey">Contactar anfitrión</button>
                    </div>
                    <div class="res-price-abs">$1,250 <span>total</span></div>
                </div>
            </div>

            <!-- Reservation Card: FINALIZADA -->
            <div class="res-card-v2">
                <div class="res-img-box">
                    <img src="https://images.unsplash.com/photo-1554995207-c18c203602cb?auto=format&fit=crop&w=600&q=80">
                    <div class="status-badge-v2" style="background: #6c757d;">FINALIZADA</div>
                </div>
                <div class="res-content-box">
                    <h2 style="font-size: 1.5rem; font-weight: 700;">Loft Soho Luxury</h2>
                    <div style="font-size: 14px; color: #64748b; display: flex; flex-direction: column; gap: 0.5rem;">
                        <span style="display: flex; align-items: center; gap: 0.75rem;"><i class="fa-solid fa-rotate-left"></i> 02 Sep - 05 Sep, 2024</span>
                        <span style="display: flex; align-items: center; gap: 0.75rem;"><i class="fa-solid fa-location-dot"></i> Nueva York, USA</span>
                    </div>
                    <div class="res-actions">
                        <button class="btn btn-res-grey">Ver factura</button>
                        <button class="btn btn-res-outline">Volver a reservar</button>
                    </div>
                    <div class="res-price-abs">$840 <span>total</span></div>
                </div>
            </div>

            <!-- Reservation Card: CONFIRMADA -->
            <div class="res-card-v2">
                <div class="res-img-box">
                    <img src="https://images.unsplash.com/photo-1544984243-ec57ea16fe25?auto=format&fit=crop&w=600&q=80">
                    <div class="status-badge-v2" style="background: #1e40af;">CONFIRMADA</div>
                </div>
                <div class="res-content-box">
                    <h2 style="font-size: 1.5rem; font-weight: 700;">Refugio Maldivas</h2>
                    <div style="font-size: 14px; color: #64748b; display: flex; flex-direction: column; gap: 0.5rem;">
                        <span style="display: flex; align-items: center; gap: 0.75rem;"><i class="fa-regular fa-calendar"></i> 20 Dic - 27 Dic, 2024</span>
                        <span style="display: flex; align-items: center; gap: 0.75rem;"><i class="fa-solid fa-location-dot"></i> Atolón Norte, Maldivas</span>
                    </div>
                    <div class="res-actions">
                        <button class="btn btn-primary">Ver detalle</button>
                        <button class="btn btn-res-grey">Gestionar reserva</button>
                    </div>
                    <div class="res-price-abs">$3,400 <span>total</span></div>
                </div>
            </div>

        </div>
    </div>

    <footer class="main-footer" style="padding: 4rem 10%; margin-top: 6rem; background: white;">
        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <div>Estancias Digitales © 2024. Tus viajes, simplificados.</div>
            <div class="footer-links">
                <a href="#">Privacidad</a>
                <a href="#">Ayuda</a>
            </div>
        </div>
    </footer>
</body>
</html>
