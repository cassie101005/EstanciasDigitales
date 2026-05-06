<?php
require_once '../../datos/conexion.php';
require_once '../../datos/auth/queries_auth.php';

// Protección: este archivo solo debe ejecutarse desde apis/auth/reset_password.php
if (!isset($correo) || !isset($nuevaContrasenia)) {
    $resultado = ['error' => 'Acceso no permitido.'];
    return;
}

$queriesAuth = new QueriesAuth($conexion);

require_once '../../negocio/utilidades/seguridad.php';

// 1. Verificar patrones maliciosos en el correo (el password se valida en datos)
if (esSospechoso($correo)) {
    $resultado = ['ok' => false, 'mensaje' => 'Se detectó actividad sospechosa.'];
    return;
}

// 2. Verificar si el correo existe
$verificar = $queriesAuth->verificarCorreoExistente($correo);
if ($verificar->num_rows === 0) {
    $resultado = ['ok' => false, 'mensaje' => 'El correo electrónico no está registrado.'];
} else {
    // 3. Encriptar la contraseña (Hashing)
    $contraseniaHash = password_hash($nuevaContrasenia, PASSWORD_DEFAULT);

    // 4. Actualizar contraseña en la base de datos
    if ($queriesAuth->actualizarContrasenia($correo, $contraseniaHash)) {
        $resultado = ['ok' => true, 'mensaje' => 'Contraseña actualizada correctamente.'];
    } else {
        $resultado = ['ok' => false, 'mensaje' => 'Error al actualizar la contraseña.'];
    }
}
?>
