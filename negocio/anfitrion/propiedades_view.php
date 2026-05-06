<?php
require_once '../../datos/conexion.php';

function getHostIngresos($idHost, $conexion) {
    // Calcular ingresos totales: reservas confirmadas (1), en curso (2), finalizadas (3) + penalizaciones de canceladas
    $sqlIngresos = "SELECT 
                        (SELECT IFNULL(SUM(r.dTotalReserva), 0)
                         FROM tbl_reserva r 
                         JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad 
                         WHERE p.idUsuario = ? 
                         AND r.idEstadoReserva IN (1, 2, 3) 
                         AND TRIM(LOWER(r.vEstatus)) NOT IN ('cancelada', 'pendiente cancelacion'))
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
    }
    return 0;
}
?>
