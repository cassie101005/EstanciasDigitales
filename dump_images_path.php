<?php
require_once 'datos/conexion.php';
$res = $conexion->query("SELECT vImagen FROM tbl_imagen_propiedad LIMIT 5");
while($row = $res->fetch_assoc()){
    echo $row['vImagen'] . "\n";
}
?>
