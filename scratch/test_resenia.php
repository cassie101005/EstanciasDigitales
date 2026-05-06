<?php
require_once 'datos/conexion.php';
require_once 'negocio/huesped/resenia.php';

$resNeg = new ReseniaNegocio($conexion);
// Simulamos una inserción para la propiedad 1, usuario 2
$res = $resNeg->guardarResenia(1, 2, 5, "Comentario de prueba " . time());
print_r($res);
