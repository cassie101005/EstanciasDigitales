<?php
require_once 'datos/conexion.php';
$result = $conexion->query("SHOW COLUMNS FROM tbl_reserva");
while($row = $result->fetch_assoc()){
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>
