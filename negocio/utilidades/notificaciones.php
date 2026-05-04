<?php
/**
 * Utilidad para el registro de notificaciones en el sistema
 */

if (!function_exists('registrarNotificacion')) {
    function registrarNotificacion($idUsuario, $tipo, $titulo, $mensaje, $url, $idReferencia = 0) {
        global $conexion;
        
        if (!isset($conexion)) {
            $path_conexion = __DIR__ . '/../../datos/conexion.php';
            if (file_exists($path_conexion)) {
                require_once $path_conexion;
            } else {
                return false;
            }
        }

        // Evitar duplicados para asegurar que no se saturen ambos perfiles
        // Si ya existe una notificación idéntica (mismo usuario, tipo y referencia), actualizamos la URL y la fecha por si cambió el destino.
        $qCheck = $conexion->prepare("SELECT idNotificacion FROM tbl_notificaciones WHERE idUsuario = ? AND tipo = ? AND idReferencia = ?");
        $ref = intval($idReferencia);
        $qCheck->bind_param("isi", $idUsuario, $tipo, $ref);
        $qCheck->execute();
        $resCheck = $qCheck->get_result();
        
        if ($resCheck->num_rows > 0) {
            $existing = $resCheck->fetch_assoc();
            $idNotif = $existing['idNotificacion'];
            $qUpd = $conexion->prepare("UPDATE tbl_notificaciones SET url = ?, fecha = NOW(), leida = 0 WHERE idNotificacion = ?");
            $qUpd->bind_param("si", $url, $idNotif);
            return $qUpd->execute();
        }
        
        $sql = "INSERT INTO tbl_notificaciones (idUsuario, tipo, titulo, mensaje, url, idReferencia, leida, fecha) 
                VALUES (?, ?, ?, ?, ?, ?, 0, NOW())";
        
        $stmt = $conexion->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("issssi", $idUsuario, $tipo, $titulo, $mensaje, $url, $idReferencia);
            return $stmt->execute();
        }
        
        return false;
    }
}

/**
 * Notificar a todos los huéspedes sobre algo (ej: nueva propiedad)
 * Solo huéspedes deben recibir alertas globales de este tipo.
 */
if (!function_exists('notificarAHuespedes')) {
    function notificarAHuespedes($tipo, $titulo, $mensaje, $url, $idReferencia = 0) {
        global $conexion;
        
        // Obtener todos los usuarios con rol huésped
        // Se asume que tbl_rol tiene vNombreRol = 'huesped'
        $sql = "SELECT u.idUsuario 
                FROM tbl_usuarios u 
                JOIN tbl_roles_usuario r ON u.idRol = r.idRol 
                WHERE r.vNombreRol = 'huesped' AND u.bEstado = 1";
        
        $res = $conexion->query($sql);
        
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                registrarNotificacion($row['idUsuario'], $tipo, $titulo, $mensaje, $url, $idReferencia);
            }
            return true;
        }
        return false;
    }
}
?>
