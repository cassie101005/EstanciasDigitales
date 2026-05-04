<?php
require_once '../../datos/conexion.php';

require_once '../../negocio/auth/verificar_sesion.php';
validarSesionAPI(); // Permite cualquier rol siempre que esté logueado

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idNotificacion = intval($_POST['idNotificacion'] ?? 0);
    $idUsuario = intval($_SESSION['idUsuario']);
    
    if ($idNotificacion > 0) {
        $stmt = $conexion->prepare("UPDATE tbl_notificaciones SET leida = 1 WHERE idNotificacion = ? AND idUsuario = ?");
        $stmt->bind_param("ii", $idNotificacion, $idUsuario);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $conexion->error]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID inválido']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
}
?>
