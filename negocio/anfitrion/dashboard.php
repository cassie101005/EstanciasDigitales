<?php
require_once '../../datos/conexion.php';

function getDashboardData($idHost, $conexion) {
    // Obtener las reservas recientes
    $sqlNotif = "SELECT r.idReserva, r.dtFechaInicio, r.dtFechaFin, r.vEstatus, r.dTotalReserva, u.vNombre, u.vApellido, u.vFoto, p.vNombre as propiedad
                 FROM tbl_reserva r 
                 JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad 
                 JOIN tbl_usuarios u ON r.idUsuario = u.idUsuario
                 WHERE p.idUsuario = ? 
                 ORDER BY r.idReserva DESC LIMIT 5";
    $stmtNotif = $conexion->prepare($sqlNotif);
    $notificaciones = [];
    if ($stmtNotif) {
        $stmtNotif->bind_param("i", $idHost);
        $stmtNotif->execute();
        $resNotif = $stmtNotif->get_result();
        while ($row = $resNotif->fetch_assoc()) {
            $notificaciones[] = $row;
        }
    }
    return $notificaciones;
}
?>
