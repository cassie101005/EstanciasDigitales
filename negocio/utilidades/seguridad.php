<?php
/**
 * Utilidades de seguridad para sanitización de datos
 * negocio/utilidades/seguridad.php
 */

/**
 * Sanitiza una cadena de texto para prevenir XSS e inyecciones básicas
 * @param string $data Cadena a sanitizar
 * @return string Cadena limpia
 */
function sanitizarEntrada($data) {
    if (is_array($data)) {
        return array_map('sanitizarEntrada', $data);
    }
    
    // Eliminar espacios en blanco adicionales
    $data = trim($data);
    
    // Eliminar etiquetas HTML y PHP para prevenir XSS
    $data = strip_tags($data);
    
    // Convertir caracteres especiales en entidades HTML
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    
    return $data;
}

/**
 * Verifica si una cadena contiene patrones sospechosos de inyección SQL
 * (Aunque se usen sentencias preparadas, esto añade una capa extra)
 */
function esSospechoso($data) {
    $patrones = [
        '/SELECT\s+.*\s+FROM/i',
        '/INSERT\s+INTO/i',
        '/UPDATE\s+.*\s+SET/i',
        '/DELETE\s+FROM/i',
        '/DROP\s+TABLE/i',
        '/UNION\s+SELECT/i',
        '/OR\s+1=1/i',
        '/--\s*$/i'
    ];
    
    foreach ($patrones as $patron) {
        if (preg_match($patron, $data)) {
            return true;
        }
    }
    
    return false;
}
?>
