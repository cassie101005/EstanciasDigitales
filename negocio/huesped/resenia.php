<?php
class ReseniaNegocio {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function getReviewsCount($idUsuario, $idPropiedad = null) {
        $sql = "SELECT COUNT(*) as total FROM tbl_resenia WHERE idUsuario = ?";
        if ($idPropiedad) {
            $sql .= " AND idPropiedad = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("ii", $idUsuario, $idPropiedad);
        } else {
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $idUsuario);
        }
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return $res['total'];
    }

    public function guardarResenia($idPropiedad, $idUsuario, $calificacion, $comentario) {
        require_once '../../negocio/utilidades/seguridad.php';
        
        // 0. Sanitizar y validar patrones maliciosos
        if (esSospechoso($comentario)) {
            return ['ok' => false, 'error' => 'Se detectó contenido malicioso en el comentario.'];
        }
        $comentario = sanitizarEntrada($comentario);

        // 1. Verificar límite de 3 comentarios por huésped EN ESTA PROPIEDAD
        if ($this->getReviewsCount($idUsuario, $idPropiedad) >= 3) {
            return ['ok' => false, 'error' => 'Has alcanzado el límite máximo de 3 comentarios para esta propiedad.'];
        }

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

    public function actualizarResenia($idResenia, $idUsuario, $comentario) {
        require_once '../../negocio/utilidades/seguridad.php';
        
        if (esSospechoso($comentario)) {
            return ['ok' => false, 'error' => 'Se detectó contenido malicioso en el comentario.'];
        }
        $comentario = sanitizarEntrada($comentario);

        // Verificar propiedad del comentario
        $sqlCheck = "SELECT idResenia FROM tbl_resenia WHERE idResenia = ? AND idUsuario = ? LIMIT 1";
        $stmtCheck = $this->conexion->prepare($sqlCheck);
        $stmtCheck->bind_param("ii", $idResenia, $idUsuario);
        $stmtCheck->execute();
        if ($stmtCheck->get_result()->num_rows === 0) {
            return ['ok' => false, 'error' => 'No tienes permiso para editar este comentario.'];
        }

        $sql = "UPDATE tbl_resenia SET vComentario = ?, dtFechaActualizacion = NOW() WHERE idResenia = ? AND idUsuario = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("sii", $comentario, $idResenia, $idUsuario);

        if ($stmt->execute()) {
            return ['ok' => true, 'mensaje' => 'Comentario actualizado correctamente.'];
        }

        return ['ok' => false, 'error' => 'Error al actualizar: ' . $this->conexion->error];
    }
}
?>
