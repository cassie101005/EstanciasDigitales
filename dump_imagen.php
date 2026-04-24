<?php
require_once 'datos/conexion.php';
$result = $conexion->query("SHOW COLUMNS FROM tbl_imagen_propiedad");
while($row = $result->fetch_assoc()){
    echo $row['Field'] . "\n";
}
?>
