<?php
require_once '../../datos/conexion.php';

function getPropertyDetail($idPropiedad, $conexion) {
    $sql = "SELECT p.*, u.vNombre as hostNombre, u.vApellido as hostApellido, 
                   tp.vNombreCategoria as tipo, ci.vNombreCiudad as ciudad, 
                   es.vNombreEstado as estado, pa.vNombrePais as pais
            FROM tbl_propiedad p
            JOIN tbl_usuarios u ON p.idUsuario = u.idUsuario
            LEFT JOIN tbl_tipo_propiedad tp ON p.idTipoPropiedad = tp.idTipoPropiedad
            LEFT JOIN tbl_ciudad ci ON p.idCiudad = ci.idCiudad
            LEFT JOIN tbl_estado es ON ci.idEstado = es.idEstado
            LEFT JOIN tbl_pais pa ON es.idPais = pa.idPais
            WHERE p.idPropiedad = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $idPropiedad);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getPropertyImages($idPropiedad, $conexion) {
    $sqlImages = "SELECT vImagen FROM tbl_imagen_propiedad WHERE idPropiedad = ? ORDER BY idImagen ASC";
    $stmtImages = $conexion->prepare($sqlImages);
    $stmtImages->bind_param("i", $idPropiedad);
    $stmtImages->execute();
    $imagesResult = $stmtImages->get_result();
    $images = [];
    while ($row = $imagesResult->fetch_assoc()) {
        $images[] = "../../" . str_replace(' ', '%20', $row['vImagen']);
    }
    return $images;
}

function getPropertyServices($idPropiedad, $conexion) {
    $sqlServices = "SELECT se.vNombreServicio 
                    FROM tbl_propiedad_servicios ps
                    JOIN tbl_servicios_extra se ON ps.idServicio = se.idServicio
                    WHERE ps.idPropiedad = ?";
    $stmtServices = $conexion->prepare($sqlServices);
    $stmtServices->bind_param("i", $idPropiedad);
    $stmtServices->execute();
    $servicesResult = $stmtServices->get_result();
    $services = [];
    while ($row = $servicesResult->fetch_assoc()) {
        $services[] = $row['vNombreServicio'];
    }
    return $services;
}

function getPropertyResenias($idPropiedad, $conexion) {
    $sqlResenias = "SELECT * FROM (
                        SELECT r.idResenia as id, 'resenia' as tipo, r.vComentario, r.iCalificacion, r.dtFechaResenia as fecha,
                               u.vNombre, u.vApellido, u.vFoto, u.idUsuario,
                               r.vRespuesta, r.dtFechaRespuesta,
                               CONCAT(h.vNombre, ' ', h.vApellido) as hostNombre, h.vFoto as hostFoto
                        FROM tbl_resenia r
                        JOIN tbl_usuarios u ON r.idUsuario = u.idUsuario
                        JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad
                        JOIN tbl_usuarios h ON p.idUsuario = h.idUsuario
                        WHERE r.idPropiedad = ?
                        UNION ALL
                        SELECT c.idComentario as id, 'comentario' as tipo, c.vComentario, c.iCalificacion, c.dtFechaRegistro as fecha,
                               u.vNombre, u.vApellido, u.vFoto, u.idUsuario,
                               c.vRespuesta, NULL as dtFechaRespuesta,
                               CONCAT(h.vNombre, ' ', h.vApellido) as hostNombre, h.vFoto as hostFoto
                        FROM tbl_comentarios c
                        JOIN tbl_usuarios u ON c.idUsuario = u.idUsuario
                        JOIN tbl_propiedad p ON c.idPropiedad = p.idPropiedad
                        JOIN tbl_usuarios h ON p.idUsuario = h.idUsuario
                        WHERE c.idPropiedad = ?
                    ) as t
                    ORDER BY fecha DESC
                    LIMIT 10";
    $stmtRes = $conexion->prepare($sqlResenias);
    $stmtRes->bind_param("ii", $idPropiedad, $idPropiedad);
    $stmtRes->execute();
    $result = $stmtRes->get_result();
    
    $resenias = [];
    while ($row = $result->fetch_assoc()) {
        $resenias[] = $row;
    }
    return $resenias;
}

function getReservedDates($idPropiedad, $conexion) {
    $sqlReserved = "SELECT dtFechaInicio, dtFechaFin FROM tbl_reserva WHERE idPropiedad = ? AND vEstatus NOT IN ('Cancelada')";
    $stmtReserved = $conexion->prepare($sqlReserved);
    $stmtReserved->bind_param("i", $idPropiedad);
    $stmtReserved->execute();
    $resReserved = $stmtReserved->get_result();
    $reservedDates = [];
    while ($row = $resReserved->fetch_assoc()) {
        $reservedDates[] = [
            'start' => $row['dtFechaInicio'],
            'end' => $row['dtFechaFin']
        ];
    }
    return $reservedDates;
}

function checkUserCalifico($idPropiedad, $idUsuario, $conexion) {
    $sqlCheck = "SELECT iCalificacion FROM tbl_resenia WHERE idPropiedad = ? AND idUsuario = ? AND iCalificacion > 0";
    $stmtCheck = $conexion->prepare($sqlCheck);
    $stmtCheck->bind_param("ii", $idPropiedad, $idUsuario);
    $stmtCheck->execute();
    $resCheck = $stmtCheck->get_result();
    if ($rowCheck = $resCheck->fetch_assoc()) {
        return [true, $rowCheck['iCalificacion']];
    }
    return [false, 0];
}
?>
