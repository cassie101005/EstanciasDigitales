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

    // 4. Validar si está activo
    if ((int)$usuario['bEstado'] !== 1) {
        $resultado = ['error' => 'El usuario está inactivo.'];
    } else {
        // 5. Validar contraseña
        if ($usuario['vContrasenia'] !== $contrasenia) {
            $resultado = ['error' => 'La contraseña es incorrecta.'];
        } else {
            // 6. Validar rol
            if (strtolower($usuario['vNombreRol']) !== strtolower($rol)) {
                $resultado = ['error' => 'El rol seleccionado no coincide con el usuario.'];
            } else {
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