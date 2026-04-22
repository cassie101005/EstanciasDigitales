<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Marketplace | Estancias Digitales</title>
    <link rel="stylesheet" href="../../recursos/css/main.css">
    <link rel="stylesheet" href="../../recursos/css/components/navbar.css">
    <link rel="stylesheet" href="../../recursos/css/huesped/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background: white;">
    <?php include '../../recursos/navbar.php'; ?>

    <header class="hero-section">
        <div class="search-pill-v2">
            <div class="search-input-box">
                <label>Ubicación</label>
                <p>¿A dónde quieres ir?</p>
            </div>
            <div class="search-input-box">
                <label>Fechas</label>
                <p>Agregar fechas</p>
            </div>
            <div class="search-input-box">
                <label>Huéspedes</label>
                <p>¿Cuántos?</p>
            </div>
            <button class="btn-search">
                <i class="fa-solid fa-magnifying-glass"></i> Buscar
            </button>
        </div>
    </header>

    <div class="categories-tabs">
        <div class="cat-pill active"><i class="fa-solid fa-house"></i> Casas</div>
        <div class="cat-pill"><i class="fa-solid fa-building"></i> Departamentos</div>
        <div class="cat-pill"><i class="fa-solid fa-landmark"></i> Villas</div>
        <div class="cat-pill"><i class="fa-solid fa-water-ladder"></i> Albercas</div>
        <div class="cat-pill"><i class="fa-solid fa-umbrella-beach"></i> Playa</div>
        <div class="cat-pill"><i class="fa-solid fa-mountain"></i> Montaña</div>
    </div>

    <div class="prop-grid" id="mainGrid">
        <!-- Loaded by JS -->
    </div>

    <footer class="main-footer">
        <div>Estancias Digitales © 2024 ESTANCIAS DIGITALES. TODOS LOS DERECHOS RESERVADOS.</div>
        <div class="footer-links">
            <a href="#">Términos</a>
            <a href="#">Privacidad</a>
            <a href="#">Carreras</a>
            <a href="#">Ayuda</a>
        </div>
    </footer>

    <script>
        const properties = [
            { id: 1, loc: "Tulum, México", desc: "A 2,450 km de distancia", dates: "12-17 de oct.", price: "$4,500", rating: "4.9", fav: true, img: "https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=600&q=80" },
            { id: 2, loc: "Cancún, México", desc: "En la playa", dates: "20-25 de oct.", price: "$7,200", rating: "4.8", fav: false, img: "https://images.unsplash.com/photo-1544984243-ec57ea16fe25?auto=format&fit=crop&w=600&q=80" },
            { id: 3, loc: "Valle de Bravo, México", desc: "Vista a la montaña", dates: "1-6 de nov.", price: "$3,800", rating: "4.95", fav: true, img: "https://images.unsplash.com/photo-1518780664697-55e3ad937233?auto=format&fit=crop&w=600&q=80" },
            { id: 4, loc: "CDMX, México", desc: "Polanco", dates: "15-20 de dic.", price: "$2,900", rating: "4.7", fav: false, img: "https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=600&q=80" },
            { id: 5, loc: "Sayulita, México", desc: "Estilo Boho Chic", dates: "10-15 de nov.", price: "$3,200", rating: "4.88", fav: false, img: "https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=600&q=80" },
            { id: 6, loc: "Monterrey, México", desc: "Chipinque", dates: "5-10 de ene.", price: "$5,100", rating: "4.92", fav: true, img: "https://images.unsplash.com/photo-1510798831971-661eb04b3739?auto=format&fit=crop&w=600&q=80" },
            { id: 7, loc: "Bacalar, México", desc: "Laguna de los siete colores", dates: "22-27 de nov.", price: "$6,800", rating: "4.97", fav: false, img: "https://images.unsplash.com/photo-1583037189850-1921ae7c6121?auto=format&fit=crop&w=600&q=80" },
            { id: 8, loc: "San Miguel, México", desc: "Centro Histórico", dates: "12-18 de dic.", price: "$4,200", rating: "4.89", fav: true, img: "https://images.unsplash.com/photo-1515263487990-61b07816b324?auto=format&fit=crop&w=600&q=80" }
        ];

        const grid = document.getElementById('mainGrid');
        properties.forEach(p => {
            const card = document.createElement('div');
            card.className = 'prop-card-v2';
            card.onclick = () => window.location.href = `detalle.php?id=${p.id}`;
            card.innerHTML = `
                <div class="img-container">
                    <img src="${p.img}" alt="${p.loc}">
                </div>
                <div class="card-content">
                    <div class="card-content-top">
                        <div class="card-title">${p.loc}</div>
                        <div class="card-rating"><i class="fa-solid fa-star" style="font-size: 0.8rem;"></i> ${p.rating}</div>
                    </div>
                    <div class="card-desc">${p.desc}</div>
                    <div class="card-dates">${p.dates}</div>
                    <div class="card-price"><strong>${p.price}</strong> noche</div>
                </div>
            `;
            grid.appendChild(card);
        });
    </script>
</body>
</html>
