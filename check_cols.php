<?php
require_once 'datos/conexion.php';
$res = $conexion->query("DESCRIBE tbl_propiedad");
$cols = [];
while($f = $res->fetch_assoc()) $cols[] = $f['Field'];
echo json_encode($cols);
?>
