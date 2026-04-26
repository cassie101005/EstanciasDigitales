<?php
// Este archivo se encarga de extraer y validar los datos para la gestión de propiedades del anfitrión

if ($accion === 'detalle') {
    $idPropiedad = intval($_GET['id'] ?? 0);
    if ($idPropiedad <= 0) {
        echo json_encode(['error' => 'ID inválido.']);
        exit;
    }
} else if ($accion !== 'listar') {
    echo json_encode(['error' => 'Acción no reconocida.']);
    exit;
}
?>
