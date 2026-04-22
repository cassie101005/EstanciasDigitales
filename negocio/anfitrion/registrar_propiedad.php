<?php
require_once '../../datos/conexion.php';

// 1. Guardar propiedad principal
$sql = "INSERT INTO tbl_propiedad (
            idCiudad,
            idUsuario,
            idTipoPropiedad,
            vNombre,
            vDireccion,
            dPrecioNoche,
            vDescripcion,
            vEspecificaciones,
            iCapacidadHuespedes,
            iNumeroHabitaciones
        ) VALUES (
            '$idCiudad',
            '$idUsuario',
            '$idTipoPropiedad',
            '$nombre',
            '$direccion',
            '$precioNoche',
            '$descripcion',
            '$especificaciones',
            '$capacidadHuespedes',
            '$numeroHabitaciones'
        )";

if ($conexion->query($sql)) {

    // 2. Obtener el ID de la propiedad creada
    $idPropiedad = $conexion->insert_id;

    // 3. Guardar servicios
    if (!empty($servicios)) {
        foreach ($servicios as $idServicio) {
            $sqlServicio = "INSERT INTO tbl_propiedad_servicios (idServicio, idPropiedad)
                            VALUES ('$idServicio', '$idPropiedad')";
            $conexion->query($sqlServicio);
        }
    }

    // 4. Guardar reglas
    if (!empty($reglas)) {
        foreach ($reglas as $idRegla) {
            $sqlRegla = "INSERT INTO tbl_propiedad_regla (idPropiedad, idRegla)
                         VALUES ('$idPropiedad', '$idRegla')";
            $conexion->query($sqlRegla);
        }
    }

    // 5. Guardar políticas
    if (!empty($politicas)) {
        foreach ($politicas as $idPolitica) {
            $sqlPolitica = "INSERT INTO tbl_propiedad_politica (idPolitica, idPropiedad)
                            VALUES ('$idPolitica', '$idPropiedad')";
            $conexion->query($sqlPolitica);
        }
    }

    // 6. Resultado final
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