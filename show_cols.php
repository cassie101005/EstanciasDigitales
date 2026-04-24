<?php
require_once 'datos/conexion.php';
$res = $conexion->query("SHOW COLUMNS FROM tbl_usuarios");
while ($row = $res->fetch_assoc()) {
    echo $row['Field'] . "\n";
}
?>
