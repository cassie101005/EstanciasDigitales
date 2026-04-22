<?php
$conexion = new mysqli("localhost", "root", "", "estancias_digitales");

if ($conexion->connect_error) {
    die("Error de conexión");
}