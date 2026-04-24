<?php
require_once '../../datos/conexion.php';
require_once '../../datos/anfitrion/queries_edicion_propiedad.php';
require_once '../../datos/anfitrion/queries_registro_propiedad.php';

$queriesEdicion = new QueriesEdicionPropiedad($conexion);
$queriesRegistro = new QueriesRegistroPropiedad($conexion);

if ($accion === 'obtener') {
    $idPropiedad = intval($_GET['id'] ?? 0);
    if ($idPropiedad <= 0) {
        $resultado = ['error' => 'ID inválido.'];
        return;
    }

    $consulta = $queriesEdicion->obtenerPropiedadPorId($idPropiedad, $idUsuario);
    $propiedad = $consulta->fetch_assoc();

    if (!$propiedad) {
        $resultado = ['error' => 'Propiedad no encontrada.'];
        return;
    }

    // Obtener imágenes
    $resImgs = $queriesEdicion->obtenerImagenes($idPropiedad);
    $imagenes = [];
    while($img = $resImgs->fetch_assoc()) $imagenes[] = $img;

    // Obtener servicios seleccionados
    $resServs = $queriesEdicion->obtenerServiciosSeleccionados($idPropiedad);
    $servicios = [];
    while($s = $resServs->fetch_assoc()) $servicios[] = $s['idServicio'];

    // Obtener reglas seleccionadas
    $resReglas = $queriesEdicion->obtenerReglasSeleccionadas($idPropiedad);
    $reglas = [];
    while($r = $resReglas->fetch_assoc()) $reglas[] = $r['idRegla'];

    // Obtener políticas seleccionadas
    $resPols = $queriesEdicion->obtenerPoliticasSeleccionadas($idPropiedad);
    $politicas = [];
    while($p = $resPols->fetch_assoc()) $politicas[] = $p['idPolitica'];

    $resultado = [
        'ok' => true,
        'propiedad' => $propiedad,
        'imagenes' => $imagenes,
        'servicios' => $servicios,
        'reglas' => $reglas,
        'politicas' => $politicas
    ];
}

else if ($accion === 'actualizar') {
    $idPropiedad = intval($_POST['idPropiedad'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $idTipoPropiedad = intval($_POST['idTipoPropiedad'] ?? 0);
    $precioNoche = floatval($_POST['precioNoche'] ?? 0);
    $capacidadHuespedes = intval($_POST['capacidadHuespedes'] ?? 0);
    $numeroHabitaciones = intval($_POST['numeroHabitaciones'] ?? 0);
    $numeroBanos = intval($_POST['numeroBanos'] ?? 0);
    $idCiudad = intval($_POST['idCiudad'] ?? 0);
    $direccion = trim($_POST['direccion'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    
    $servicios = json_decode($_POST['servicios'] ?? '[]');
    $reglas = json_decode($_POST['reglas'] ?? '[]');
    $politicas = json_decode($_POST['politicas'] ?? '[]');

    if ($idPropiedad <= 0 || empty($nombre) || $idTipoPropiedad <= 0 || $precioNoche <= 0) {
        $resultado = ['error' => 'Faltan datos obligatorios.'];
        return;
    }

    $datos = [
        'idPropiedad' => $idPropiedad,
        'idCiudad' => $idCiudad,
        'idTipoPropiedad' => $idTipoPropiedad,
        'nombre' => $nombre,
        'direccion' => $direccion,
        'precioNoche' => $precioNoche,
        'descripcion' => $descripcion,
        'especificaciones' => trim($_POST['especificaciones'] ?? ''),
        'capacidadHuespedes' => $capacidadHuespedes,
        'numeroHabitaciones' => $numeroHabitaciones
    ];

    if ($queriesEdicion->actualizarPropiedad($datos, $idUsuario)) {
        // Actualizar relaciones (limpiar y reinsertar)
        $queriesEdicion->limpiarRelaciones($idPropiedad);

        foreach ($servicios as $idS) $queriesRegistro->insertarServicioPropiedad($idS, $idPropiedad);
        foreach ($reglas as $idR) $queriesRegistro->insertarReglaPropiedad($idR, $idPropiedad);
        foreach ($politicas as $idP) $queriesRegistro->insertarPoliticaPropiedad($idP, $idPropiedad);

        $resultado = ['ok' => true, 'mensaje' => 'Propiedad actualizada correctamente.'];
    } else {
        $resultado = ['error' => 'No se pudo actualizar la propiedad.'];
    }
}

else if ($accion === 'eliminar_imagen') {
    $idImagen = intval($_POST['idImagen'] ?? 0);
    $idPropiedad = intval($_POST['idPropiedad'] ?? 0);
    
    if ($queriesEdicion->eliminarImagen($idImagen, $idPropiedad)) {
        $resultado = ['ok' => true];
    } else {
        $resultado = ['error' => 'No se pudo eliminar la imagen.'];
    }
}
?>
