<?php
require 'datos/conexion.php';
$r = $conexion->query("DESCRIBE tbl_reserva");
while($f = $r->fetch_assoc()) { print_r($f); }
echo "========\n";
$r = $conexion->query("DESCRIBE tbl_disponibilidad_administrativa_propiedad");
while($f = $r->fetch_assoc()) { print_r($f); }
