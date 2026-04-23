<?php
require_once '../../datos/conexion.php';
require_once '../../datos/anfitrion/queries_registro_propiedad.php';

$queriesRegistro = new QueriesRegistroPropiedad($conexion);

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
    // Process Regla Extra if there is one
    if (!empty($reglaExtra)) {
        if ($queriesRegistro->insertarReglaPersonalizada($reglaExtra)) {
            $reglas[] = $conexion->insert_id;
        }
    }

    // Preparar datos para inserción
    $datosPropiedad = [
        'idCiudad' => $idCiudad,
        'idUsuario' => $idUsuario,
        'idTipoPropiedad' => $idTipoPropiedad,
        'nombre' => $nombre,
        'direccion' => $direccion,
        'precioNoche' => $precioNoche,
        'descripcion' => $descripcion,
        'especificaciones' => $especificaciones,
        'capacidadHuespedes' => $capacidadHuespedes,
        'numeroHabitaciones' => $numeroHabitaciones
    ];

    if ($queriesRegistro->insertarPropiedad($datosPropiedad)) {
        $idPropiedad = $conexion->insert_id;

        // Insertar servicios
        if (!empty($servicios)) {
            foreach ($servicios as $idServicio) {
                $queriesRegistro->insertarServicioPropiedad($idServicio, $idPropiedad);
            }
        }

        // Insertar reglas
        if (!empty($reglas)) {
            foreach ($reglas as $idRegla) {
                $queriesRegistro->insertarReglaPropiedad($idRegla, $idPropiedad);
            }
        }

        // Insertar políticas
        if (!empty($politicas)) {
            foreach ($politicas as $idPolitica) {
                $queriesRegistro->insertarPoliticaPropiedad($idPolitica, $idPropiedad);
            }
        }

        $resultado = [
            'ok' => true,
            'mensaje' => 'Propiedad registrada correctamente.',
            'idPropiedad' => $idPropiedad
        ];
    } else {
        $resultado = [
            'error' => 'No se pudo registrar la propiedad.'
        ];
    }
}

// --------------------
// POST: SUBIR IMAGENES
// --------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $accion === 'subir_imagenes') {
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