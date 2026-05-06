<?php
require_once '../../datos/conexion.php';
require_once '../../negocio/utilidades/seguridad.php';

// 0. Validar CSRF
if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    $resultado = ['ok' => false, 'error' => 'Error de seguridad (CSRF).'];
    return;
}

// Protección inicial
if (!isset($id) || !isset($tipo) || !isset($respuesta)) {
    $resultado = ['ok' => false, 'error' => 'Acceso no permitido'];
    return;
}

// 1. Validar propiedad del comentario/reseña (IDOR Protection)
$tabla = ($tipo === 'comentario') ? 'tbl_comentarios' : 'tbl_resenia';
$colId = ($tipo === 'comentario') ? 'idComentario' : 'idResenia';

$sqlCheck = "SELECT p.idUsuario 
             FROM $tabla c 
             JOIN tbl_propiedad p ON c.idPropiedad = p.idPropiedad 
             WHERE c.$colId = ?";
$stmtCheck = $conexion->prepare($sqlCheck);
$stmtCheck->bind_param("i", $id);
$stmtCheck->execute();
$resCheck = $stmtCheck->get_result()->fetch_assoc();

if (!$resCheck) {
    http_response_code(404);
    $resultado = ['ok' => false, 'error' => 'Registro no encontrado.'];
    return;
}

if ((int)$resCheck['idUsuario'] !== (int)($_SESSION['idUsuario'] ?? 0)) {
    http_response_code(403);
    $resultado = ['ok' => false, 'error' => 'No tienes permiso para responder a esta reseña.'];
    return;
}

// Sanitizar entrada
if (esSospechoso($respuesta)) {
    $resultado = ['ok' => false, 'error' => 'Se detectó contenido malicioso en la respuesta.'];
    return;
}
$respuesta = sanitizarEntrada($respuesta);

$tiposPermitidos = ['comentario', 'resenia'];
if (!in_array($tipo, $tiposPermitidos, true)) {
    $resultado = ['ok' => false, 'error' => 'Tipo no válido'];
    return;
}

$sql = "UPDATE $tabla SET vRespuesta = ?, dtFechaRespuesta = NOW() WHERE $colId = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("si", $respuesta, $id);

if ($stmt->execute()) {
    $resultado = ['ok' => true];

    // NOTIFICACIÓN AL HUÉSPED (Opcional, no debe romper el flujo si falla)
    try {
        require_once __DIR__ . '/../../negocio/utilidades/notificaciones.php';

        // Determinar qué campos seleccionar dependiendo de la tabla
        $colReserva = ($tabla === 'tbl_comentarios') ? 'c.idReserva' : '0 as idReserva';

        // Obtener datos del comentario para saber a quién notificar
        $qData = $conexion->prepare("
            SELECT c.idUsuario as idHuesped, p.vNombre as vTitulo, c.idPropiedad, $colReserva
            FROM $tabla c
            JOIN tbl_propiedad p ON c.idPropiedad = p.idPropiedad
            WHERE c.$colId = ?
        ");
        $qData->bind_param("i", $id);
        $qData->execute();
        $notifData = $qData->get_result()->fetch_assoc();

        if ($notifData) {
            $msg = "El anfitrión respondió tu reseña en " . $notifData['vTitulo'];
            
            // Siempre mandamos al detalle de la propiedad
            $url = "presentacion/huesped/detalle.php?id=" . $notifData['idPropiedad'];
            
            registrarNotificacion(
                $notifData['idHuesped'],
                'respuesta_resena',
                'Respuesta del anfitrión',
                $msg,
                $url,
                $id
            );
        }
    } catch (Throwable $notifError) {
        error_log("Error al notificar respuesta: " . $notifError->getMessage());
    }
} else {
    $resultado = ['ok' => false, 'error' => 'Error al guardar la respuesta.'];
}
?>
