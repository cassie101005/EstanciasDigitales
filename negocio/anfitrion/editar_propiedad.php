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
    require_once '../../negocio/utilidades/seguridad.php';
    if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        $resultado = ['ok' => false, 'error' => 'Error de seguridad (CSRF).'];
        return;
    }

    $idPropiedad = intval($_POST['idPropiedad'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');

    // Primero validar que la propiedad pertenece al usuario (IDOR Protection)
    $checkOwner = $queriesEdicion->obtenerPropiedadPorId($idPropiedad, $idUsuario);
    if ($checkOwner->num_rows === 0) {
        http_response_code(403);
        $resultado = ['ok' => false, 'error' => 'No tienes permiso para editar esta propiedad.'];
        return;
    }

    // VALIDACIÓN OBLIGATORIA: Mínimo 3 imágenes
    $resImgs = $queriesEdicion->obtenerImagenes($idPropiedad);
    $totalActuales = $resImgs->num_rows;
    $totalNuevas = 0;
    if (!empty($_FILES['imagenes']['name'][0])) {
        foreach ($_FILES['imagenes']['error'] as $err) {
            if ($err === UPLOAD_ERR_OK) $totalNuevas++;
        }
    }

    if (($totalActuales + $totalNuevas) < 3) {
        http_response_code(400);
        $resultado = ['ok' => false, 'error' => 'Debes mantener al menos 3 imágenes para guardar la propiedad.'];
        return;
    }

    if (!preg_match('/^[a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ]+$/u', $nombre)) {
        $resultado = ['ok' => false, 'error' => 'El nombre de la propiedad solo puede contener letras y números, sin caracteres especiales.'];
        return;
    }
    // ... (rest of the logic remains)
    if (!preg_match('/[a-zA-ZáéíóúÁÉÍÓÚñÑ]/u', $nombre)) {
        $resultado = ['ok' => false, 'error' => 'El nombre de la propiedad debe contener al menos una letra.'];
        return;
    }
    $idTipoPropiedad = intval($_POST['idTipoPropiedad'] ?? 0);
    $precioNoche = floatval($_POST['precioNoche'] ?? 0);
    $tarifaLimpieza = trim($_POST['dTarifaLimpieza'] ?? ''); 
    $capacidadHuespedes = intval($_POST['capacidadHuespedes'] ?? 0);
    $numeroHabitaciones = intval($_POST['numeroHabitaciones'] ?? 0);
    $numeroBanos = intval($_POST['numeroBanos'] ?? 0);
    $idCiudad = intval($_POST['idCiudad'] ?? 0);
    $direccion = trim($_POST['direccion'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    if (!preg_match('/^\d{1,5}(\.\d{1,2})?$/', $tarifaLimpieza)) {
        http_response_code(400);
        $resultado = ['ok' => false, 'error' => 'Tarifa de limpieza inválida. Solo números positivos, máximo 5 dígitos enteros y 2 decimales.'];
        return;
    }

    $servicios = json_decode($_POST['servicios'] ?? '[]', true) ?: [];
    $reglas = json_decode($_POST['reglas'] ?? '[]', true) ?: [];
    $politicas = json_decode($_POST['politicas'] ?? '[]', true) ?: [];

    if ($idPropiedad <= 0 || empty($nombre) || $idTipoPropiedad <= 0 || $precioNoche <= 0) {
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
        'tarifaLimpieza' => $tarifaLimpieza,
        'descripcion' => $descripcion,
        'capacidadHuespedes' => $capacidadHuespedes,
        'numeroHabitaciones' => $numeroHabitaciones,
        'numeroBanos' => $numeroBanos
    ];

    $affectedRows = $queriesEdicion->actualizarPropiedad($datos, $idUsuario);
    if ($affectedRows >= 0) {
        $queriesEdicion->limpiarRelaciones($idPropiedad);
        foreach ($servicios as $idS) $queriesRegistro->insertarServicioPropiedad(intval($idS), $idPropiedad);
        foreach ($reglas as $idR) $queriesRegistro->insertarReglaPropiedad(intval($idR), $idPropiedad);
        foreach ($politicas as $idP) $queriesRegistro->insertarPoliticaPropiedad(intval($idP), $idPropiedad);

        $resultado = ['ok' => true, 'mensaje' => 'Propiedad actualizada correctamente.'];
    } else {
        $resultado = ['ok' => false, 'error' => 'No se pudo actualizar la propiedad.'];
    }
}

// ── ELIMINAR IMAGEN ──────────────────────────────────────────────────────────
elseif ($accion === 'eliminar_imagen') {
    $idImagen = intval($_POST['idImagen'] ?? 0);
    $idPropiedad = intval($_POST['idPropiedad'] ?? 0);

    // Validar propiedad y anfitrión
    $checkOwner = $queriesEdicion->obtenerPropiedadPorId($idPropiedad, $idUsuario);
    if ($checkOwner->num_rows === 0) {
        http_response_code(403);
        $resultado = ['ok' => false, 'error' => 'No tienes permiso para modificar esta propiedad.'];
        return;
    }

    // Validar mínimo 3 imágenes (en DB + nuevas seleccionadas en el frontend)
    $totalNuevas = intval($_POST['totalNuevas'] ?? 0);
    $resImgs = $queriesEdicion->obtenerImagenes($idPropiedad);
    if (($resImgs->num_rows + $totalNuevas) <= 3) {
        http_response_code(400);
        $resultado = ['ok' => false, 'error' => 'No puedes tener menos de 3 imágenes. Si deseas cambiar esta foto, selecciona primero las fotos nuevas.'];
        return;
    }

    if ($queriesEdicion->eliminarImagen($idImagen, $idPropiedad)) {
        $resultado = ['ok' => true];
    } else {
        $resultado = ['ok' => false, 'error' => 'No se pudo eliminar la imagen.'];
    }
}

// ── SUBIR IMÁGENES NUEVAS ────────────────────────────────────────────────────
elseif ($accion === 'subir_imagenes') {
    require_once '../../negocio/utilidades/seguridad.php';
    if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        $resultado = ['ok' => false, 'error' => 'Error de seguridad (CSRF).'];
        return;
    }

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