<?php
require_once '../../datos/conexion.php';

function getHostReservas($idHost, $conexion) {
    $sqlRes = "SELECT r.*, p.vNombre as nombrePropiedad, p.idPropiedad, u.vNombre as guestNombre, u.vApellido as guestApellido, u.vFoto as guestFoto,
                      MAX(c.vMotivo) as motivoCancelacionReal,
                      MAX(c.dPenalizacion) as penalizacion,
                      MAX(c.dReembolso) as reembolso,
                      MAX(c.vTipoCancelacion) as tipoCancelacion
               FROM tbl_reserva r
               JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad
               JOIN tbl_usuarios u ON r.idUsuario = u.idUsuario
               LEFT JOIN tbl_cancelacion c ON r.idReserva = c.idReserva
               WHERE p.idUsuario = ?
               GROUP BY r.idReserva
               ORDER BY r.dtFechaInicio DESC";
    $stmtRes = $conexion->prepare($sqlRes);
    $stmtRes->bind_param("i", $idHost);
    $stmtRes->execute();
    $result = $stmtRes->get_result();
    
    $reservas = [];
    while ($row = $result->fetch_assoc()) {
        $reservas[] = $row;
    }
    return $reservas;
}

function getHostComentarios($idHost, $conexion) {
    $sqlCom = "SELECT id, tipo, vComentario, iCalificacion, fecha, nombrePropiedad, guestNombre, guestApellido, guestFoto, idUsuario, vRespuesta FROM (
                    SELECT c.idComentario as id, 'comentario' as tipo, c.vComentario, c.iCalificacion, c.dtFechaRegistro as fecha, p.vNombre as nombrePropiedad, u.vNombre as guestNombre, u.vApellido as guestApellido, u.vFoto as guestFoto, c.idUsuario, c.vRespuesta
                    FROM tbl_comentarios c
                    JOIN tbl_propiedad p ON c.idPropiedad = p.idPropiedad
                    JOIN tbl_usuarios u ON c.idUsuario = u.idUsuario
                    WHERE p.idUsuario = ?
                    UNION ALL
                    SELECT r.idResenia as id, 'resenia' as tipo, r.vComentario, r.iCalificacion, r.dtFechaResenia as fecha, p.vNombre as nombrePropiedad, u.vNombre as guestNombre, u.vApellido as guestApellido, u.vFoto as guestFoto, r.idUsuario, r.vRespuesta
                    FROM tbl_resenia r
                    JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad
                    JOIN tbl_usuarios u ON r.idUsuario = u.idUsuario
                    WHERE p.idUsuario = ?
               ) as t
               ORDER BY fecha DESC";
    $stmtCom = $conexion->prepare($sqlCom);
    $stmtCom->bind_param("ii", $idHost, $idHost);
    $stmtCom->execute();
    $result = $stmtCom->get_result();
    
    $comentarios = [];
    while ($row = $result->fetch_assoc()) {
        $comentarios[] = $row;
    }
    return $comentarios;
}

function getHostStats($idHost, $conexion) {
    $sqlAvg = "SELECT AVG(calif) as promedio, COUNT(*) as total FROM (
                    SELECT iCalificacion as calif FROM tbl_comentarios c JOIN tbl_propiedad p ON c.idPropiedad = p.idPropiedad WHERE p.idUsuario = ? AND iCalificacion > 0
                    UNION ALL
                    SELECT iCalificacion as calif FROM tbl_resenia r JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad WHERE p.idUsuario = ? AND iCalificacion > 0
               ) as t_avg";
    $stmtAvg = $conexion->prepare($sqlAvg);
    $stmtAvg->bind_param("ii", $idHost, $idHost);
    $stmtAvg->execute();
    $avgData = $stmtAvg->get_result()->fetch_assoc();
    return [
        'promedio' => round($avgData['promedio'] ?? 5.0, 1),
        'total' => $avgData['total'] ?? 0
    ];
}

function getPoliticasCancelacion($conexion) {
    $sqlPol = "SELECT vNombreOpcion, vDescripcion FROM tbl_politicas_reservas";
    $stmtPol = $conexion->query($sqlPol);
    $politicas = [];
    if ($stmtPol && $stmtPol->num_rows > 0) {
        while($rowPol = $stmtPol->fetch_assoc()) {
            $politicas[] = $rowPol;
        }
    }
    return $politicas;
}
?>
