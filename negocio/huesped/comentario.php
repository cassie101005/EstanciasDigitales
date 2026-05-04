<?php
require_once '../../datos/conexion.php';

// Protección
if (!isset($idReserva) || !isset($idPropiedad) || !isset($idUsuario)) {
    $resultado = ['ok' => false, 'mensaje' => 'Acceso no permitido'];
    return;
}

// Verificar si ya comentó para esta reserva
$check = $conexion->prepare("SELECT idComentario FROM tbl_comentarios WHERE idReserva = ?");
$check->bind_param("i", $idReserva);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    $resultado = ['ok' => false, 'mensaje' => 'Ya has dejado un comentario para esta reserva'];
    return;
}

$sql = "INSERT INTO tbl_comentarios (idReserva, idPropiedad, idUsuario, vComentario, iCalificacion) VALUES (?, ?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("iiisi", $idReserva, $idPropiedad, $idUsuario, $comentario, $calificacion);

if ($stmt->execute()) {
    $idComentario = $stmt->insert_id;
    $resultado = ['ok' => true, 'mensaje' => 'Comentario guardado con éxito'];

    // NOTIFICACIÓN AL ANFITRIÓN
    require_once '../../negocio/utilidades/notificaciones.php';
    
    // Obtener nombre del huésped
    $qG = $conexion->prepare("SELECT vNombre FROM tbl_usuarios WHERE idUsuario = ?");
    $qG->bind_param("i", $idUsuario);
    $qG->execute();
    $huespedNombre = $qG->get_result()->fetch_assoc()['vNombre'] ?? 'Un huésped';

    // Obtener datos de la propiedad y su dueño
    $qP = $conexion->prepare("SELECT p.vNombre as vTitulo, p.idUsuario as idAnfitrion FROM tbl_propiedad p WHERE p.idPropiedad = ?");
    $qP->bind_param("i", $idPropiedad);
    $qP->execute();
    $propData = $qP->get_result()->fetch_assoc();
    
    if ($propData) {
        $msg = "{$huespedNombre} dejó una reseña en " . $propData['vTitulo'];
        registrarNotificacion(
            $propData['idAnfitrion'], 
            'resena_recibida', 
            'Nueva reseña recibida', 
            $msg, 
            "presentacion/anfitrion/reservas.php#reseñas", 
            $idPropiedad
        );
    }
} else {
    $resultado = ['ok' => false, 'mensaje' => 'Error al guardar el comentario.'];
}
?>
