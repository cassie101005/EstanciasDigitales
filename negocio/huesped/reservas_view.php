<?php
require_once '../../datos/conexion.php';

function updateReservaStates($idUsuario, $conexion) {
    $hoy_str = date('Y-m-d');

    // 1. FINALIZADA (id 3): Si la fecha fin ya pasó y no está cancelada
    $conexion->query("UPDATE tbl_reserva SET idEstadoReserva = 3 
                      WHERE idUsuario = $idUsuario 
                      AND idEstadoReserva != 4 
                      AND dtFechaFin < '$hoy_str'");

    // 2. EN CURSO (id 2): Si hoy está entre inicio y fin y no está cancelada
    $conexion->query("UPDATE tbl_reserva SET idEstadoReserva = 2 
                      WHERE idUsuario = $idUsuario 
                      AND idEstadoReserva != 4 
                      AND '$hoy_str' BETWEEN dtFechaInicio AND dtFechaFin");

    // 3. CONFIRMADA (id 1): Si aún no ha empezado y no tiene otro estado especial
    $conexion->query("UPDATE tbl_reserva SET idEstadoReserva = 1 
                      WHERE idUsuario = $idUsuario 
                      AND idEstadoReserva NOT IN (2, 3, 4) 
                      AND dtFechaInicio > '$hoy_str'");
}

function getGuestReservas($idUsuario, $conexion) {
    $sql = "SELECT r.*, p.vNombre as nombrePropiedad, p.vDescripcion, 
                   (SELECT vImagen FROM tbl_imagen_propiedad WHERE idPropiedad = p.idPropiedad LIMIT 1) as imagen,
                   ci.vNombreCiudad as ciudad, pa.vNombrePais as pais
            FROM tbl_reserva r
            JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad
            LEFT JOIN tbl_ciudad ci ON p.idCiudad = ci.idCiudad
            LEFT JOIN tbl_estado es ON ci.idEstado = es.idEstado
            LEFT JOIN tbl_pais pa ON es.idPais = pa.idPais
            WHERE r.idUsuario = ?
            ORDER BY r.dtFechaInicio DESC";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $reservas = [];
    while ($row = $result->fetch_assoc()) {
        $reservas[] = $row;
    }
    return $reservas;
}
?>
