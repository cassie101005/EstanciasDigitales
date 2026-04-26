<?php
require_once '../../negocio/auth/verificar_sesion.php';
validarSesion('huesped', '../../');
require_once '../../datos/conexion.php';


// Obtener parámetros de búsqueda y filtrado
$ubicacion = isset($_GET['ubicacion']) ? trim($_GET['ubicacion']) : '';
$huespedes = isset($_GET['huespedes']) ? intval($_GET['huespedes']) : 0;
$fechaInicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fechaFin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';
$categoriaSeleccionada = isset($_GET['categoria']) ? $_GET['categoria'] : '';

require_once '../../negocio/huesped/home_view.php';

// Consultar categorías disponibles para las pestañas
$categorias = getHomeCategories($conexion);

// Mapeo de iconos para categorías comunes
$iconosCategorias = [
    'Casa' => 'fa-house',
    'Departamento' => 'fa-building',
    'Villa' => 'fa-landmark',
    'Alberca' => 'fa-water-ladder',
    'Playa' => 'fa-umbrella-beach',
    'Montaña' => 'fa-mountain',
    'Cabaña' => 'fa-tree',
    'Mansión' => 'fa-monument',
    'Habitación' => 'fa-bed'
];

$properties = getHomeProperties($ubicacion, $huespedes, $fechaInicio, $fechaFin, $categoriaSeleccionada, $conexion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Marketplace | Estancias Digitales</title>
    <link rel="icon" type="image/png" href="../../recursos/img/logo.png">
    <link rel="stylesheet" href="../../recursos/css/main.css">
    <link rel="stylesheet" href="../../recursos/css/components/navbar.css">
    <link rel="stylesheet" href="../../recursos/css/huesped/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../recursos/css/huesped/home.css">
</head>
<body style="background: white;">
    <?php include '../../recursos/navbar.php'; ?>

    <header class="hero-section">
        <form method="GET" action="home.php" class="search-pill-v2">
            <div class="search-input-box">
                <label>Ubicación</label>
                <input type="text" name="ubicacion" placeholder="¿A dónde quieres ir?" value="<?php echo htmlspecialchars($ubicacion); ?>">
            </div>
            <div class="search-input-box">
                <label>Fechas</label>
                <div class="date-inputs">
                    <input type="date" name="fecha_inicio" title="Llegada" value="<?php echo htmlspecialchars($fechaInicio); ?>">
                    <input type="date" name="fecha_fin" title="Salida" value="<?php echo htmlspecialchars($fechaFin); ?>">
                </div>
            </div>
            <div class="search-input-box">
                <label>Huéspedes</label>
                <select name="huespedes">
                    <option value="" disabled selected hidden>Huéspedes</option>
                    <?php for($i=1; $i<=10; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo $huespedes == $i ? 'selected' : ''; ?>><?php echo $i; ?> huésped<?php echo $i>1?'es':''; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <button type="submit" class="btn-search">
                <i class="fa-solid fa-magnifying-glass"></i> Buscar
            </button>
        </form>
    </header>

    <div class="categories-tabs">
        <a href="home.php" class="cat-pill <?php echo $categoriaSeleccionada === '' ? 'active' : ''; ?>">
            <i class="fa-solid fa-border-all"></i>
            <span>Todas</span>
        </a>
        <?php foreach ($categorias as $cat): ?>
            <?php 
                $icono = isset($iconosCategorias[$cat]) ? $iconosCategorias[$cat] : 'fa-house-user';
                $url = "home.php?" . http_build_query(array_merge($_GET, ['categoria' => $cat]));
            ?>
            <a href="<?php echo $url; ?>" class="cat-pill <?php echo $categoriaSeleccionada === $cat ? 'active' : ''; ?>">
                <i class="fa-solid <?php echo $icono; ?>"></i>
                <span><?php echo htmlspecialchars($cat); ?></span>
            </a>
        <?php endforeach; ?>
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
                <i class="fa-solid fa-house-circle-xmark" style="font-size: 3rem; color: #ddd; margin-bottom: 1.5rem;"></i>
                <h2 style="color: #64748b;">No encontramos propiedades que coincidan con tu búsqueda.</h2>
                <p style="color: #94a3b8; margin-top: 0.5rem;">Intenta cambiar los filtros o la ubicación.</p>
                <a href="home.php" style="display: inline-block; margin-top: 2rem; color: var(--primary); font-weight: 700; text-decoration: underline;">Limpiar filtros</a>
            </div>
        <?php endif; ?>
    </div>

    <footer class="main-footer">
    </footer>

    <script src="../../recursos/js/huesped/home.js"></script>

</body>
</html>
