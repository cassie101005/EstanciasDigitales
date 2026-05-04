<?php
// Este archivo se encarga de extraer y validar los datos para el calendario del anfitrión

if ($accion === 'obtener_eventos') {
    $idPropiedad = intval($_GET['idPropiedad'] ?? 0);
    $mes = intval($_GET['mes'] ?? date('n'));
    $anio = intval($_GET['anio'] ?? date('Y'));

    if ($idPropiedad <= 0) {
        echo json_encode(['error' => 'ID Propiedad inválido']);
        exit;
    }
} else if ($accion === 'bloquear_fechas') {
    $idPropiedad = intval($_POST['idPropiedad'] ?? 0);
    $fechaInicio = $_POST['fechaInicio'] ?? '';
    $fechaFin = $_POST['fechaFin'] ?? '';
    $motivo = trim($_POST['motivo'] ?? 'Bloqueo manual');

    if ($idPropiedad <= 0 || empty($fechaInicio) || empty($fechaFin)) {
        echo json_encode(['error' => 'Datos incompletos']);
        exit;
    }

    if ($fechaInicio > $fechaFin) {
        echo json_encode(['error' => 'La fecha de inicio debe ser anterior a la de fin.']);
        exit;
    }
} else if ($accion === 'ajustar_tarifa') {
    $idPropiedad = intval($_POST['idPropiedad'] ?? 0);
    $fechaInicio = $_POST['fechaInicio'] ?? '';
    $fechaFin = $_POST['fechaFin'] ?? '';
    
    $precioRaw = trim($_POST['precio'] ?? '');
    if ($precioRaw === '' || !is_numeric($precioRaw) || floatval($precioRaw) <= 0) {
        echo json_encode(['error' => 'La tarifa debe ser un número válido mayor a 0 y no puede contener letras o caracteres especiales.']);
        exit;
    }
    $precio = filter_var($precioRaw, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $precio = floatval($precio);

    if ($idPropiedad <= 0 || empty($fechaInicio) || empty($fechaFin)) {
        echo json_encode(['error' => 'Datos incompletos para el ajuste de tarifa.']);
        exit;
    }

    if ($fechaInicio > $fechaFin) {
        echo json_encode(['error' => 'La fecha de inicio debe ser anterior a la de fin.']);
        exit;
    }
} else if ($accion === 'desbloquear') {
    $idDisponibilidad = intval($_POST['idDisponibilidad'] ?? 0);
    $idPropiedad = intval($_POST['idPropiedad'] ?? 0);
}
?>
