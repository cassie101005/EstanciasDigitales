<?php
// Permitir peticiones desde cualquier origen
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Extraer datos del cliente
    $idCiudad = intval($_POST['idCiudad'] ?? 0);
    $idUsuario = intval($_POST['idUsuario'] ?? 0);
    $idTipoPropiedad = intval($_POST['idTipoPropiedad'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $precioNoche = floatval($_POST['precioNoche'] ?? 0);
    $descripcion = trim($_POST['descripcion'] ?? '');
    $especificaciones = trim($_POST['especificaciones'] ?? '');
    $capacidadHuespedes = intval($_POST['capacidadHuespedes'] ?? 0);
    $numeroHabitaciones = intval($_POST['numeroHabitaciones'] ?? 0);

    $servicios = $_POST['servicios'] ?? [];
    $reglas = $_POST['reglas'] ?? [];
    $politicas = $_POST['politicas'] ?? [];

    // 2. Validaciones básicas
    if (
        $idCiudad <= 0 ||
        $idUsuario <= 0 ||
        $idTipoPropiedad <= 0 ||
        empty($nombre) ||
        empty($direccion) ||
        $precioNoche <= 0 ||
        $capacidadHuespedes <= 0 ||
        $numeroHabitaciones <= 0
    ) {
        echo json_encode(['error' => 'Por favor, completa todos los campos correctamente.']);
        http_response_code(400);
        exit;
    }

    // 3. Ejecutar lógica de negocio de manera secuencial
    require_once '../../negocio/anfitrion/registrar_propiedad.php';

    // 4. Retornar resultados en formato JSON
    echo json_encode($resultado);

} else {
    echo json_encode(['error' => 'Metodo no permitido o datos invalidos.']);
    http_response_code(405);
}