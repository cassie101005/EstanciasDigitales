<?php
require_once '../../datos/conexion.php';
require_once '../../datos/anfitrion/queries_edicion_propiedad.php';
require_once '../../datos/anfitrion/queries_registro_propiedad.php';

$queriesEdicion = new QueriesEdicionPropiedad($conexion);
$queriesRegistro = new QueriesRegistroPropiedad($conexion);

// ── OBTENER ──────────────────────────────────────────────────────────────────
if ($accion === 'obtener') {
    $idPropiedad = intval($_GET['id'] ?? 0);
    if ($idPropiedad <= 0) {
        $resultado = ['ok' => false, 'error' => 'ID inválido.'];
        return;
    }

    $consulta = $queriesEdicion->obtenerPropiedadPorId($idPropiedad, $idUsuario);
    $propiedad = $consulta->fetch_assoc();

    if (!$propiedad) {
        $resultado = ['ok' => false, 'error' => 'Propiedad no encontrada.'];
        return;
    }

    // Imágenes
    $resImgs = $queriesEdicion->obtenerImagenes($idPropiedad);
    $imagenes = [];
    while ($img = $resImgs->fetch_assoc())
        $imagenes[] = $img;

    // Servicios seleccionados
    $resServs = $queriesEdicion->obtenerServiciosSeleccionados($idPropiedad);
    $servicios = [];
    while ($s = $resServs->fetch_assoc())
        $servicios[] = intval($s['idServicio']);

    // Reglas seleccionadas
    $resReglas = $queriesEdicion->obtenerReglasSeleccionadas($idPropiedad);
    $reglas = [];
    while ($r = $resReglas->fetch_assoc())
        $reglas[] = intval($r['idRegla']);

    // Políticas seleccionadas
    $resPols = $queriesEdicion->obtenerPoliticasSeleccionadas($idPropiedad);
    $politicas = [];
    while ($p = $resPols->fetch_assoc())
        $politicas[] = intval($p['idPolitica']);

    $resultado = [
        'ok' => true,
        'propiedad' => $propiedad,
        'imagenes' => $imagenes,
        'servicios' => $servicios,
        'reglas' => $reglas,
        'politicas' => $politicas
    ];
}

// ── ACTUALIZAR ───────────────────────────────────────────────────────────────
elseif ($accion === 'actualizar') {
    $idPropiedad = intval($_POST['idPropiedad'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');

    if (!preg_match('/^[a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ]+$/u', $nombre)) {
        $resultado = ['ok' => false, 'error' => 'El nombre de la propiedad solo puede contener letras y números, sin caracteres especiales.'];
        return;
    }

    if (!preg_match('/[a-zA-ZáéíóúÁÉÍÓÚñÑ]/u', $nombre)) {
        $resultado = ['ok' => false, 'error' => 'El nombre de la propiedad debe contener al menos una letra.'];
        return;
    }
    $idTipoPropiedad = intval($_POST['idTipoPropiedad'] ?? 0);
    $precioNoche = floatval($_POST['precioNoche'] ?? 0);
    $capacidadHuespedes = intval($_POST['capacidadHuespedes'] ?? 0);
    $numeroHabitaciones = intval($_POST['numeroHabitaciones'] ?? 0);
    $numeroBanos = intval($_POST['numeroBanos'] ?? 0);
    $idCiudad = intval($_POST['idCiudad'] ?? 0);
    $direccion = trim($_POST['direccion'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    $servicios = json_decode($_POST['servicios'] ?? '[]', true) ?: [];
    $reglas = json_decode($_POST['reglas'] ?? '[]', true) ?: [];
    $politicas = json_decode($_POST['politicas'] ?? '[]', true) ?: [];

    if ($idPropiedad <= 0 || empty($nombre) || $idTipoPropiedad <= 0 || $precioNoche <= 0) {
        error_log("FALLO EDICION: idPropiedad=$idPropiedad, nombre=$nombre, idTipo=$idTipoPropiedad, precio=$precioNoche");
        $resultado = ['ok' => false, 'error' => 'Faltan datos obligatorios.'];
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
        'capacidadHuespedes' => $capacidadHuespedes,
        'numeroHabitaciones' => $numeroHabitaciones,
        'numeroBanos' => $numeroBanos
    ];

    if ($queriesEdicion->actualizarPropiedad($datos, $idUsuario)) {
        $queriesEdicion->limpiarRelaciones($idPropiedad);

        foreach ($servicios as $idS)
            $queriesRegistro->insertarServicioPropiedad(intval($idS), $idPropiedad);
        foreach ($reglas as $idR)
            $queriesRegistro->insertarReglaPropiedad(intval($idR), $idPropiedad);
        foreach ($politicas as $idP)
            $queriesRegistro->insertarPoliticaPropiedad(intval($idP), $idPropiedad);

        $resultado = ['ok' => true, 'mensaje' => 'Propiedad actualizada correctamente.'];
    } else {
        $resultado = ['ok' => false, 'error' => 'No se pudo actualizar la propiedad.'];
    }
}

// ── ELIMINAR IMAGEN ──────────────────────────────────────────────────────────
elseif ($accion === 'eliminar_imagen') {
    $idImagen = intval($_POST['idImagen'] ?? 0);
    $idPropiedad = intval($_POST['idPropiedad'] ?? 0);

    if ($queriesEdicion->eliminarImagen($idImagen, $idPropiedad)) {
        $resultado = ['ok' => true];
    } else {
        $resultado = ['ok' => false, 'error' => 'No se pudo eliminar la imagen.'];
    }
}

// ── SUBIR IMÁGENES NUEVAS ────────────────────────────────────────────────────
elseif ($accion === 'subir_imagenes') {
    $idPropiedad = intval($_POST['idPropiedad'] ?? 0);

    if ($idPropiedad <= 0) {
        $resultado = ['ok' => false, 'error' => 'ID de propiedad inválido.'];
        return;
    }

    $uploadDir = '../../recursos/img/propiedades/';

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $subidos = 0;
    $errores = [];

    if (!empty($_FILES['imagenes']['name'][0])) {
        $total = count($_FILES['imagenes']['name']);
        for ($i = 0; $i < $total; $i++) {
            if ($_FILES['imagenes']['error'][$i] !== UPLOAD_ERR_OK)
                continue;

            $ext = strtolower(pathinfo($_FILES['imagenes']['name'][$i], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            if (!in_array($ext, $allowed)) {
                $errores[] = "Formato no permitido: {$_FILES['imagenes']['name'][$i]}";
                continue;
            }

            $filename = 'prop_' . $idPropiedad . '_' . uniqid() . '.' . $ext;
            $ruta = $uploadDir . $filename;
            $rutaBD = 'recursos/img/propiedades/' . $filename;

            if (move_uploaded_file($_FILES['imagenes']['tmp_name'][$i], $ruta)) {
                $stmt = $conexion->prepare("INSERT INTO tbl_imagen_propiedad (idPropiedad, vImagen) VALUES (?, ?)");
                $stmt->bind_param("is", $idPropiedad, $rutaBD);
                $stmt->execute();
                $subidos++;
            }
        }
    }

    $resultado = ['ok' => true, 'subidos' => $subidos, 'errores' => $errores];
}
?>