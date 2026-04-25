<?php
require_once 'datos/conexion.php';
$res = $conexion->query("SHOW TABLES LIKE 'tbl_resenia'");
echo $res->num_rows > 0 ? "EXISTS" : "NOT_EXISTS";
?>
