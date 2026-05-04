<?php
require_once '../../datos/conexion.php';

function getHostIngresos($idHost, $conexion) {
    // Calcular ingresos totales: reservas no canceladas + penalizaciones de canceladas
    $sqlIngresos = "SELECT 
                        (SELECT IFNULL(SUM(r.dTotalReserva), 0)
                         FROM tbl_reserva r 
                         JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad 
                         WHERE p.idUsuario = ? 
                         AND (r.vEstatus IS NULL OR (UPPER(r.vEstatus) != 'CANCELADA' AND UPPER(r.vEstatus) != 'CANCELADO')))
                        +
                        (SELECT IFNULL(SUM(c.dPenalizacion), 0)
                         FROM tbl_cancelacion c
                         JOIN tbl_reserva r ON c.idReserva = r.idReserva
                         JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad
                         WHERE p.idUsuario = ?) as totalIngresos";
    $stmtIng = $conexion->prepare($sqlIngresos);
    if ($stmtIng) {
        $stmtIng->bind_param("ii", $idHost, $idHost);
        $stmtIng->execute();
        $resIng = $stmtIng->get_result()->fetch_assoc();
        return floatval($resIng['totalIngresos'] ?? 0);
    } else {
        // Fallback for different column name
        $sqlIngresos2 = "SELECT 
                            (SELECT IFNULL(SUM(r.dTotalReserva), 0)
                             FROM tbl_reserva r 
                             JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad 
                             WHERE p.idUsuario = ? 
                             AND (r.vEstado IS NULL OR (UPPER(r.vEstado) != 'CANCELADA' AND UPPER(r.vEstado) != 'CANCELADO')))
                            +
                            (SELECT IFNULL(SUM(c.dPenalizacion), 0)
                             FROM tbl_cancelacion c
                             JOIN tbl_reserva r ON c.idReserva = r.idReserva
                             JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad
                             WHERE p.idUsuario = ?) as totalIngresos";
        $stmtIng2 = $conexion->prepare($sqlIngresos2);
        if ($stmtIng2) {
            $stmtIng2->bind_param("ii", $idHost, $idHost);
            $stmtIng2->execute();
            $resIng2 = $stmtIng2->get_result()->fetch_assoc();
            return floatval($resIng2['totalIngresos'] ?? 0);
        }
    }
    return 0;
}
?>
