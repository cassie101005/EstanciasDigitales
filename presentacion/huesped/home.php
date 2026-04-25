<?php
session_start();
require_once '../../datos/conexion.php';

// Obtener parámetros de búsqueda y filtrado
$ubicacion = isset($_GET['ubicacion']) ? trim($_GET['ubicacion']) : '';
$huespedes = isset($_GET['huespedes']) ? intval($_GET['huespedes']) : 0;
$fechaInicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fechaFin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';
$categoriaSeleccionada = isset($_GET['categoria']) ? $_GET['categoria'] : '';

// Consultar categorías disponibles para las pestañas
$sqlCats = "SELECT MIN(idTipoPropiedad) as id, vNombreCategoria FROM tbl_tipo_propiedad WHERE bEstado = 1 GROUP BY vNombreCategoria ORDER BY vNombreCategoria ASC";
$resCats = $conexion->query($sqlCats);
$categorias = [];
while ($cat = $resCats->fetch_assoc()) {
    $categorias[] = $cat['vNombreCategoria'];
}

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

// Construir la consulta base
$sql = "SELECT p.*, 
               (SELECT vImagen FROM tbl_imagen_propiedad WHERE idPropiedad = p.idPropiedad LIMIT 1) as imagen,
               (SELECT COALESCE(AVG(iCalificacion), 0.0) FROM tbl_resenia WHERE idPropiedad = p.idPropiedad) as promedio_rating,
               ci.vNombreCiudad as ciudad, pa.vNombrePais as pais, tp.vNombreCategoria as tipo
        FROM tbl_propiedad p
        LEFT JOIN tbl_ciudad ci ON p.idCiudad = ci.idCiudad
        LEFT JOIN tbl_estado es ON ci.idEstado = es.idEstado
        LEFT JOIN tbl_pais pa ON es.idPais = pa.idPais
        LEFT JOIN tbl_tipo_propiedad tp ON p.idTipoPropiedad = tp.idTipoPropiedad
        WHERE 1=1";

$params = [];
$types = "";

// Filtrar por ubicación
if ($ubicacion !== '') {
    $sql .= " AND (p.vNombre LIKE ? OR ci.vNombreCiudad LIKE ? OR es.vNombreEstado LIKE ? OR pa.vNombrePais LIKE ?)";
    $search = "%$ubicacion%";
    $params[] = $search; $params[] = $search; $params[] = $search; $params[] = $search;
    $types .= "ssss";
}

// Filtrar por huéspedes
if ($huespedes > 0) {
    $sql .= " AND p.iCapacidadHuespedes >= ?";
    $params[] = $huespedes;
    $types .= "i";
}

// Filtrar por categoría
if ($categoriaSeleccionada !== '') {
    $sql .= " AND tp.vNombreCategoria = ?";
    $params[] = $categoriaSeleccionada;
    $types .= "s";
}

// Filtrar por fechas (disponibilidad)
if ($fechaInicio !== '' && $fechaFin !== '') {
    $sql .= " AND p.idPropiedad NOT IN (
                SELECT idPropiedad FROM tbl_reserva 
                WHERE (dtFechaInicio < ? AND dtFechaFin > ?)
              )
              AND p.idPropiedad NOT IN (
                SELECT idPropiedad FROM tbl_disponibilidad_administrativa_propiedad
                WHERE bEstado = 1 AND (dtFechaInicio < ? AND dtFechaFin > ?)
              )";
    $params[] = $fechaFin; $params[] = $fechaInicio;
    $params[] = $fechaFin; $params[] = $fechaInicio;
    $types .= "ssss";
}

$sql .= " ORDER BY p.dtFechaRegistro DESC";

$stmt = $conexion->prepare($sql);
if ($types !== "") {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$properties = [];
while ($row = $result->fetch_assoc()) {
    $properties[] = [
        'id' => $row['idPropiedad'],
        'loc' => $row['vNombre'],
        'desc' => $row['ciudad'] . ', ' . $row['pais'],
        'dates' => ($fechaInicio && $fechaFin) ? date('d M', strtotime($fechaInicio)) . " - " . date('d M', strtotime($fechaFin)) : "Disponible ahora",
        'price' => '$' . number_format($row['dPrecioNoche'], 0),
        'rating' => $row['promedio_rating'] > 0 ? number_format($row['promedio_rating'], 1) : "Nuevo",
        'fav' => false,
        'img' => !empty($row['imagen']) ? "../../" . str_replace(' ', '%20', $row['imagen']) : "https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=600&q=80"
    ];
}
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
    <style>
        .hero-section {
            height: 420px;
            background: linear-gradient(rgba(0,0,0,0.2), rgba(0,0,0,0.2)), url('../../recursos/img/marketplace_hero.png') center/cover;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }
        .search-pill-v2 {
            display: flex;
            align-items: center;
            background: white;
            border-radius: 100px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 8px 16px;
            max-width: 900px;
            margin: 0 auto;
            border: 1px solid #eee;
        }
        .search-input-box {
            flex: 1;
            padding: 0 16px;
            border-right: 1px solid #eee;
        }
        .search-input-box:last-of-type {
            border-right: none;
        }
        .search-input-box label {
            display: block;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            color: #1a1a1a;
            margin-bottom: 2px;
        }
        .search-input-box input, .search-input-box select {
            width: 100%;
            border: none;
            outline: none;
            font-size: 14px;
            color: #6a6a6a;
            background: transparent;
            padding: 2px 0;
        }
        .btn-search {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 100px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: transform 0.2s;
        }
        .btn-search:hover {
            transform: scale(1.05);
            background: #6d28d9;
        }
        .date-inputs {
            display: flex;
            gap: 4px;
        }
        .date-inputs input {
            font-size: 12px !important;
        }
        .categories-tabs {
            padding: 2rem 4rem;
            display: flex;
            gap: 2.5rem;
            overflow-x: auto;
            background: white;
            scrollbar-width: none;
            border-bottom: 1px solid #eee;
        }
        .categories-tabs::-webkit-scrollbar {
            display: none;
        }
        .cat-pill {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            font-size: 13px;
            font-weight: 600;
            color: #717171;
            cursor: pointer;
            min-width: fit-content;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
            text-decoration: none;
        }
        .cat-pill i { font-size: 1.5rem; }
        .cat-pill:hover {
            color: #000;
            border-bottom: 2px solid #ddd;
        }
        .cat-pill.active {
            color: #000;
            border-bottom: 2px solid #000;
            opacity: 1;
        }
    </style>
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
                    <option value="">Añadir huéspedes</option>
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
