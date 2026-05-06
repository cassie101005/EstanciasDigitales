<?php
// Este archivo se encarga de extraer y validar los datos para la gestión de propiedades del anfitrión

if ($accion === 'detalle') {
    $idPropiedad = intval($_GET['id'] ?? 0);
    if ($idPropiedad <= 0) {
        echo json_encode(['error' => 'ID inválido.']);
        exit;
    }
} else if ($accion === 'eliminar') {
    $idPropiedad = intval($_GET['id'] ?? 0);
    if ($idPropiedad <= 0) {
        echo json_encode(['error' => 'ID inválido para eliminar.']);
        exit;
    }
} else if ($accion !== 'listar' && $accion !== 'listar_tipos' && $accion !== 'ingresos') {
    echo json_encode(['error' => 'Acción no reconocida: ' . $accion]);
    exit;
}
?>
