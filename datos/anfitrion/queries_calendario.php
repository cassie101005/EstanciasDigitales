<?php
/**
 * Queries para el módulo de calendario del anfitrión
 * Solo consultas SQL - sin lógica de negocio
 */

class QueriesCalendario {
    private $conexion;
    
    public function __construct($conexion) {
        $this->conexion = $conexion;
    }
    
    /**
     * Verificar que una propiedad pertenece a un usuario
     */
    public function verificarPropiedadUsuario($idPropiedad, $idUsuario) {
        $sql = "SELECT idPropiedad FROM tbl_propiedad WHERE idPropiedad = ? AND idUsuario = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $idPropiedad, $idUsuario);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    /**
     * Obtener eventos (reservas y bloqueos) de una propiedad
     */
    public function obtenerEventosPropiedad($idPropiedad, $anio, $mes) {
        $eventos = [];
        
        // Reservas
        $sqlRes = "SELECT r.idReserva, r.dtFechaInicio, r.dtFechaFin, r.dTotalReserva, u.vNombre as nombreHuesped
                   FROM tbl_reserva r
                   JOIN tbl_usuarios u ON u.idUsuario = r.idUsuario
                   WHERE r.idPropiedad = ? 
                   AND (YEAR(r.dtFechaInicio) = ? OR YEAR(r.dtFechaFin) = ?)
                   AND (MONTH(r.dtFechaInicio) = ? OR MONTH(r.dtFechaFin) = ?)";
        $stmtRes = $this->conexion->prepare($sqlRes);
        $stmtRes->bind_param("iiiii", $idPropiedad, $anio, $anio, $mes, $mes);
        $stmtRes->execute();
        $resRes = $stmtRes->get_result();
        
        while ($f = $resRes->fetch_assoc()) {
            $eventos[] = [
                'tipo' => 'reserva',
                'id' => $f['idReserva'],
                'inicio' => date('Y-m-d', strtotime($f['dtFechaInicio'])),
                'fin' => date('Y-m-d', strtotime($f['dtFechaFin'])),
                'nombre' => $f['nombreHuesped'],
                'total' => $f['dTotalReserva']
            ];
        }
        
        // Bloqueos
        $sqlBlq = "SELECT idDisponibilidad, dtFechaInicio, dtFechaFin, vMotivo
                   FROM tbl_disponibilidad_administrativa_propiedad
                   WHERE idPropiedad = ? AND bEstado = 1
                   AND (YEAR(dtFechaInicio) = ? OR YEAR(dtFechaFin) = ?)
                   AND (MONTH(dtFechaInicio) = ? OR MONTH(dtFechaFin) = ?)";
        $stmtBlq = $this->conexion->prepare($sqlBlq);
        $stmtBlq->bind_param("iiiii", $idPropiedad, $anio, $anio, $mes, $mes);
        $stmtBlq->execute();
        $resBlq = $stmtBlq->get_result();
        
        while ($f = $resBlq->fetch_assoc()) {
            $eventos[] = [
                'tipo' => 'bloqueo',
                'id' => $f['idDisponibilidad'],
                'inicio' => date('Y-m-d', strtotime($f['dtFechaInicio'])),
                'fin' => date('Y-m-d', strtotime($f['dtFechaFin'])),
                'motivo' => $f['vMotivo']
            ];
        }
        
        return $eventos;
    }
    
    /**
     * Validar solapamiento con reservas existentes
     */
    public function validarSolapamientoReservas($idPropiedad, $fechaInicio, $fechaFin) {
        $sql = "SELECT idReserva FROM tbl_reserva 
                WHERE idPropiedad = ? 
                AND dtFechaInicio <= ? AND dtFechaFin >= ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("iss", $idPropiedad, $fechaFin, $fechaInicio);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    /**
     * Insertar bloqueo de fechas
     */
    public function insertarBloqueo($idPropiedad, $fechaInicio, $fechaFin, $motivo) {
        $sql = "INSERT INTO tbl_disponibilidad_administrativa_propiedad (idPropiedad, dtFechaInicio, dtFechaFin, bEstado, vMotivo) VALUES (?, ?, ?, 1, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("isss", $idPropiedad, $fechaInicio, $fechaFin, $motivo);
        return $stmt->execute();
    }
    
    /**
     * Desbloquear fechas
     */
    public function desbloquearFechas($idDisponibilidad, $idPropiedad) {
        $sql = "UPDATE tbl_disponibilidad_administrativa_propiedad SET bEstado = 0 WHERE idDisponibilidad = ? AND idPropiedad = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $idDisponibilidad, $idPropiedad);
        return $stmt->execute();
    }
}
?>