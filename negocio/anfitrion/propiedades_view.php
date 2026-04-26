<?php
require_once '../../datos/conexion.php';

function getHostIngresos($idHost, $conexion) {
    // Calcular ingresos totales omitiendo las reservas canceladas
    $sqlIngresos = "SELECT SUM(r.dTotalReserva) as totalIngresos 
                    FROM tbl_reserva r 
                    JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad 
                    WHERE p.idUsuario = ? 
                    AND (r.vEstatus IS NULL OR (UPPER(r.vEstatus) != 'CANCELADA' AND UPPER(r.vEstatus) != 'CANCELADO'))";
    $stmtIng = $conexion->prepare($sqlIngresos);
    if ($stmtIng) {
        $stmtIng->bind_param("i", $idHost);
        $stmtIng->execute();
        $resIng = $stmtIng->get_result()->fetch_assoc();
        return $resIng['totalIngresos'] ?? 0;
    } else {
        // Fallback for different column name
        $sqlIngresos2 = "SELECT SUM(r.dTotalReserva) as totalIngresos 
                         FROM tbl_reserva r 
                         JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad 
                         WHERE p.idUsuario = ? 
                         AND (r.vEstado IS NULL OR (UPPER(r.vEstado) != 'CANCELADA' AND UPPER(r.vEstado) != 'CANCELADO'))";
        $stmtIng2 = $conexion->prepare($sqlIngresos2);
        if ($stmtIng2) {
            $stmtIng2->bind_param("i", $idHost);
            $stmtIng2->execute();
            $resIng2 = $stmtIng2->get_result()->fetch_assoc();
            return $resIng2['totalIngresos'] ?? 0;
        }
    }
    return 0;
}
?>
