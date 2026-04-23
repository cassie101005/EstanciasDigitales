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
    $resultado = ['ok' => true, 'eventos' => $eventos];
}
else if ($accion === 'bloquear_fechas') {
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

    // Insertar bloqueo
    if ($queriesCalendario->insertarBloqueo($idPropiedad, $fechaInicio, $fechaFin, $motivo)) {
        $resultado = ['ok' => true];
    } else {
        $resultado = ['error' => 'Error al guardar el bloqueo.'];
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

