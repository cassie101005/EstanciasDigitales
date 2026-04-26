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
} else if ($accion === 'desbloquear') {
    $idDisponibilidad = intval($_POST['idDisponibilidad'] ?? 0);
    $idPropiedad = intval($_POST['idPropiedad'] ?? 0);
}
?>
