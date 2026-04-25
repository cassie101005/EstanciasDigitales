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
            return ['ok' => true, 'mensaje' => 'Comentario guardado correctamente.'];
        }

        return ['ok' => false, 'error' => 'Error al guardar el comentario: ' . $this->conexion->error];
    }
}
?>
