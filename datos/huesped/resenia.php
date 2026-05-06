<?php
// Este archivo se encarga únicamente de recibir, limpiar y validar los datos enviados para una reseña

$idPropiedad = intval($_POST['idPropiedad'] ?? 0);
$comentario = htmlspecialchars(trim($_POST['vComentario'] ?? ''), ENT_QUOTES, 'UTF-8');
$calificacion = intval($_POST['iCalificacion'] ?? 0);

if ($idPropiedad <= 0 || empty($comentario) || $calificacion < 0 || $calificacion > 5) {
    echo json_encode(['ok' => false, 'error' => 'Datos incompletos o comentario vacío.']);
    exit;
}
?>
