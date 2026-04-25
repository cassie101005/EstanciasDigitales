<?php
/**
 * Queries para autenticación y usuarios
 * Solo consultas SQL - sin lógica de negocio
 */

class QueriesAuth {
    private $conexion;
    
    public function __construct($conexion) {
        $this->conexion = $conexion;
    }
    
    /**
     * Buscar usuario por correo
     */
    public function buscarUsuarioPorCorreo($correo) {
        $sql = "SELECT 
                    u.idUsuario,
                    u.idRol,
                    u.vNombre,
                    u.vApellido,
                    u.vCorreo,
                    u.vContrasenia,
                    u.bEstado,
                    u.vFoto,
                    r.vNombreRol
                FROM tbl_usuarios u
                INNER JOIN tbl_roles_usuario r ON u.idRol = r.idRol
                WHERE u.vCorreo = ?
                LIMIT 1";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    /**
     * Obtener usuario por ID
     */
    public function obtenerUsuarioPorId($idUsuario) {
        $sql = "SELECT 
                    u.*,
                    r.vNombreRol
                FROM tbl_usuarios u
                INNER JOIN tbl_roles_usuario r ON u.idRol = r.idRol
                WHERE u.idUsuario = ?
                LIMIT 1";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    /**
     * Verificar si correo ya existe
     */
    public function verificarCorreoExistente($correo) {
        $sql = "SELECT idUsuario FROM tbl_usuarios WHERE vCorreo = ? LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    /**
     * Insertar nuevo usuario
     */
    public function insertarUsuario($datos) {
        $sql = "INSERT INTO tbl_usuarios (
                    idRol,
                    vNombre,
                    vApellido,
                    dFechaNacimiento,
                    vCorreo,
                    vTelefono,
                    vContrasenia,
                    bEstado
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 1)";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param(
            "issssss",
            $datos['idRol'],
            $datos['nombre'],
            $datos['apellido'],
            $datos['fechaNacimiento'],
            $datos['correo'],
            $datos['telefono'],
            $datos['contrasenia']
        );
        
        return $stmt->execute();
    }

    /**
     * Actualizar perfil de usuario
     */
    public function actualizarPerfil($idUsuario, $datos) {
        $sql = "UPDATE tbl_usuarios SET 
                    vNombre = ?, 
                    vApellido = ?, 
                    dFechaNacimiento = ?, 
                    vCorreo = ?, 
                    vTelefono = ?, 
                    vContrasenia = ?,
                    vFoto = ?
                WHERE idUsuario = ?";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param(
            "sssssssi",
            $datos['nombre'],
            $datos['apellido'],
            $datos['fechaNacimiento'],
            $datos['correo'],
            $datos['telefono'],
            $datos['contrasenia'],
            $datos['foto'],
            $idUsuario
        );
        
        return $stmt->execute();
    }

    /**
     * Actualizar solo la contraseña por correo
     */
    public function actualizarContrasenia($correo, $nuevaContrasenia) {
        $sql = "UPDATE tbl_usuarios SET vContrasenia = ? WHERE vCorreo = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ss", $nuevaContrasenia, $correo);
        return $stmt->execute();
    }
}

?>