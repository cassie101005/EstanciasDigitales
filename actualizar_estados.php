<?php
$mysqli = new mysqli('localhost', 'root', '', 'estancias_digitales');

// 1. Canceladas (basado en vEstatus o vEstado)
$mysqli->query("UPDATE tbl_reserva SET idEstadoReserva = 4 WHERE UPPER(vEstatus) LIKE 'CANCELADA%' OR UPPER(vEstatus) LIKE 'CANCELADO%'");

// 2. Pendiente de Cancelacion (podríamos dejarla en 1 o crear un 5, pero el usuario pidió 1-4. Usaremos 1 por ahora o 4?)
// El usuario no pidió ID para "Pendiente", así que lo dejaremos como está (probablemente 1).

// 3. Finalizadas
$mysqli->query("UPDATE tbl_reserva SET idEstadoReserva = 3 WHERE idEstadoReserva != 4 AND CURDATE() > dtFechaFin");

// 4. En curso
$mysqli->query("UPDATE tbl_reserva SET idEstadoReserva = 2 WHERE idEstadoReserva != 4 AND CURDATE() BETWEEN dtFechaInicio AND dtFechaFin");

// 5. Confirmadas (el resto)
$mysqli->query("UPDATE tbl_reserva SET idEstadoReserva = 1 WHERE idEstadoReserva NOT IN (2,3,4)");

echo "Estados actualizados: " . $mysqli->affected_rows;
?>
