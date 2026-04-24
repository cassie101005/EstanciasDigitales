<?php
require_once 'datos/conexion.php';
$sqlCrearTabla = "CREATE TABLE IF NOT EXISTS tbl_cancelacion (
    idCancelacion INT AUTO_INCREMENT PRIMARY KEY,
    idReserva INT NOT NULL,
    idUsuario INT NOT NULL,
    dtFechaCancelacion DATETIME DEFAULT CURRENT_TIMESTAMP
)";
if (!$conexion->query($sqlCrearTabla)) {
    echo "Error creando tabla: " . $conexion->error . "\n";
} else {
    echo "Tabla creada OK.\n";
}
?>
