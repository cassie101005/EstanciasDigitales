<?php
// Este archivo se encarga de extraer y validar los datos para una respuesta del anfitrión

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';
$respuesta = isset($_POST['respuesta']) ? htmlspecialchars(trim($_POST['respuesta']), ENT_QUOTES, 'UTF-8') : '';

if ($id <= 0 || empty($tipo) || empty($respuesta)) {
    echo json_encode(['ok' => false, 'error' => 'Datos incompletos o respuesta vacía']);
    exit;
}
?>
