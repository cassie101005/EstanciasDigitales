<?php
require_once 'datos/conexion.php';
$res = $conexion->query("SHOW TABLES");
while ($row = $res->fetch_array()) {
    echo $row[0] . "\n";
}
?>
