<?php
class ReseniaNegocio {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function guardarResenia($idPropiedad, $idUsuario, $calificacion, $comentario) {
        // Verificar si el usuario ya ha calificado (iCalificacion > 0) esta propiedad
        $checkSql = "SELECT idResenia FROM tbl_resenia WHERE idPropiedad = ? AND idUsuario = ? AND iCalificacion > 0 LIMIT 1";
        $stmtCheck = $this->conexion->prepare($checkSql);
        $stmtCheck->bind_param("ii", $idPropiedad, $idUsuario);
        $stmtCheck->execute();
        $resCheck = $stmtCheck->get_result();

        // Siempre insertamos un nuevo comentario (no modificamos el anterior)
        // Pero si ya llegó al límite de calificación (1), forzamos null en este nuevo registro
        $califFinal = $calificacion;
        if ($resCheck->num_rows > 0) {
            $califFinal = null;
        } else {
            // Si es la primera vez pero no seleccionó estrellas, también es null
            if ($califFinal <= 0) $califFinal = null;
        }

        $sql = "INSERT INTO tbl_resenia (idPropiedad, idUsuario, iCalificacion, vComentario, dtFechaResenia) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("iiis", $idPropiedad, $idUsuario, $califFinal, $comentario);

        if ($stmt->execute()) {
            $idResenia = $this->conexion->insert_id;
            
            // ── NOTIFICACIÓN AL ANFITRIÓN ──
            require_once '../../negocio/utilidades/notificaciones.php';
            $sqlHost = "SELECT p.idUsuario as idAnfitrion, p.vNombre as nombreProp, u.vNombre as guestNombre, u.vApellido as guestApellido 
                        FROM tbl_propiedad p 
                        JOIN tbl_usuarios u ON u.idUsuario = ? 
                        WHERE p.idPropiedad = ?";
            $qHost = $this->conexion->prepare($sqlHost);
            $qHost->bind_param("ii", $idUsuario, $idPropiedad);
            $qHost->execute();
            $hostData = $qHost->get_result()->fetch_assoc();
            
            if ($hostData) {
                $idAnfitrion = $hostData['idAnfitrion'];
                $nombreProp  = $hostData['nombreProp'];
                $nombreHuesped = $hostData['guestNombre'] . ' ' . $hostData['guestApellido'];
                
                $tituloHost = "Nueva reseña recibida";
                $mensajeHost = $nombreHuesped . " ha dejado un comentario en '" . $nombreProp . "'.";
                $urlHost = "presentacion/anfitrion/reservas.php#reseñas";
                
                registrarNotificacion($idAnfitrion, 'resena_recibida', $tituloHost, $mensajeHost, $urlHost, $idResenia);
            }

            return ['ok' => true, 'mensaje' => 'Comentario guardado correctamente.'];
        }

        return ['ok' => false, 'error' => 'Error al guardar el comentario: ' . $this->conexion->error];
    }
}
?>
