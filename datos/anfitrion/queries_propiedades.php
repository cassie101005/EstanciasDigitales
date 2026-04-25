<?php
/**
 * Queries para el módulo de propiedades del anfitrión
 * Solo consultas SQL - sin lógica de negocio
 */

class QueriesPropiedades {
    private $conexion;
    
    public function __construct($conexion) {
        $this->conexion = $conexion;
    }
    
    /**
     * Obtener propiedades de un usuario
     */
    public function obtenerPropiedadesUsuario($idUsuario) {
        $sql = "SELECT
                    p.idPropiedad,
                    p.vNombre,
                    p.dPrecioNoche,
                    p.iCapacidadHuespedes,
                    p.iNumeroHabitaciones,
                    p.vDescripcion,
                    p.dtFechaRegistro,
                    tp.vNombreCategoria  AS tipo,
                    ci.vNombreCiudad     AS ciudad,
                    es.vNombreEstado     AS estado,
                    pa.vNombrePais       AS pais,
                    (SELECT vImagen FROM tbl_imagen_propiedad
                     WHERE idPropiedad = p.idPropiedad
                     ORDER BY idImagen ASC LIMIT 1) AS imagenPrincipal
                FROM tbl_propiedad p
                LEFT JOIN tbl_tipo_propiedad  tp ON tp.idTipoPropiedad = p.idTipoPropiedad
                LEFT JOIN tbl_ciudad          ci ON ci.idCiudad        = p.idCiudad
                LEFT JOIN tbl_estado          es ON es.idEstado        = ci.idEstado
                LEFT JOIN tbl_pais            pa ON pa.idPais          = es.idPais
                WHERE p.idUsuario = ?
                ORDER BY p.dtFechaRegistro DESC";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    /**
     * Obtener detalle de una propiedad
     */
    public function obtenerDetallePropiedad($idPropiedad, $idUsuario) {
        $sql = "SELECT
                    p.*,
                    tp.vNombreCategoria  AS tipo,
                    ci.vNombreCiudad     AS ciudad,
                    es.vNombreEstado     AS estado,
                    pa.vNombrePais       AS pais
                FROM tbl_propiedad p
                LEFT JOIN tbl_tipo_propiedad  tp ON tp.idTipoPropiedad = p.idTipoPropiedad
                LEFT JOIN tbl_ciudad          ci ON ci.idCiudad        = p.idCiudad
                LEFT JOIN tbl_estado          es ON es.idEstado        = ci.idEstado
                LEFT JOIN tbl_pais            pa ON pa.idPais          = es.idPais
                WHERE p.idPropiedad = ? AND p.idUsuario = ?
                LIMIT 1";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $idPropiedad, $idUsuario);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    /**
     * Obtener imágenes de una propiedad
     */
    public function obtenerImagenesPropiedad($idPropiedad) {
        $sql = "SELECT vImagen FROM tbl_imagen_propiedad WHERE idPropiedad = ? ORDER BY idImagen ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idPropiedad);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    /**
     * Obtener servicios de una propiedad
     */
    public function obtenerServiciosPropiedad($idPropiedad) {
        $sql = "SELECT se.vNombreServicio
                FROM tbl_propiedad_servicios ps
                JOIN tbl_servicios_extra se ON se.idServicio = ps.idServicio
                WHERE ps.idPropiedad = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idPropiedad);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    /**
     * Obtener reglas de una propiedad
     */
    public function obtenerReglasPropiedad($idPropiedad) {
        $sql = "SELECT r.vNombreRegla
               FROM tbl_propiedad_regla pr
               JOIN tbl_reglas r ON r.idRegla = pr.idRegla
               WHERE pr.idPropiedad = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idPropiedad);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    /**
     * Obtener políticas de una propiedad
     */
    public function obtenerPoliticasPropiedad($idPropiedad) {
        $sql = "SELECT pol.vNombrePol
               FROM tbl_propiedad_politica pp
               JOIN tbl_politicas pol ON pol.idPolitica = pp.idPolitica
               WHERE pp.idPropiedad = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idPropiedad);
        $stmt->execute();
        return $stmt->get_result();
    }
    /**
     * Obtener reseñas de una propiedad
     */
    public function obtenerReseniasPropiedad($idPropiedad) {
        $sql = "SELECT r.*, u.vNombre, u.vApellido, u.vFoto
                FROM tbl_resenia r
                JOIN tbl_usuarios u ON u.idUsuario = r.idUsuario
                WHERE r.idPropiedad = ?
                ORDER BY r.dtFechaResenia DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idPropiedad);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>