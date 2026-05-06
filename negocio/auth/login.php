<?php
require_once '../../datos/conexion.php';
require_once '../../datos/auth/queries_auth.php';

// Protección: este archivo solo debe ejecutarse desde apis/auth/login.php
if (!isset($correo) || !isset($contrasenia) || !isset($rol)) {
    $resultado = ['error' => 'Acceso no permitido.'];
    return;
}

$queriesAuth = new QueriesAuth($conexion);

// 1. Buscar el usuario por correo
$resultadoQuery = $queriesAuth->buscarUsuarioPorCorreo($correo);


// 2. Validar si existe el usuario
if ($resultadoQuery->num_rows === 0) {
    $resultado = ['error' => 'El correo no existe.'];
} else {
    // 3. Obtener los datos del usuario
    $usuario = $resultadoQuery->fetch_assoc();

    // 3.1 Verificar si la cuenta está bloqueada
    if (!empty($usuario['bloqueado_hasta'])) {
        $bloqueo = new DateTime($usuario['bloqueado_hasta']);
        $ahora = new DateTime();
        if ($bloqueo > $ahora) {
            $resultado = ['error' => 'Tu cuenta ha sido bloqueada por demasiados intentos fallidos. Intenta nuevamente mañana.'];
            return;
        }
    }

    // 4. Validar si está activo
    if ((int)$usuario['bEstado'] !== 1) {
        $resultado = ['error' => 'El usuario está inactivo.'];
    } else {
        // 5. Validar contraseña
        $passwordCorrecto = password_verify($contrasenia, $usuario['vContrasenia']) || $usuario['vContrasenia'] === $contrasenia;

        if (!$passwordCorrecto) {
            // Incrementar intentos fallidos
            $queriesAuth->incrementarIntentosFallidos($usuario['idUsuario']);
            $nuevosIntentos = $usuario['intentos_fallidos'] + 1;

            if ($nuevosIntentos >= 5) {
                $queriesAuth->bloquearUsuario($usuario['idUsuario']);
                $resultado = ['error' => 'Tu cuenta ha sido bloqueada por demasiados intentos fallidos. Intenta nuevamente mañana.'];
            } else {
                $resultado = ['error' => 'La contraseña es incorrecta.'];
            }
        } else {
            // 6. Validar rol
            if (strtolower($usuario['vNombreRol']) !== strtolower($rol)) {
                $resultado = ['error' => 'El rol seleccionado no coincide con el usuario.'];
            } else {
                // Reiniciar intentos fallidos al iniciar sesión correctamente
                $queriesAuth->reiniciarIntentos($usuario['idUsuario']);

                // 7. Guardar datos en sesión
                $_SESSION['idUsuario'] = $usuario['idUsuario'];
                $_SESSION['idRol'] = $usuario['idRol'];
                $_SESSION['rol'] = $usuario['vNombreRol'];
                $_SESSION['nombre'] = $usuario['vNombre'] . ' ' . $usuario['vApellido'];
                $_SESSION['foto'] = $usuario['vFoto'] ?? '';

                // 8. Definir ruta según rol
                $ruta = './presentacion/huesped/home.php';

                if ($usuario['vNombreRol'] === 'admin') {
                    $ruta = './presentacion/admin/dashboard.php';
                }

                if ($usuario['vNombreRol'] === 'anfitrion') {
                    $ruta = './presentacion/anfitrion/dashboard.php';
                }

                if ($usuario['vNombreRol'] === 'huesped') {
                    $ruta = './presentacion/huesped/home.php';
                }

                // 9. Preparar respuesta final
                $resultado = [
                    'ok' => true,
                    'mensaje' => 'Inicio de sesión correcto.',
                    'redirect' => $ruta
                ];
            }
        }
    }
}
?>