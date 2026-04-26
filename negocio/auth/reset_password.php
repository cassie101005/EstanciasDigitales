<?php
require_once '../../datos/conexion.php';
require_once '../../datos/auth/queries_auth.php';

// Protección: este archivo solo debe ejecutarse desde apis/auth/reset_password.php
if (!isset($correo) || !isset($nuevaContrasenia)) {
    $resultado = ['error' => 'Acceso no permitido.'];
    return;
}

$queriesAuth = new QueriesAuth($conexion);

// Verificar si el correo existe
$verificar = $queriesAuth->verificarCorreoExistente($correo);
if ($verificar->num_rows === 0) {
    $resultado = ['ok' => false, 'mensaje' => 'El correo electrónico no está registrado.'];
} else {
    // Actualizar contraseña
    if ($queriesAuth->actualizarContrasenia($correo, $nuevaContrasenia)) {
        $resultado = ['ok' => true, 'mensaje' => 'Contraseña actualizada correctamente.'];
    } else {
        $resultado = ['ok' => false, 'mensaje' => 'Error al actualizar la contraseña.'];
    }
}
?>
