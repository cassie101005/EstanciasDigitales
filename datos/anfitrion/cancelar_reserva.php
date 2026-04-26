<?php
// Este archivo se encarga de extraer y validar los datos para la cancelación por parte del anfitrión

$idReserva = isset($_POST['idReserva']) ? intval($_POST['idReserva']) : 0;
$motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : 'Sin motivo especificado';

if ($idReserva <= 0) {
    echo json_encode(['ok' => false, 'mensaje' => 'ID de reserva inválido']);
    exit();
}
?>
