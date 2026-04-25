<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

require_once '../../negocio/auth/verificar_sesion.php';
validarSesionAPI('anfitrion');
require_once '../../datos/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$accion = $_REQUEST['accion'] ?? 'guardar';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($accion === 'guardar') {
        session_start();
        $idCiudad = intval($_POST['idCiudad'] ?? 0);
        $idUsuario = intval($_SESSION['idUsuario'] ?? 0);
        $idTipoPropiedad = intval($_POST['idTipoPropiedad'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $precioNoche = floatval($_POST['precioNoche'] ?? 0);
        $descripcion = trim($_POST['descripcion'] ?? '');
        $especificaciones = trim($_POST['especificaciones'] ?? '');
        $capacidadHuespedes = intval($_POST['capacidadHuespedes'] ?? 0);
        $numeroHabitaciones = intval($_POST['numeroHabitaciones'] ?? 0);

        $servicios = json_decode($_POST['servicios'] ?? '[]');
        $reglas = json_decode($_POST['reglas'] ?? '[]');
        $politicas = json_decode($_POST['politicas'] ?? '[]');
        
        $reglaExtra = trim($_POST['reglaExtra'] ?? ''); // <--- Regla adicional manual

        if ($idCiudad <= 0 || $idUsuario <= 0 || $idTipoPropiedad <= 0 || empty($nombre) || empty($direccion) || $precioNoche <= 0 || $capacidadHuespedes <= 0 || $numeroHabitaciones <= 0) {
            echo json_encode(['error' => 'Por favor, completa todos los campos obligatorios correctamente.']);
            http_response_code(400);
            exit;
        }
    } else if ($accion === 'subir_imagenes') {
        $idPropiedad = intval($_POST['idPropiedad'] ?? 0);
        if ($idPropiedad <= 0 || empty($_FILES['imagenes'])) {
            echo json_encode(['error' => 'No hay imágenes o falta ID de propiedad.']);
            http_response_code(400);
            exit;
        }
    }
}

require_once '../../negocio/anfitrion/registrar_propiedad.php';

if (isset($resultado)) {
    echo json_encode($resultado);
} else {
    echo json_encode(['error' => 'Acción no encontrada o lógica fallida.']);
    http_response_code(400);
}