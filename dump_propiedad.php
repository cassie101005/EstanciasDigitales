<?php
require_once 'datos/conexion.php';
$result = $conexion->query("SHOW COLUMNS FROM tbl_propiedad");
while($row = $result->fetch_assoc()){
    echo $row['Field'] . "\n";
}
echo "---\n";
$result2 = $conexion->query("SHOW COLUMNS FROM tbl_tipo_propiedad");
while($row = $result2->fetch_assoc()){
    echo $row['Field'] . "\n";
}
?>
