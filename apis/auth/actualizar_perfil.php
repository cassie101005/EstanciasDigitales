<?php
header('Content-Type: application/json');
require_once '../../negocio/auth/verificar_sesion.php';
validarSesionAPI(); // Cualquier usuario autenticado
require_once '../../datos/conexion.php';
require_once '../../datos/auth/queries_auth.php';

$idUsuario = $_SESSION['idUsuario'];
$queriesAuth = new QueriesAuth($conexion);

// Obtener datos actuales del usuario
$resUser = $queriesAuth->obtenerUsuarioPorId($idUsuario);
$userActual = $resUser->fetch_assoc();
$fotoPath = $userActual['vFoto'] ?? '';

// Manejo de la foto de perfil
if (isset($_FILES['fotoPerfil'])) {
    if ($_FILES['fotoPerfil']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['fotoPerfil']['tmp_name'];
        $fileName = $_FILES['fotoPerfil']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

    // Validar extensión
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = 'user_' . $idUsuario . '_' . time() . '.' . $fileExtension;
            $uploadFileDir = '../../recursos/img/perfiles/';
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }
            
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $fotoPath = 'recursos/img/perfiles/' . $newFileName;
            } else {
                echo json_encode(['ok' => false, 'mensaje' => 'Error al mover el archivo subido.']);
                exit;
            }
        } else {
            echo json_encode(['ok' => false, 'mensaje' => 'Extensión de imagen no permitida.']);
            exit;
        }
    } elseif ($_FILES['fotoPerfil']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Hubo un error al subir (ej. excede tamaño máximo)
        $errCode = $_FILES['fotoPerfil']['error'];
        echo json_encode(['ok' => false, 'mensaje' => 'Error al subir la imagen. Código: ' . $errCode]);
        exit;
    }
}

$datos = [
    'nombre' => htmlspecialchars(trim($_POST['nombre'] ?? '')),
    'apellido' => htmlspecialchars(trim($_POST['apellido'] ?? '')),
    'fechaNacimiento' => htmlspecialchars(trim($_POST['fechaNacimiento'] ?? '')),
    'correo' => htmlspecialchars(trim($_POST['correo'] ?? '')),
    'telefono' => htmlspecialchars(trim($_POST['telefono'] ?? '')),
    'contrasenia' => !empty(trim($_POST['contrasenia'] ?? '')) ? password_hash(trim($_POST['contrasenia']), PASSWORD_DEFAULT) : $userActual['vContrasenia'],
    'foto' => $fotoPath
];

if (!empty($datos['telefono'])) {
    if (!preg_match('/^[0-9]{10}$/', $datos['telefono'])) {
        echo json_encode(['ok' => false, 'mensaje' => 'El teléfono debe tener exactamente 10 dígitos y solo contener números.']);
        exit;
    }
}

if (empty($datos['nombre']) || empty($datos['correo'])) {
    echo json_encode(['ok' => false, 'mensaje' => 'Nombre y Correo son obligatorios']);
    exit;
}

if ($datos['correo'] !== $userActual['vCorreo']) {
    $resCorreo = $queriesAuth->verificarCorreoExistente($datos['correo']);
    if ($resCorreo->num_rows > 0) {
        echo json_encode(['ok' => false, 'mensaje' => 'No puedes utilizar ese correo, ya está registrado por otro usuario.']);
        exit;
    }
}

if (!empty($datos['fechaNacimiento'])) {
    $fechaNacObj = new DateTime($datos['fechaNacimiento']);
    $hoy = new DateTime();
    $edad = $hoy->diff($fechaNacObj)->y;
    if ($edad < 18) {
        echo json_encode(['ok' => false, 'mensaje' => 'Debes ser mayor de 18 años para actualizar tu perfil.']);
        exit;
    }
}

if ($queriesAuth->actualizarPerfil($idUsuario, $datos)) {
    $_SESSION['nombre'] = $datos['nombre'] . ' ' . $datos['apellido'];
    $_SESSION['foto'] = $fotoPath;
    echo json_encode(['ok' => true, 'mensaje' => 'Perfil actualizado correctamente']);
} else {
    echo json_encode(['ok' => false, 'mensaje' => 'Error al actualizar el perfil']);
}
?>
