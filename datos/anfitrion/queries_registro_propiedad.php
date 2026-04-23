<?php
/**
 * Queries para el registro de propiedades
 * Solo consultas SQL - sin lógica de negocio
 */

class QueriesRegistroPropiedad {
    private $conexion;
    
    public function __construct($conexion) {
        $this->conexion = $conexion;
    }
    
    /**
     * Obtener tipos de propiedad
     */
    public function obtenerTiposPropiedad() {
        $sql = "SELECT MIN(idTipoPropiedad) AS idTipoPropiedad, vNombreCategoria
                FROM tbl_tipo_propiedad
                WHERE bEstado = 1
                GROUP BY vNombreCategoria
                ORDER BY vNombreCategoria ASC";
        return $this->conexion->query($sql);
    }
    
    /**
     * Obtener países
     */
    public function obtenerPaises() {
        $sql = "SELECT idPais, vNombrePais FROM tbl_pais ORDER BY vNombrePais ASC";
        return $this->conexion->query($sql);
    }
    
    /**
     * Obtener estados por país
     */
    public function obtenerEstadosPorPais($idPais) {
        $sql = "SELECT idEstado, vNombreEstado FROM tbl_estado WHERE idPais = ? ORDER BY vNombreEstado ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idPais);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    /**
     * Obtener ciudades por estado
     */
    public function obtenerCiudadesPorEstado($idEstado) {
        $sql = "SELECT idCiudad, vNombreCiudad FROM tbl_ciudad WHERE idEstado = ? ORDER BY vNombreCiudad ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idEstado);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    /**
     * Obtener servicios disponibles
     */
    public function obtenerServicios() {
        $sql = "SELECT idServicio, vNombreServicio
                FROM tbl_servicios_extra
                WHERE bEstado = 1
                ORDER BY vNombreServicio ASC";
        return $this->conexion->query($sql);
    }
    
    /**
     * Obtener reglas disponibles
     */
    public function obtenerReglas() {
        $sql = "SELECT idRegla, vNombreRegla FROM tbl_reglas ORDER BY vNombreRegla ASC";
        return $this->conexion->query($sql);
    }
    
    /**
     * Obtener políticas disponibles
     */
    public function obtenerPoliticas() {
        $sql = "SELECT idPolitica, vNombrePol FROM tbl_politicas ORDER BY vNombrePol ASC";
        return $this->conexion->query($sql);
    }
    
    /**
     * Insertar nueva propiedad
     */
    public function insertarPropiedad($datos) {
        $sql = "INSERT INTO tbl_propiedad (
                    idCiudad,
                    idUsuario,
                    idTipoPropiedad,
                    vNombre,
                    vDireccion,
                    dPrecioNoche,
                    vDescripcion,
                    vEspecificaciones,
                    iCapacidadHuespedes,
                    iNumeroHabitaciones
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param(
            "iiisssssii",
            $datos['idCiudad'],
            $datos['idUsuario'],
            $datos['idTipoPropiedad'],
            $datos['nombre'],
            $datos['direccion'],
            $datos['precioNoche'],
            $datos['descripcion'],
            $datos['especificaciones'],
            $datos['capacidadHuespedes'],
            $datos['numeroHabitaciones']
        );
        
        return $stmt->execute();
    }
    
    /**
     * Insertar servicio para propiedad
     */
    public function insertarServicioPropiedad($idServicio, $idPropiedad) {
        $sql = "INSERT INTO tbl_propiedad_servicios (idServicio, idPropiedad) VALUES (?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $idServicio, $idPropiedad);
        return $stmt->execute();
    }
    
    /**
     * Insertar regla para propiedad
     */
    public function insertarReglaPropiedad($idRegla, $idPropiedad) {
        $sql = "INSERT INTO tbl_propiedad_regla (idPropiedad, idRegla) VALUES (?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $idPropiedad, $idRegla);
        return $stmt->execute();
    }
    
    /**
     * Insertar política para propiedad
     */
    public function insertarPoliticaPropiedad($idPolitica, $idPropiedad) {
        $sql = "INSERT INTO tbl_propiedad_politica (idPolitica, idPropiedad) VALUES (?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $idPolitica, $idPropiedad);
        return $stmt->execute();
    }
    
    /**
     * Insertar imagen de propiedad
     */
    public function insertarImagenPropiedad($idPropiedad, $rutaImagen) {
        $sql = "INSERT INTO tbl_imagen_propiedad (idPropiedad, vImagen) VALUES (?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("is", $idPropiedad, $rutaImagen);
        return $stmt->execute();
    }
    
    /**
     * Insertar regla personalizada
     */
    public function insertarReglaPersonalizada($nombreRegla) {
        $sql = "INSERT INTO tbl_reglas (idClasificacionRegla, vNombreRegla, vDescripcion, vEstado) VALUES (1, ?, '', 'Activo')";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("s", $nombreRegla);
        return $stmt->execute();
    }
}
?>