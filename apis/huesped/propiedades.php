<?php
header('Content-Type: application/json');
require_once '../../negocio/auth/verificar_sesion.php';
validarSesionAPI('huesped');
require_once '../../datos/conexion.php';
require_once '../../negocio/huesped/home_view.php';

$ubicacion          = trim($_GET['ubicacion']          ?? '');
$huespedes          = intval($_GET['huespedes']         ?? 0);
$fechaInicio        = trim($_GET['fecha_inicio']        ?? '');
$fechaFin           = trim($_GET['fecha_fin']           ?? '');
$categoriaSeleccionada = trim($_GET['categoria']        ?? '');

// Validar fechas si vienen
if ($fechaInicio !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaInicio)) {
    echo json_encode(['ok' => false, 'error' => 'Formato de fecha_inicio inválido.']);
    exit;
}
if ($fechaFin !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaFin)) {
    echo json_encode(['ok' => false, 'error' => 'Formato de fecha_fin inválido.']);
    exit;
}
if ($fechaInicio !== '' && $fechaFin !== '' && $fechaInicio >= $fechaFin) {
    echo json_encode(['ok' => false, 'error' => 'La fecha de inicio debe ser menor a la fecha de fin.']);
    exit;
}

$properties = getHomeProperties($ubicacion, $huespedes, $fechaInicio, $fechaFin, $categoriaSeleccionada, $conexion);

echo json_encode(['ok' => true, 'properties' => $properties]);
?>
