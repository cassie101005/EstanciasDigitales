<?php
require_once '../../datos/conexion.php';
require_once '../../datos/anfitrion/queries_registro_propiedad.php';

$queriesRegistro = new QueriesRegistroPropiedad($conexion);


//CODIGO QUE MANEJA ENDPOINTS JUNTOS EN UNA SOLA PESTAÑA Y USANDO LA MISMA API

// --------------------
// GET: TIPOS
// --------------------
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $accion === 'tipos') {
    $consulta = $queriesRegistro->obtenerTiposPropiedad();
    $tipos = [];

    if ($consulta && $consulta->num_rows > 0) {
        while ($fila = $consulta->fetch_assoc()) {
            $tipos[] = $fila;
        }
        $resultado = ['ok' => true, 'tipos' => $tipos];
    } else {
        $resultado = ['ok' => false, 'mensaje' => 'No hay tipos de propiedad.'];
    }
}

// --------------------
// GET: PAISES
// --------------------
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $accion === 'paises') {
    $consulta = $queriesRegistro->obtenerPaises();
    $paises = [];

    if ($consulta && $consulta->num_rows > 0) {
        while ($fila = $consulta->fetch_assoc()) {
            $paises[] = $fila;
        }
        $resultado = ['ok' => true, 'paises' => $paises];
    } else {
        $resultado = ['ok' => false, 'mensaje' => 'No hay países.'];
    }
}

// --------------------
// GET: ESTADOS
// --------------------
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $accion === 'estados') {
    $idPais = intval($_GET['idPais'] ?? 0);

    if ($idPais <= 0) {
        $resultado = ['ok' => false, 'mensaje' => 'ID de país inválido.'];
    } else {
        $consulta = $queriesRegistro->obtenerEstadosPorPais($idPais);
        $estados = [];

        if ($consulta && $consulta->num_rows > 0) {
            while ($fila = $consulta->fetch_assoc()) {
                $estados[] = $fila;
            }
            $resultado = ['ok' => true, 'estados' => $estados];
        } else {
            $resultado = ['ok' => false, 'mensaje' => 'No hay estados para este país.'];
        }
    }
}

// --------------------
// GET: CIUDADES
// --------------------
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $accion === 'ciudades') {
    $idEstado = intval($_GET['idEstado'] ?? 0);

    if ($idEstado <= 0) {
        $resultado = ['ok' => false, 'mensaje' => 'ID de estado inválido.'];
    } else {
        $consulta = $queriesRegistro->obtenerCiudadesPorEstado($idEstado);
        $ciudades = [];

        if ($consulta && $consulta->num_rows > 0) {
            while ($fila = $consulta->fetch_assoc()) {
                $ciudades[] = $fila;
            }
            $resultado = ['ok' => true, 'ciudades' => $ciudades];
        } else {
            $resultado = ['ok' => false, 'mensaje' => 'No hay ciudades para este estado.'];
        }
    }
}

// --------------------
// GET: SERVICIOS
// --------------------
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $accion === 'servicios') {
    $consulta = $queriesRegistro->obtenerServicios();
    $serviciosLista = [];

    if ($consulta && $consulta->num_rows > 0) {
        while ($fila = $consulta->fetch_assoc()) {
            $serviciosLista[] = $fila;
        }
        $resultado = ['ok' => true, 'servicios' => $serviciosLista];
    } else {
        $resultado = ['ok' => false, 'mensaje' => 'No hay servicios.'];
    }
}

// --------------------
// GET: REGLAS
// --------------------
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $accion === 'reglas') {
    $consulta = $queriesRegistro->obtenerReglas();
    $reglasLista = [];

    if ($consulta && $consulta->num_rows > 0) {
        while ($fila = $consulta->fetch_assoc()) {
            $reglasLista[] = $fila;
        }
        $resultado = ['ok' => true, 'reglas' => $reglasLista];
    } else {
        $resultado = ['ok' => false, 'mensaje' => 'No hay reglas.'];
    }
}

// --------------------
// GET: POLITICAS
// --------------------
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $accion === 'politicas') {
    $consulta = $queriesRegistro->obtenerPoliticas();
    $politicasLista = [];

    if ($consulta && $consulta->num_rows > 0) {
        while ($fila = $consulta->fetch_assoc()) {
            $politicasLista[] = $fila;
        }
        $resultado = ['ok' => true, 'politicas' => $politicasLista];
    } else {
        $resultado = ['ok' => false, 'mensaje' => 'No hay politicas.'];
    }
}

// --------------------
// POST: GUARDAR PROPIEDAD
// --------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $accion === 'guardar') {
    // 1. Validar imágenes (Mínimo 3 reales)
    $imagenesValidas = 0;
    $fotosAProcesar = [];
    
    if (isset($_FILES['imagenes']) && is_array($_FILES['imagenes']['name'])) {
        foreach ($_FILES['imagenes']['name'] as $index => $nombreImg) {
            if (!empty($nombreImg) && $_FILES['imagenes']['error'][$index] === UPLOAD_ERR_OK) {
                $nombreLower = strtolower($nombreImg);
                $placeholders = ['default.jpg', 'placeholder.jpg', 'sin-imagen.jpg', 'imagen-default.png', 'propiedad-default.png'];
                if (!in_array($nombreLower, $placeholders)) {
                    $imagenesValidas++;
                    $fotosAProcesar[] = [
                        'tmp' => $_FILES['imagenes']['tmp_name'][$index],
                        'name' => $_FILES['imagenes']['name'][$index]
                    ];
                }
            }
        }
    }

    if ($imagenesValidas < 3) {
        $resultado = [
            'ok' => false,
            'error' => 'Debes subir mínimo 3 fotos reales para registrar la propiedad.'
        ];
        return;
    }

    // 2. Insertar Propiedad
    $idCiudad = intval($_POST['idCiudad'] ?? 0);
    $idTipoPropiedad = intval($_POST['idTipoPropiedad'] ?? 0);
    $nombre = htmlspecialchars(trim($_POST['nombre'] ?? ''), ENT_QUOTES, 'UTF-8');
    $direccion = htmlspecialchars(trim($_POST['direccion'] ?? ''), ENT_QUOTES, 'UTF-8');
    $precioNoche = floatval($_POST['precioNoche'] ?? 0);
    $tarifaLimpieza = trim($_POST['dTarifaLimpieza'] ?? ''); // Traer como string para validar regex exacto
    $descripcion = htmlspecialchars(trim($_POST['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8');
    $capacidadHuespedes = intval($_POST['capacidadHuespedes'] ?? 0);
    $numeroHabitaciones = intval($_POST['numeroHabitaciones'] ?? 0);
    $numeroBanos = intval($_POST['numeroBanos'] ?? 0);

    // Validación estricta de Tarifa de Limpieza
    if (!preg_match('/^\d{1,5}(\.\d{1,2})?$/', $tarifaLimpieza)) {
        http_response_code(400);
        $resultado = ['ok' => false, 'error' => 'Tarifa de limpieza inválida. Solo números positivos, máximo 5 dígitos enteros y 2 decimales.'];
        return;
    }

    $servicios = array_unique(json_decode($_POST['servicios'] ?? '[]', true) ?: []);
    $reglas = array_unique(json_decode($_POST['reglas'] ?? '[]', true) ?: []);
    $politicas = array_unique(json_decode($_POST['politicas'] ?? '[]', true) ?: []);

    $datosPropiedad = [
        'idCiudad' => $idCiudad,
        'idUsuario' => $idUsuario,
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

    if ($queriesRegistro->insertarPropiedad($datosPropiedad)) {
        $idPropiedad = $conexion->insert_id;

        // Insertar servicios, reglas, políticas
        if (!empty($servicios)) foreach ($servicios as $idS) $queriesRegistro->insertarServicioPropiedad($idS, $idPropiedad);
        if (!empty($reglas)) foreach ($reglas as $idR) $queriesRegistro->insertarReglaPropiedad($idR, $idPropiedad);
        if (!empty($politicas)) foreach ($politicas as $idP) $queriesRegistro->insertarPoliticaPropiedad($idP, $idPropiedad);

        // 3. Guardar Imágenes
        $carpeta = '../../recursos/img/propiedades/';
        if (!file_exists($carpeta)) mkdir($carpeta, 0777, true);

        foreach ($fotosAProcesar as $foto) {
            $nombreArchivo = time() . '_' . preg_replace("/[^a-zA-Z0-9._-]/", "_", $foto['name']);
            $rutaDestino = $carpeta . $nombreArchivo;
            $rutaGuardar = 'recursos/img/propiedades/' . $nombreArchivo;

            if (move_uploaded_file($foto['tmp'], $rutaDestino)) {
                $queriesRegistro->insertarImagenPropiedad($idPropiedad, $rutaGuardar);
            }
        }

        $resultado = [
            'ok' => true,
            'mensaje' => 'Propiedad registrada con éxito.',
            'idPropiedad' => $idPropiedad
        ];

        // Notificar
        require_once '../../negocio/utilidades/notificaciones.php';
        notificarAHuespedes('propiedad', "Nueva propiedad: $nombre", "Descubre '$nombre' ahora.", "presentacion/huesped/detalle.php?id=$idPropiedad", $idPropiedad);
    } else {
        $resultado = ['ok' => false, 'error' => 'No se pudo guardar la información básica de la propiedad.'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $accion === 'subir_imagenes') {
    if (!isset($_FILES['imagenes']['name']) || count($_FILES['imagenes']['name']) < 3) {
        $resultado = [
            'ok' => false,
            'error' => 'Debes subir mínimo 3 fotos reales para registrar la propiedad.'
        ];
        return;
    }

    $carpeta = '../../recursos/img/propiedades/';

    if (!file_exists($carpeta)) {
        mkdir($carpeta, 0777, true);
    }

    $guardadas = 0;

    foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmpName) {
        $nombreArchivo = time() . '_' . $_FILES['imagenes']['name'][$key];
        $rutaDestino = $carpeta . $nombreArchivo;
        $rutaGuardar = 'recursos/img/propiedades/' . $nombreArchivo;

        if (move_uploaded_file($tmpName, $rutaDestino)) {
            if ($queriesRegistro->insertarImagenPropiedad($idPropiedad, $rutaGuardar)) {
                $guardadas++;
            }
        }
    }

    $resultado = [
        'ok' => true,
        'mensaje' => 'Imagenes guardadas correctamente.',
        'imagenes' => $guardadas
    ];
}
?>