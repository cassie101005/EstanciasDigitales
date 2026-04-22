<?php
require_once '../../datos/conexion.php';

// 1. Buscar el usuario por correo
$sql = "SELECT 
            u.idUsuario,
            u.idRol,
            u.vNombre,
            u.vApellido,
            u.vCorreo,
            u.vContrasenia,
            u.bEstado,
            r.vNombreRol
        FROM tbl_usuarios u
        INNER JOIN tbl_roles_usuario r ON u.idRol = r.idRol
        WHERE u.vCorreo = ?
        LIMIT 1";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $correo);
$stmt->execute();
$respuesta = $stmt->get_result();

// 2. Validar si existe el usuario
if ($respuesta->num_rows === 0) {
    $resultado = ['error' => 'El correo no existe.'];
} else {

    // 3. Obtener los datos del usuario
    $usuario = $respuesta->fetch_assoc();

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