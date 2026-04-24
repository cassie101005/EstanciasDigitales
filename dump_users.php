<?php
require_once 'datos/conexion.php';
$res = $conexion->query("SELECT idUsuario, vNombre, vCorreo FROM tbl_usuarios");
while($row = $res->fetch_assoc()){
    echo $row['idUsuario'] . " - " . $row['vNombre'] . " (" . $row['vCorreo'] . ")\n";
}
?>
