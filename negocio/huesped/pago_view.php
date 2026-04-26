<?php
// negocio/huesped/pago_view.php

function getPropertyPaymentDetails($idPropiedad, $conexion) {
    // Consultar detalles para el resumen
    $sql = "SELECT p.*, tp.vNombreCategoria as tipo FROM tbl_propiedad p 
            LEFT JOIN tbl_tipo_propiedad tp ON p.idTipoPropiedad = tp.idTipoPropiedad
            WHERE idPropiedad = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $idPropiedad);
    $stmt->execute();
    $prop = $stmt->get_result()->fetch_assoc();

    return $prop;
}

function getPropertyMainImage($idPropiedad, $conexion) {
    // Imagen principal
    $sqlImg = "SELECT vImagen FROM tbl_imagen_propiedad WHERE idPropiedad = ? LIMIT 1";
    $stmtImg = $conexion->prepare($sqlImg);
    $stmtImg->bind_param("i", $idPropiedad);
    $stmtImg->execute();
    $imgRow = $stmtImg->get_result()->fetch_assoc();
    
    return $imgRow ? $imgRow['vImagen'] : "https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=600&q=80";
}
