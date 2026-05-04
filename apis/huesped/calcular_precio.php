<?php
/**
 * API para calcular el precio de una estancia
 * apis/huesped/calcular_precio.php
 */
ob_start(); // Evita que warnings/notices contaminen la respuesta JSON
header('Content-Type: application/json');
require_once '../../datos/conexion.php';
require_once '../../negocio/utilidades/calculadora_precios.php';

$idPropiedad = intval($_GET['idPropiedad'] ?? 0);
$fechaInicio = trim($_GET['fechaInicio'] ?? '');
$fechaFin    = trim($_GET['fechaFin']    ?? '');

if ($idPropiedad > 0 && !empty($fechaInicio) && !empty($fechaFin)) {
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaInicio) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaFin)) {
        ob_end_clean();
        echo json_encode(['ok' => false, 'mensaje' => 'Formato de fecha inválido.']);
        exit;
    }

    $hoy = date('Y-m-d');
    if ($fechaInicio < $hoy) {
        ob_end_clean();
        echo json_encode(['ok' => false, 'mensaje' => 'No se permiten fechas pasadas.']);
        exit;
    }

    if ($fechaInicio >= $fechaFin) {
        ob_end_clean();
        echo json_encode(['ok' => false, 'mensaje' => 'La fecha de salida debe ser posterior a la de entrada.']);
        exit;
    }

    $desglose   = calcularPrecioEstancia($idPropiedad, $fechaInicio, $fechaFin, $conexion);
    $disponible = validarDisponibilidad($idPropiedad, $fechaInicio, $fechaFin, $conexion);

    ob_end_clean();
    echo json_encode([
        'ok'        => true,
        'disponible' => $disponible,
        'desglose'  => $desglose
    ]);
} else {
    ob_end_clean();
    echo json_encode(['ok' => false, 'mensaje' => 'Parámetros incompletos.']);
}
