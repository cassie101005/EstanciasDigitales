<?php
// negocio/huesped/home_view.php

function getHomeCategories($conexion) {
    $sqlCats = "SELECT MIN(idTipoPropiedad) as id, vNombreCategoria FROM tbl_tipo_propiedad WHERE bEstado = 1 GROUP BY vNombreCategoria ORDER BY vNombreCategoria ASC";
    $resCats = $conexion->query($sqlCats);
    $categorias = [];
    while ($cat = $resCats->fetch_assoc()) {
        $categorias[] = $cat['vNombreCategoria'];
    }
    return $categorias;
}

function getHomeProperties($ubicacion, $huespedes, $fechaInicio, $fechaFin, $categoriaSeleccionada, $conexion) {
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
    
    return $properties;
}
?>
