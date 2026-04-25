<?php
require_once '../../datos/conexion.php';
require_once '../../datos/anfitrion/queries_propiedades.php';

if (!isset($idUsuario) || !isset($accion)) {
    $resultado = ['error' => 'Acceso no permitido'];
    return;
}

$queriesPropiedades = new QueriesPropiedades($conexion);

if ($accion === 'listar') {
    $resultadoQuery = $queriesPropiedades->obtenerPropiedadesUsuario($idUsuario);
    
    $propiedades = [];
    while ($fila = $resultadoQuery->fetch_assoc()) {
        $propiedades[] = $fila;
    }

    $resultado = ['ok' => true, 'propiedades' => $propiedades];
}
else if ($accion === 'detalle') {
    // Datos principales
    $resultadoQuery = $queriesPropiedades->obtenerDetallePropiedad($idPropiedad, $idUsuario);
    $propiedad = $resultadoQuery->fetch_assoc();

    if (!$propiedad) {
        $resultado = ['error' => 'Propiedad no encontrada.'];
        return;
    }

    // Imágenes
    $resultadoImagenes = $queriesPropiedades->obtenerImagenesPropiedad($idPropiedad);
    $imagenes = [];
    while ($img = $resultadoImagenes->fetch_assoc()) {
        $imagenes[] = $img['vImagen'];
    }

    // Servicios
    $resultadoServicios = $queriesPropiedades->obtenerServiciosPropiedad($idPropiedad);
    $servicios = [];
    while ($s = $resultadoServicios->fetch_assoc()) {
        $servicios[] = $s['vNombreServicio'];
    }

    // Reglas
    $resultadoReglas = $queriesPropiedades->obtenerReglasPropiedad($idPropiedad);
    $reglas = [];
    while ($r = $resultadoReglas->fetch_assoc()) {
        $reglas[] = $r['vNombreRegla'];
    }

    // Políticas
    $resultadoPoliticas = $queriesPropiedades->obtenerPoliticasPropiedad($idPropiedad);
    $politicas = [];
    while ($p = $resultadoPoliticas->fetch_assoc()) {
        $politicas[] = $p['vNombrePol'];
    }

    // Reseñas
    $resultadoResenias = $queriesPropiedades->obtenerReseniasPropiedad($idPropiedad);
    $resenias = [];
    while ($res = $resultadoResenias->fetch_assoc()) {
        $resenias[] = $res;
    }

    $resultado = [
        'ok'        => true,
        'propiedad' => $propiedad,
        'imagenes'  => $imagenes,
        'servicios' => $servicios,
        'reglas'    => $reglas,
        'politicas' => $politicas,
        'resenias'  => $resenias,
    ];
}

