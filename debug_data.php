<?php
require_once 'datos/conexion.php';
$res = $conexion->query("SELECT * FROM tbl_propiedad LIMIT 5");
while($f = $res->fetch_assoc()) {
    print_r($f);
}
?>
