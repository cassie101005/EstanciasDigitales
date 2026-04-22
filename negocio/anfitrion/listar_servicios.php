<?php
require_once '../../datos/conexion.php';

// 1. Consultar servicios activos
$sql = "SELECT idServicio, vNombreServicio, vDescripcion
        FROM tbl_servicios_extra
        WHERE bEstado = 1
        ORDER BY vNombreServicio ASC";

$consulta = $conexion->query($sql);

// 2. Preparar arreglo de resultados
$servicios = [];

if ($consulta->num_rows > 0) {
    while ($fila = $consulta->fetch_assoc()) {
        $servicios[] = $fila;
    }

    $resultado = [
        'ok' => true,
        'servicios' => $servicios
    ];
} else {
    $resultado = [
        'ok' => false,
        'mensaje' => 'No hay servicios registrados.'
    ];
}x  