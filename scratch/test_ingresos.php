<?php
require_once '../datos/conexion.php';
require_once '../negocio/anfitrion/propiedades_view.php';
$idHost = 1; // Or whatever host the user has
echo getHostIngresos($idHost, $conexion);
?>
