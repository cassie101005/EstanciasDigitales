<?php
/**
 * verificar_sesion.php
 * Centraliza la seguridad de acceso a las vistas.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Evitar acceso con el botón "atrás" después de cerrar sesión (Cache Control)
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

/**
 * Función principal para proteger una página.
 * @param string|array $rolesPermitidos El rol o roles que pueden ver esta página (ej: 'admin', 'huesped', ['admin', 'anfitrion'])
 * @param string $rutaBase Ruta para regresar al login si no hay sesión
 */
function validarSesion($rolesPermitidos = [], $rutaBase = '../../') {
    // Asegurar que $rolesPermitidos sea un array
    if (!is_array($rolesPermitidos)) {
        $rolesPermitidos = [$rolesPermitidos];
    }

    // 1. Verificar si existe la sesión
    if (!isset($_SESSION['idUsuario']) || !isset($_SESSION['rol'])) {
        // No hay sesión, destruir lo que quede y mandar al login
        session_unset();
        session_destroy();
        header("Location: " . $rutaBase . "index.php");
        exit();
    }

    // 2. Verificar permisos por rol (si se especificaron roles)
    if (!empty($rolesPermitidos)) {
        $rolUsuario = strtolower($_SESSION['rol']);
        $permitido = false;

        foreach ($rolesPermitidos as $rolPermitido) {
            if ($rolUsuario === strtolower($rolPermitido)) {
                $permitido = true;
                break;
            }
        }

        if (!$permitido) {
            // El usuario está logueado pero no tiene permiso para esta página específica
            // Redirigir a su home correspondiente según su rol real
            switch ($rolUsuario) {
                case 'admin':
                    header("Location: " . $rutaBase . "presentacion/admin/dashboard.php");
                    break;
                case 'anfitrion':
                    header("Location: " . $rutaBase . "presentacion/anfitrion/dashboard.php");
                    break;
                case 'huesped':
                default:
                    header("Location: " . $rutaBase . "presentacion/huesped/home.php");
                    break;
            }
            exit();
        }
    }
}

/**
 * Validación para archivos de API (retorna JSON en lugar de redireccionar)
 */
function validarSesionAPI($rolesPermitidos = []) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!is_array($rolesPermitidos)) {
        $rolesPermitidos = [$rolesPermitidos];
    }

    if (!isset($_SESSION['idUsuario']) || !isset($_SESSION['rol'])) {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['ok' => false, 'error' => 'Sesión no activa o expirada']);
        exit();
    }

    if (!empty($rolesPermitidos)) {
        $rolUsuario = strtolower($_SESSION['rol']);
        $permitido = false;
        foreach ($rolesPermitidos as $rolPermitido) {
            if ($rolUsuario === strtolower($rolPermitido)) {
                $permitido = true;
                break;
            }
        }

        if (!$permitido) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'No tienes permisos para realizar esta acción']);
            exit();
        }
    }
}
?>
