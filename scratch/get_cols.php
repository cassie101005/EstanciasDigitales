<?php
require 'datos/conexion.php';
$result = $conexion->query("DESCRIBE tbl_usuarios");
$cols = [];
while ($row = $result->fetch_assoc()) {
    $cols[] = $row['Field'];
}
echo implode(", ", $cols);
?>
