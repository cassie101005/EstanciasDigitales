<?php
require_once 'datos/conexion.php';
$_POST['idReserva'] = 3;
$_POST['role'] = 'huesped';
$_POST['idUsuario'] = 4;
ob_start();
include 'apis/cancelar_reserva.php';
$output = ob_get_clean();
echo "OUTPUT:\n" . $output;
?>
