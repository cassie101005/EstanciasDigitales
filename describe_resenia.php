<?php
require_once 'datos/conexion.php';
$res = $conexion->query("DESCRIBE tbl_resenia");
$cols = [];
while($f = $res->fetch_assoc()) $cols[] = $f;
echo json_encode($cols);
?>
