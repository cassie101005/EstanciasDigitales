<?php
require 'datos/conexion.php';
$result = $conexion->query("SELECT * FROM tbl_tipo_propiedad");
while ($row = $result->fetch_assoc()) {
    print_r($row);
}
?>
