<?php
require 'datos/conexion.php';
$result = $conexion->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    $table = $row[0];
    echo "Table: $table\n";
    $columns = $conexion->query("DESCRIBE $table");
    while ($col = $columns->fetch_assoc()) {
        echo "  " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
    echo "\n";
}
