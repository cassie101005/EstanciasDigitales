<?php
require_once '../../datos/conexion.php';

// 1. Verificar si el correo ya existe
$sqlVerificar = "SELECT idUsuario FROM tbl_usuarios WHERE vCorreo = ? LIMIT 1";
$stmtVerificar = $conexion->prepare($sqlVerificar);
$stmtVerificar->bind_param("s", $correo);
$stmtVerificar->execute();
$consultaVerificar = $stmtVerificar->get_result();

if ($consultaVerificar->num_rows > 0) {
    $resultado = [
        'ok' => false,
        'mensaje' => 'El correo ya está registrado.'
    ];
    exit;
}

// 2. Insertar el nuevo usuario
$sqlInsertar = "INSERT INTO tbl_usuarios (
                    idRol,
                    vNombre,
                    vApellido,
                    dFechaNacimiento,
                    vCorreo,
                    vTelefono,
                    vContrasenia,
                    bEstado
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 1)";

$stmtInsertar = $conexion->prepare($sqlInsertar);
$stmtInsertar->bind_param(
    "issssss",
    $idRol,
    $nombre,
    $apellido,
    $fechaNacimiento,
    $correo,
    $telefono,
    $contrasenia
);

if ($stmtInsertar->execute()) {
    $resultado = [
        'ok' => true,
        'mensaje' => 'Usuario registrado correctamente.',
        'idUsuario' => $conexion->insert_id
    ];
} else {
    $resultado = [
        'ok' => false,
        'mensaje' => 'No se pudo registrar el usuario.'
    ];
}