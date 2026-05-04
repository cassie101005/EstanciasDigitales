<?php
require_once '../datos/conexion.php';
$res = $conexion->query("DESCRIBE tbl_reserva");
while ($row = $res->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>
