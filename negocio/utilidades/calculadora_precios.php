<?php
/**
 * calculadora_precios.php
 * negocio/utilidades/calculadora_precios.php
 */

/**
 * Calcula el desglose de precios detallado para una estancia
 * Utiliza consultas SQL directas por noche para asegurar precisión con las tarifas especiales
 */
function calcularPrecioEstancia($idPropiedad, $fechaInicio, $fechaFin, $conexion) {
    // Validación defensiva: asegurar que las fechas sean strings válidos (YYYY-MM-DD)
    $validarFecha = function(string $f): bool {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $f)) return false;
        $d = DateTime::createFromFormat('Y-m-d', $f);
        return $d && $d->format('Y-m-d') === $f;
    };
    if (!$validarFecha($fechaInicio) || !$validarFecha($fechaFin)) {
        return ['error' => 'Fechas inválidas', 'totalBase' => 0, 'noches' => 0,
                'limpieza' => 0, 'impuestos' => 0, 'granTotal' => 0,
                'precioPromedio' => 0, 'desgloseNoches' => []];
    }

    // 1. Obtener precio base y CATEGORÍA de la propiedad
    $sqlProp = "SELECT p.dPrecioNoche, tp.vNombreCategoria as tipo 
                FROM tbl_propiedad p
                LEFT JOIN tbl_tipo_propiedad tp ON p.idTipoPropiedad = tp.idTipoPropiedad
                WHERE p.idPropiedad = ?";
    $stmtProp = $conexion->prepare($sqlProp);
    $stmtProp->bind_param("i", $idPropiedad);
    $stmtProp->execute();
    $resProp = $stmtProp->get_result();
    $prop = $resProp->fetch_assoc();
    $precioBase = floatval($prop['dPrecioNoche'] ?? 0);
    $categoria  = $prop['tipo'] ?? '';

    // 2. Iterar noche por noche (excluyendo el día de salida)
    $start = new DateTime($fechaInicio);
    $end = new DateTime($fechaFin);
    
    $totalBase = 0;
    $noches = 0;
    $desgloseNoches = [];

    // DatePeriod recorre desde 'start' hasta 'end' (sin incluir 'end')
    $interval = new DateInterval('P1D');
    $period = new DatePeriod($start, $interval, $end);

    foreach ($period as $date) {
        $fechaActual = $date->format('Y-m-d');
        $precioParaEstaNoche = $precioBase;
        $esEspecial = false;

        // 3. Buscar tarifa especial para ESTA noche específica
        // Usamos DATE() para ignorar cualquier componente de tiempo en la BD
        $sqlRate = "SELECT dPrecioNoche FROM tbl_tarifa_propiedad 
                    WHERE idPropiedad = ? 
                    AND bEstado = 1 
                    AND ? BETWEEN DATE(dtFechaInicio) AND DATE(dtFechaFin)
                    ORDER BY idTarifaPropiedad DESC LIMIT 1";
        
        $stmtRate = $conexion->prepare($sqlRate);
        $stmtRate->bind_param("is", $idPropiedad, $fechaActual);
        $stmtRate->execute();
        $resRate = $stmtRate->get_result();

        if ($rowRate = $resRate->fetch_assoc()) {
            $precioParaEstaNoche = floatval($rowRate['dPrecioNoche']);
            $esEspecial = true;
        }
        
        $totalBase += $precioParaEstaNoche;
        $desgloseNoches[] = [
            'fecha' => $fechaActual,
            'precio' => $precioParaEstaNoche,
            'esEspecial' => $esEspecial
        ];
        $noches++;
    }

    // Cálculo dinámico de limpieza por porcentaje según categoría
    $obtenerPorcentaje = function($cat) {
        $porcentajes = [
            'habitacion' => 0.05, 'habitación' => 0.05,
            'departamento' => 0.08,
            'casa' => 0.10,
            'cabaña' => 0.12, 'cabana' => 0.12,
            'villa' => 0.15,
            'lujo' => 0.18, 'premium' => 0.18
        ];
        $catNormalizada = strtolower(trim($cat));
        return $porcentajes[$catNormalizada] ?? 0.08;
    };

    $porcentajeLimpieza = $obtenerPorcentaje($categoria);
    $limpieza  = round($totalBase * $porcentajeLimpieza, 2);
    $impuestos = round($totalBase * 0.16, 2);
    $granTotal = round($totalBase + $limpieza + $impuestos, 2);

    return [
        'totalBase' => $totalBase,
        'noches' => $noches,
        'limpieza' => $limpieza,
        'impuestos' => $impuestos,
        'granTotal' => $granTotal,
        'precioPromedio' => $noches > 0 ? $totalBase / $noches : $precioBase,
        'desgloseNoches' => $desgloseNoches
    ];
}

/**
 * Valida la disponibilidad estricta de una propiedad
 */
function validarDisponibilidad($idPropiedad, $fechaInicio, $fechaFin, $conexion) {
    // 1. Validar contra reservas existentes
    $sqlReserva = "SELECT COUNT(*) AS total FROM tbl_reserva 
                   WHERE idPropiedad = ? 
                   AND vEstatus NOT IN ('Cancelada', 'Cancelado', 'CANCELADA', 'CANCELADO')
                   AND dtFechaInicio < ? AND dtFechaFin > ?";
    $stmtR = $conexion->prepare($sqlReserva);
    $stmtR->bind_param("iss", $idPropiedad, $fechaFin, $fechaInicio);
    $stmtR->execute();
    if ($stmtR->get_result()->fetch_assoc()['total'] > 0) return false;

    // 2. Validar contra bloqueos del anfitrión
    $sqlBloqueo = "SELECT COUNT(*) AS total FROM tbl_disponibilidad_administrativa_propiedad 
                   WHERE idPropiedad = ? AND bEstado = 1
                   AND dtFechaInicio < ? AND dtFechaFin > ?";
    $stmtB = $conexion->prepare($sqlBloqueo);
    $stmtB->bind_param("iss", $idPropiedad, $fechaFin, $fechaInicio);
    $stmtB->execute();
    if ($stmtB->get_result()->fetch_assoc()['total'] > 0) return false;

    return true;
}
