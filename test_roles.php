<?php
require_once "datos/conexion.php";
$r = $conexion->query("SELECT * FROM tbl_roles_usuario");
while($row = $r->fetch_assoc()) {
    echo $row["idRol"] . " - " . $row["vNombreRol"] . "\n";
}
?>
