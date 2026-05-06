<?php
require_once '../../datos/conexion.php';
require_once '../../datos/anfitrion/queries_calendario.php';

if (!isset($idUsuario) || !isset($accion)) {
    $resultado = ['error' => 'Acceso no permitido'];
    return;
}

$queriesCalendario = new QueriesCalendario($conexion);

if ($accion === 'obtener_eventos') {
    // Verificar que la propiedad pertenece al usuario
    $resultadoVerificacion = $queriesCalendario->verificarPropiedadUsuario($idPropiedad, $idUsuario);
    if ($resultadoVerificacion->num_rows === 0) {
        $resultado = ['error' => 'Propiedad no encontrada o no autorizada'];
        return;
    }

    $eventos = $queriesCalendario->obtenerEventosPropiedad($idPropiedad, $anio, $mes);
    $tarifas = $queriesCalendario->obtenerTarifasPropiedad($idPropiedad, $anio, $mes);
    
    $resultado = [
        'ok' => true, 
        'eventos' => $eventos,
        'tarifas' => $tarifas
    ];
}
else if ($accion === 'bloquear_fechas') {
    // Validar fechas pasadas
    $hoy = date('Y-m-d');
    if ($fechaInicio < $hoy) {
        $resultado = ['error' => 'No puedes bloquear fechas que ya pasaron.'];
        return;
    }

    // Verificar propiedad
    $resultadoVerificacion = $queriesCalendario->verificarPropiedadUsuario($idPropiedad, $idUsuario);
    if ($resultadoVerificacion->num_rows === 0) {
        $resultado = ['error' => 'Propiedad no autorizada'];
        return;
    }

    // Validar solapamiento con reservas
    $resultadoSolapamiento = $queriesCalendario->validarSolapamientoReservas($idPropiedad, $fechaInicio, $fechaFin);
    if ($resultadoSolapamiento->num_rows > 0) {
        $resultado = ['error' => 'No puedes bloquear fechas que ya están reservadas.'];
        return;
    }

    // Validar solapamiento con bloqueos existentes
    $resultadoBloqueoExistente = $queriesCalendario->validarSolapamientoBloqueos($idPropiedad, $fechaInicio, $fechaFin);
    if ($resultadoBloqueoExistente->num_rows > 0) {
        http_response_code(409);
        $resultado = ['error' => 'Ya existe un bloqueo administrativo en este rango de fechas.'];
        return;
    }

    // Insertar bloqueo
    if ($queriesCalendario->insertarBloqueo($idPropiedad, $fechaInicio, $fechaFin, $motivo)) {
        $resultado = ['ok' => true];
    } else {
        $resultado = ['error' => 'Error al guardar el bloqueo.'];
    }
}
else if ($accion === 'ajustar_tarifa') {
    // $precio ya viene definido de datos/anfitrion/calendario.php
    
    // Validar fechas pasadas
    $hoy = date('Y-m-d');
    if ($fechaInicio < $hoy) {
        $resultado = ['error' => 'No puedes ajustar tarifas en fechas que ya pasaron.'];
        return;
    }

    // Verificar propiedad
    $resultadoVerificacion = $queriesCalendario->verificarPropiedadUsuario($idPropiedad, $idUsuario);
    if ($resultadoVerificacion->num_rows === 0) {
        $resultado = ['error' => 'Propiedad no autorizada'];
        return;
    }

    // Validar solapamiento con reservas activas
    $resultadoSolapamiento = $queriesCalendario->validarSolapamientoReservas($idPropiedad, $fechaInicio, $fechaFin);
    if ($resultadoSolapamiento->num_rows > 0) {
        $resultado = ['error' => 'No puedes ajustar tarifas en fechas que ya están reservadas.'];
        return;
    }

    // Validar solapamiento con bloqueos activos
    $resultadoBloqueo = $queriesCalendario->validarSolapamientoBloqueos($idPropiedad, $fechaInicio, $fechaFin);
    if ($resultadoBloqueo->num_rows > 0) {
        $resultado = ['error' => 'No puedes ajustar tarifas en fechas que están bloqueadas.'];
        return;
    }

    // Insertar tarifa (esto internamente desactiva las anteriores que solapen)
    if ($queriesCalendario->insertarTarifa($idPropiedad, $fechaInicio, $fechaFin, $precio)) {
        $resultado = ['ok' => true];
    } else {
        $resultado = ['error' => 'Error al guardar la tarifa en la base de datos.'];
    }
}
else if ($accion === 'desbloquear') {
    // Validar propiedad
    $resultadoVerificacion = $queriesCalendario->verificarPropiedadUsuario($idPropiedad, $idUsuario);
    if ($resultadoVerificacion->num_rows === 0) {
        $resultado = ['error' => 'Propiedad no autorizada'];
        return;
    }

    if ($queriesCalendario->desbloquearFechas($idDisponibilidad, $idPropiedad)) {
        $resultado = ['ok' => true];
    } else {
        $resultado = ['error' => 'Error al desbloquear fechas.'];
    }
}

