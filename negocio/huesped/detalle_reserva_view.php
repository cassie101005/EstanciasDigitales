<?php
// negocio/huesped/detalle_reserva_view.php

function getReservationDetails($idReserva, $userId, $conexion) {
    $sqlRes = "SELECT r.*, p.vNombre as nombrePropiedad, p.dPrecioNoche,
                      u.vNombre as hostNombre, u.vApellido as hostApellido,
                      tp.vNombreCategoria as tipo, ci.vNombreCiudad as ciudad,
                      es.vNombreEstado as estado, pa.vNombrePais as pais
               FROM tbl_reserva r
               JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad
               JOIN tbl_usuarios u ON p.idUsuario = u.idUsuario
               LEFT JOIN tbl_tipo_propiedad tp ON p.idTipoPropiedad = tp.idTipoPropiedad
               LEFT JOIN tbl_ciudad ci ON p.idCiudad = ci.idCiudad
               LEFT JOIN tbl_estado es ON ci.idEstado = es.idEstado
               LEFT JOIN tbl_pais pa ON es.idPais = pa.idPais
               WHERE r.idReserva = ? AND r.idUsuario = ?";

    $stmtRes = $conexion->prepare($sqlRes);
    $stmtRes->bind_param("ii", $idReserva, $userId);
    $stmtRes->execute();
    return $stmtRes->get_result()->fetch_assoc();
}

function getReservationMainImage($idPropiedad, $conexion) {
    $sqlImages = "SELECT vImagen FROM tbl_imagen_propiedad WHERE idPropiedad = ? ORDER BY idImagen ASC";
    $stmtImages = $conexion->prepare($sqlImages);
    $stmtImages->bind_param("i", $idPropiedad);
    $stmtImages->execute();
    $imagesResult = $stmtImages->get_result();
    $images = [];
    while ($row = $imagesResult->fetch_assoc()) {
        $images[] = $row['vImagen'];
    }
    $mainImage = !empty($images) ? $images[0] : "";
    if ($mainImage && strpos($mainImage, 'http') === false) {
        $mainImage = "../../" . $mainImage;
    } elseif (!$mainImage) {
        $mainImage = "https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80";
    }
    return $mainImage;
}

require_once __DIR__ . '/../utilidades/helper_reservas.php';

function calculateReservationStatus($reserva) {
    $info = obtenerEstadoReserva($reserva);
    $fechaInicio = new DateTime($reserva['dtFechaInicio']);
    $fechaFin = new DateTime($reserva['dtFechaFin']);
    
    return [
        'noches' => $info['noches'],
        'status' => strtoupper($info['label']),
        'color' => $info['color'],
        'bgColor' => $info['bgColor'],
        'fechaInicio' => $fechaInicio,
        'fechaFin' => $fechaFin
    ];
}
