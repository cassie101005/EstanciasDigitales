<?php
// Este archivo se encarga de extraer y validar los datos para el registro de una propiedad

if ($accion === 'guardar') {
    $idCiudad = intval($_POST['idCiudad'] ?? 0);
    $idUsuario = intval($_SESSION['idUsuario'] ?? 0);
    $idTipoPropiedad = intval($_POST['idTipoPropiedad'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $precioNoche = floatval($_POST['precioNoche'] ?? 0);
    $descripcion = trim($_POST['descripcion'] ?? '');
    $capacidadHuespedes = intval($_POST['capacidadHuespedes'] ?? 0);
    $numeroHabitaciones = intval($_POST['numeroHabitaciones'] ?? 0);
    $numeroBanos = intval($_POST['numeroBanos'] ?? 0);

    $servicios = json_decode($_POST['servicios'] ?? '[]');
    $reglas = json_decode($_POST['reglas'] ?? '[]');
    $politicas = json_decode($_POST['politicas'] ?? '[]');

    if ($idCiudad <= 0 || $idUsuario <= 0 || $idTipoPropiedad <= 0 || empty($nombre) || empty($direccion) || $precioNoche <= 0 || $capacidadHuespedes <= 0 || $numeroHabitaciones <= 0 || $numeroBanos <= 0) {
        echo json_encode(['error' => 'Por favor, completa todos los campos obligatorios correctamente.']);
        exit;
    }

    if (!preg_match('/^[a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ]+$/u', $nombre)) {
        echo json_encode(['error' => 'El nombre de la propiedad solo puede contener letras y números, sin caracteres especiales.']);
        exit;
    }

    if (!preg_match('/[a-zA-ZáéíóúÁÉÍÓÚñÑ]/u', $nombre)) {
        echo json_encode(['error' => 'El nombre de la propiedad debe contener al menos una letra.']);
        exit;
    }
} else if ($accion === 'subir_imagenes') {
    $idPropiedad = intval($_POST['idPropiedad'] ?? 0);
    if ($idPropiedad <= 0 || empty($_FILES['imagenes'])) {
        echo json_encode(['error' => 'No hay imágenes o falta ID de propiedad.']);
        exit;
    }
}
?>
