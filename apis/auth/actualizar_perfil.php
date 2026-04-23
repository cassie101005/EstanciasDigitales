<?php
session_start();
header('Content-Type: application/json');
require_once '../../datos/conexion.php';
require_once '../../datos/auth/queries_auth.php';

if (!isset($_SESSION['idUsuario'])) {
    echo json_encode(['ok' => false, 'mensaje' => 'Sesión no iniciada']);
    exit;
}

$idUsuario = $_SESSION['idUsuario'];
$queriesAuth = new QueriesAuth($conexion);

// Obtener datos actuales del usuario
$resUser = $queriesAuth->obtenerUsuarioPorId($idUsuario);
$userActual = $resUser->fetch_assoc();
$fotoPath = $userActual['vFoto'] ?? '';

// Manejo de la foto de perfil
if (isset($_FILES['fotoPerfil']) && $_FILES['fotoPerfil']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['fotoPerfil']['tmp_name'];
    $fileName = $_FILES['fotoPerfil']['name'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    // Validar extensión
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (in_array($fileExtension, $allowedExtensions)) {
        $newFileName = 'user_' . $idUsuario . '_' . time() . '.' . $fileExtension;
        $uploadFileDir = '../../recursos/img/usuarios/';
        
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0777, true);
        }
        
        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $fotoPath = 'recursos/img/usuarios/' . $newFileName;
        }
    }
}

$datos = [
    'nombre' => $_POST['nombre'] ?? '',
    'apellido' => $_POST['apellido'] ?? '',
    'fechaNacimiento' => $_POST['fechaNacimiento'] ?? '',
    'correo' => $_POST['correo'] ?? '',
    'telefono' => $_POST['telefono'] ?? '',
    'contrasenia' => $_POST['contrasenia'] ?? '',
    'foto' => $fotoPath
];

if (empty($datos['nombre']) || empty($datos['correo'])) {
    echo json_encode(['ok' => false, 'mensaje' => 'Nombre y Correo son obligatorios']);
    exit;
}

if ($queriesAuth->actualizarPerfil($idUsuario, $datos)) {
    $_SESSION['nombre'] = $datos['nombre'] . ' ' . $datos['apellido'];
    $_SESSION['foto'] = $fotoPath;
    echo json_encode(['ok' => true, 'mensaje' => 'Perfil actualizado correctamente']);
} else {
    echo json_encode(['ok' => false, 'mensaje' => 'Error al actualizar el perfil']);
}
?>
