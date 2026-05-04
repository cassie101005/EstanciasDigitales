<?php
require_once '../../datos/conexion.php';
require_once '../../datos/auth/queries_auth.php';

$queriesAuth = new QueriesAuth($conexion);

// 1. Verificar si el correo ya existe
$resultadoVerificacion = $queriesAuth->verificarCorreoExistente($correo);

if ($resultadoVerificacion->num_rows > 0) {
    $resultado = [
        'success' => false,
        'message' => 'El correo ya está registrado.'
    ];
    return; // Usamos return en lugar de exit para que el archivo que incluye este pueda continuar si fuera necesario, aunque aquí ya se cortaría la lógica
}

$fechaNacimiento = $data['fechaNacimiento'] ?? '';

if (!empty($fechaNacimiento)) {
    $fechaNacObj = new DateTime($fechaNacimiento);
    $hoy = new DateTime();
    $edad = $hoy->diff($fechaNacObj)->y;
    if ($edad < 18) {
        $resultado = [
            'success' => false,
            'message' => 'Debes ser mayor de 18 años para registrarte.'
        ];
        return;
    }
}

// 2. Preparar datos para inserción
$datosUsuario = [
    'idRol' => $idRol,
    'nombre' => $nombre,
    'apellido' => $apellido,
    'fechaNacimiento' => $fechaNacimiento,
    'correo' => $correo,
    'telefono' => $data['telefono'] ?? '',
    'contrasenia' => $contrasenia
];

// 3. Insertar el nuevo usuario
if ($queriesAuth->insertarUsuario($datosUsuario)) {
    $resultado = [
        'success' => true,
        'message' => 'Cuenta creada exitosamente',
        'idUsuario' => $conexion->insert_id
    ];
} else {
    $resultado = [
        'success' => false,
        'message' => 'No se pudo registrar el usuario.'
    ];
}
?>