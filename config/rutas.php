<?php
/**
 * Configuración de rutas del sistema
 * Centraliza todas las rutas para facilitar mantenimiento
 */

define('RUTA_BASE', dirname(__DIR__));

// Rutas de carpetas principales
define('RUTA_APIS', RUTA_BASE . '/apis');
define('RUTA_DATOS', RUTA_BASE . '/datos');
define('RUTA_NEGOCIO', RUTA_BASE . '/negocio');
define('RUTA_PRESENTACION', RUTA_BASE . '/presentacion');
define('RUTA_RECURSOS', RUTA_BASE . '/recursos');

// Rutas comunes
define('RUTA_CSS', RUTA_RECURSOS . '/css');
define('RUTA_JS', RUTA_RECURSOS . '/js');
define('RUTA_IMG', RUTA_RECURSOS . '/img');

// Rutas por módulo
define('RUTA_ANFITRION', RUTA_PRESENTACION . '/anfitrion');
define('RUTA_HUESPED', RUTA_PRESENTACION . '/huesped');
define('RUTA_ADMIN', RUTA_PRESENTACION . '/admin');

// URLs relativas (para uso en frontend)
define('URL_BASE', '/');
define('URL_CSS', URL_BASE . 'recursos/css/');
define('URL_JS', URL_BASE . 'recursos/js/');
define('URL_IMG', URL_BASE . 'recursos/img/');

// URLs por módulo
define('URL_ANFITRION', URL_BASE . 'presentacion/anfitrion/');
define('URL_HUESPED', URL_BASE . 'presentacion/huesped/');
define('URL_ADMIN', URL_BASE . 'presentacion/admin/');
?>