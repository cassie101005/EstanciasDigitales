<?php
require_once 'datos/conexion.php';
$sql = "ALTER TABLE tbl_resenia ADD COLUMN dtFechaActualizacion DATETIME NULL AFTER dtFechaResenia";
if ($conexion->query($sql)) {
    echo "Columna dtFechaActualizacion añadida correctamente.\n";
} else {
    echo "Error: " . $conexion->error . "\n";
}
