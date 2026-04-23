<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['idUsuario'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$idUsuario = intval($_SESSION['idUsuario']);
$accion = $_REQUEST['accion'] ?? '';

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
} else {
    echo json_encode(['error' => 'Accion no valida']);
    exit;
}

require_once '../../negocio/anfitrion/calendario.php';

if (isset($resultado)) {
    echo json_encode($resultado);
} else {
    echo json_encode(['error' => 'Error en la lógica de negocio.']);
    http_response_code(400);
}

