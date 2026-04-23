<?php
session_start();
require_once '../../datos/conexion.php';

// Consultar propiedades
$sql = "SELECT p.*, 
               (SELECT vImagen FROM tbl_imagen_propiedad WHERE idPropiedad = p.idPropiedad LIMIT 1) as imagen,
               ci.vNombreCiudad as ciudad, pa.vNombrePais as pais
        FROM tbl_propiedad p
        LEFT JOIN tbl_ciudad ci ON p.idCiudad = ci.idCiudad
        LEFT JOIN tbl_estado es ON ci.idEstado = es.idEstado
        LEFT JOIN tbl_pais pa ON es.idPais = pa.idPais
        ORDER BY p.dtFechaRegistro DESC";

$result = $conexion->query($sql);
$properties = [];
while ($row = $result->fetch_assoc()) {
    $properties[] = [
        'id' => $row['idPropiedad'],
        'loc' => $row['vNombre'],
        'desc' => $row['ciudad'] . ', ' . $row['pais'],
        'dates' => "Disponible ahora",
        'price' => '$' . number_format($row['dPrecioNoche'], 0),
        'rating' => "4.9",
        'fav' => false,
        'img' => $row['imagen'] ?? "https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=600&q=80"
    ];
}
?>
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
        <?php if (count($properties) > 0): ?>
            <?php foreach ($properties as $p): ?>
                <div class="prop-card-v2" onclick="window.location.href = 'detalle.php?id=<?php echo $p['id']; ?>'">
                    <div class="img-container">
                        <img src="<?php echo htmlspecialchars($p['img']); ?>" alt="<?php echo htmlspecialchars($p['loc']); ?>">
                    </div>
                    <div class="card-content">
                        <div class="card-content-top">
                            <div class="card-title"><?php echo htmlspecialchars($p['loc']); ?></div>
                            <div class="card-rating"><i class="fa-solid fa-star" style="font-size: 0.8rem;"></i> <?php echo $p['rating']; ?></div>
                        </div>
                        <div class="card-desc"><?php echo htmlspecialchars($p['desc']); ?></div>
                        <div class="card-dates"><?php echo htmlspecialchars($p['dates']); ?></div>
                        <div class="card-price"><strong><?php echo $p['price']; ?></strong> noche</div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 4rem;">
                <h2 style="color: #64748b;">No hay propiedades disponibles en este momento.</h2>
            </div>
        <?php endif; ?>
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
</body>
</html>
