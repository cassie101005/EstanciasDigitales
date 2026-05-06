<?php
class QueriesEdicionPropiedad {
    private $conexion;
    
    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function obtenerPropiedadPorId($idPropiedad, $idUsuario) {
        $sql = "SELECT p.*, c.idEstado, e.idPais 
                FROM tbl_propiedad p
                LEFT JOIN tbl_ciudad c ON p.idCiudad = c.idCiudad
                LEFT JOIN tbl_estado e ON c.idEstado = e.idEstado
                WHERE p.idPropiedad = ? AND p.idUsuario = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $idPropiedad, $idUsuario);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function obtenerImagenes($idPropiedad) {
        $sql = "SELECT idImagen, vImagen FROM tbl_imagen_propiedad WHERE idPropiedad = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idPropiedad);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function obtenerServiciosSeleccionados($idPropiedad) {
        $sql = "SELECT idServicio FROM tbl_propiedad_servicios WHERE idPropiedad = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idPropiedad);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function obtenerReglasSeleccionadas($idPropiedad) {
        $sql = "SELECT idRegla FROM tbl_propiedad_regla WHERE idPropiedad = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idPropiedad);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function obtenerPoliticasSeleccionadas($idPropiedad) {
        $sql = "SELECT idPolitica FROM tbl_propiedad_politica WHERE idPropiedad = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idPropiedad);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function actualizarPropiedad($datos, $idUsuario) {
        $sql = "UPDATE tbl_propiedad SET 
                    vNombre             = ?, 
                    idTipoPropiedad     = ?, 
                    dPrecioNoche        = ?, 
                    dTarifaLimpieza     = ?, 
                    iCapacidadHuespedes = ?, 
                    iNumeroHabitaciones = ?, 
                    iNumeroBanos        = ?,
                    idCiudad            = ?, 
                    vDireccion          = ?, 
                    vDescripcion        = ?
                WHERE idPropiedad = ? AND idUsuario = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param(
            "siddiiiissii",
            $datos['nombre'],
            $datos['idTipoPropiedad'],
            $datos['precioNoche'],
            $datos['tarifaLimpieza'],
            $datos['capacidadHuespedes'],
            $datos['numeroHabitaciones'],
            $datos['numeroBanos'],
            $datos['idCiudad'],
            $datos['direccion'],
            $datos['descripcion'],
            $datos['idPropiedad'],
            $idUsuario
        );
        if ($stmt->execute()) {
            return $stmt->affected_rows;
        }
        return -1;
    }

    public function limpiarRelaciones($idPropiedad) {
        $stmt1 = $this->conexion->prepare("DELETE FROM tbl_propiedad_servicios WHERE idPropiedad = ?");
        $stmt1->bind_param("i", $idPropiedad);
        $stmt1->execute();
        
        $stmt2 = $this->conexion->prepare("DELETE FROM tbl_propiedad_regla WHERE idPropiedad = ?");
        $stmt2->bind_param("i", $idPropiedad);
        $stmt2->execute();
        
        $stmt3 = $this->conexion->prepare("DELETE FROM tbl_propiedad_politica WHERE idPropiedad = ?");
        $stmt3->bind_param("i", $idPropiedad);
        $stmt3->execute();
    }

    public function eliminarImagen($idImagen, $idPropiedad) {
        // Primero obtener la ruta para borrar el archivo si es necesario (opcional)
        $sql = "DELETE FROM tbl_imagen_propiedad WHERE idImagen = ? AND idPropiedad = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $idImagen, $idPropiedad);
        return $stmt->execute();
    }
}
?>
