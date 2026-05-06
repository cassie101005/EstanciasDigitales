<?php
require_once '../datos/conexion.php';
$res = $conexion->query('SELECT idEstadoReserva, vNombreEstado FROM cat_estadoreserva');
$data = [];
while($row = $res->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode($data);
?>
