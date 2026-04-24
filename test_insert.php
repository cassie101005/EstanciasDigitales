<?php
require_once 'datos/conexion.php';
$idReserva = 3;
$idUsuario = 4;
$sqlCancel = "INSERT INTO tbl_cancelacion (idReserva, idUsuario) VALUES (?, ?)";
$stmtCancel = $conexion->prepare($sqlCancel);
if ($stmtCancel) {
    $stmtCancel->bind_param("ii", $idReserva, $idUsuario);
    if (!$stmtCancel->execute()) {
        echo "Error insertando: " . $stmtCancel->error . "\n";
    } else {
        echo "Insertado OK.\n";
    }
} else {
    echo "Error prepare: " . $conexion->error . "\n";
}
?>
