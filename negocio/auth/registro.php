<?php
require_once '../../datos/conexion.php';
require_once '../../datos/auth/queries_auth.php';

$queriesAuth = new QueriesAuth($conexion);

// 1. Verificar si el correo ya existe
$resultadoVerificacion = $queriesAuth->verificarCorreoExistente($correo);

if ($resultadoVerificacion->num_rows > 0) {
    $resultado = [
        'ok' => false,
        'mensaje' => 'El correo ya está registrado.'
    ];
    exit;
}

// 2. Preparar datos para inserción
$datosUsuario = [
    'idRol' => $idRol,
    'nombre' => $nombre,
    'apellido' => $apellido,
    'fechaNacimiento' => $fechaNacimiento,
    'correo' => $correo,
    'telefono' => $telefono,
    'contrasenia' => $contrasenia
];

// 3. Insertar el nuevo usuario
if ($queriesAuth->insertarUsuario($datosUsuario)) {
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
?>