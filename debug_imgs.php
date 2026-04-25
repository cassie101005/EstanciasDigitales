<?php
require_once 'datos/conexion.php';
$res = $conexion->query("SELECT * FROM tbl_imagen_propiedad LIMIT 10");
while($f = $res->fetch_assoc()) {
    print_r($f);
}
?>
