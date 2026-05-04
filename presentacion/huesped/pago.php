<?php
require_once '../../negocio/auth/verificar_sesion.php';
validarSesion('huesped', '../../');
require_once '../../datos/conexion.php';

// Obtener datos del POST
$idPropiedad = isset($_POST['idPropiedad']) ? intval($_POST['idPropiedad']) : 0;
$fechaInicio = isset($_POST['fechaInicio']) ? trim($_POST['fechaInicio']) : '';
$fechaFin    = isset($_POST['fechaFin'])    ? trim($_POST['fechaFin'])    : '';
$huespedes   = isset($_POST['huespedes'])   ? intval($_POST['huespedes']) : 1;

// Validar que las fechas tengan formato YYYY-MM-DD antes de procesarlas
function esFormatoFechaValido(string $fecha): bool {
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) return false;
    $d = DateTime::createFromFormat('Y-m-d', $fecha);
    return $d && $d->format('Y-m-d') === $fecha;
}

if (!esFormatoFechaValido($fechaInicio) || !esFormatoFechaValido($fechaFin)) {
    echo "<script>alert('Error: Las fechas proporcionadas no son válidas. Por favor selecciona fechas correctas.'); window.history.back();</script>";
    exit();
}

if ($idPropiedad <= 0) {
    echo "<script>alert('Error: Propiedad no válida.'); window.history.back();</script>";
    exit();
}
require_once '../../negocio/utilidades/calculadora_precios.php';

// 1. Validar fechas en el servidor (Evita manipulación de DOM)
if ($fechaInicio < date('Y-m-d')) {
    echo "<script>alert('Error: No se pueden seleccionar fechas pasadas.'); window.history.back();</script>";
    exit();
}

// 2. Validar disponibilidad inmediata
if (!validarDisponibilidad($idPropiedad, $fechaInicio, $fechaFin, $conexion)) {
    echo "<script>alert('Lo sentimos, estas fechas ya no están disponibles.'); window.location.href='home.php';</script>";
    exit();
}

// 2. Recalcular TODO desde la base de datos (Sincronización total)
$desglose = calcularPrecioEstancia($idPropiedad, $fechaInicio, $fechaFin, $conexion);

$noches = $desglose['noches'];
$totalBase = $desglose['totalBase'];
$tarifaLimpieza = $desglose['limpieza'];
$impuestos = $desglose['impuestos'];
$granTotal = $desglose['granTotal'];
$precioPromedio = $desglose['precioPromedio'];

require_once '../../negocio/huesped/pago_view.php';

// Consultar detalles para el resumen
$prop = getPropertyPaymentDetails($idPropiedad, $conexion);

// Imagen principal
$mainImage = getPropertyMainImage($idPropiedad, $conexion);

// Simular usuario logueado si no hay sesión
$idUsuarioHuesped = isset($_SESSION['idUsuario']) ? $_SESSION['idUsuario'] : 2; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmar y Pagar | Estancias Digitales</title>
    <link rel="stylesheet" href="../../recursos/css/variables.css">
    <link rel="stylesheet" href="../../recursos/css/main.css">
    <link rel="stylesheet" href="../../recursos/css/components/navbar.css?v=1">
    <link rel="stylesheet" href="../../recursos/css/huesped/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body style="background: white;">
    <?php include '../../recursos/navbar.php'; ?>

    <div class="reservation-container" style="max-width: 1400px;">
        <div class="payment-title-group">
            <div class="btn-back-circled" onclick="history.back()"><i class="fa-solid fa-chevron-left"></i></div>
            <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -1.5px;">Confirmar y pagar</h1>
        </div>

        <div class="payment-layout">
            <section>
                <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 2rem;">Total a pagar</h2>
                
                            <div class="payment-option-card" id="opt-full">
                    <div style="display: flex; gap: 1.5rem;">
                        <div>
                            <strong style="font-size: 1.1rem;">Pago total</strong>
                            <p style="font-size: 14px; color: #64748b; margin-top: 4px;">
                                Paga el total ahora y olvídate de cargos adicionales durante tu estancia.
                            </p>
                        </div>
                    </div>

                    <strong style="font-size: 1.25rem;">
                        $<?php echo number_format($granTotal, 2); ?>
                    </strong>
                </div>

                <div class="tonal-card" style="margin-top: 3rem; background: #f8f9fa;">
                    <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1rem;">Políticas de pago</h3>
                    <p style="font-size: 13.5px; color: #64748b; line-height: 1.6;">
                        Tu pago está protegido bajo nuestro sistema de seguridad bancaria. 
                        Los anticipos no son reembolsables si se cancela dentro de los 7 días previos a la estancia. 
                        Al confirmar, aceptas nuestros términos de servicio y políticas de cancelación.
                    </p>
                    <div style="display: flex; align-items: center; gap: 1rem; margin-top: 1.5rem; color: #008a60; font-weight: 700; font-size: 14px;">
                        <i class="fa-solid fa-shield-check"></i> Pago seguro y verificado por Estancias Digitales
                    </div>
                </div>

                <button class="btn btn-primary" id="btn-submit-pay" onclick="procesarPago()" style="width: 100%; justify-content: center; padding: 1.5rem; margin-top: 3rem; font-size: 1.1rem; font-weight: 800;">
                    Confirmar y Pagar $<?php echo number_format($granTotal, 2); ?>
                </button>
            </section>

            <aside>
                <div class="summary-sidebar-v2">
                    <div class="preview-box">
                        <div class="preview-img"><img src="<?php echo htmlspecialchars($mainImage); ?>"></div>
                        <div>
                            <span style="font-size: 11px; font-weight: 800; color: #999; text-transform: uppercase;"><?php echo htmlspecialchars($prop['tipo']); ?></span>
                            <h3 style="font-size: 15px; font-weight: 700; margin-top: 4px;"><?php echo htmlspecialchars($prop['vNombre']); ?></h3>
                            <div style="font-size: 13px; margin-top: 8px;"><i class="fa-solid fa-star"></i> 4.98 <span style="color: #999;">(Excelente)</span></div>
                        </div>
                    </div>

                    <div style="margin-bottom: 2rem; border-bottom: 1px solid #eee; padding-bottom: 2rem;">
                        <h4 style="font-size: 12px; font-weight: 800; text-transform: uppercase; margin-bottom: 1.5rem;">Tu estancia</h4>
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                            <div>
                                <span style="display:block; font-size: 13px; font-weight: 700;">Fechas</span>
                                <span style="font-size: 13px; color: #64748b;"><?php echo date('d M', strtotime($fechaInicio)) . ' - ' . date('d M, Y', strtotime($fechaFin)); ?></span>
                            </div>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div>
                                <span style="display:block; font-size: 13px; font-weight: 700;">Huéspedes</span>
                                <span style="font-size: 13px; color: #64748b;"><?php echo $huespedes; ?> huésped<?php echo $huespedes>1?'es':''; ?></span>
                            </div>
                        </div>
                    </div>

                    <div id="summary-details">
                        <h4 style="font-size: 12px; font-weight: 800; text-transform: uppercase; margin-bottom: 1.5rem;">Desglose por noche</h4>
                        
                        <?php foreach ($desglose['desgloseNoches'] as $n): ?>
                            <div class="price-row" style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 14px;">
                                <span style="color: #64748b;"><?php echo date('d M Y', strtotime($n['fecha'])); ?><?php echo $n['esEspecial'] ? ' <small style="color:var(--primary); font-weight:800;">(Tarifa especial)</small>' : ''; ?></span>
                                <span style="font-weight: 600;">$<?php echo number_format($n['precio'], 2); ?></span>
                            </div>
                        <?php endforeach; ?>

                        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #eee;">
                            <div class="price-row" style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                                <span style="font-weight: 700;">Subtotal noches</span>
                                <span style="font-weight: 700;">$<?php echo number_format($totalBase, 2); ?></span>
                            </div>
                            <div class="price-row" style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                                <span>Tarifa de limpieza</span>
                                <span>$<?php echo number_format($tarifaLimpieza, 2); ?></span>
                            </div>
                            <div class="price-row" style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                                <span>Impuestos (16%)</span>
                                <span>$<?php echo number_format($impuestos, 2); ?></span>
                            </div>

                            <div class="price-row" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #000; font-size: 1.25rem; font-weight: 800; display: flex; justify-content: space-between;">
                                <span>Total (MXN)</span>
                                <span id="total-amount" style="color: var(--primary);">$<?php echo number_format($granTotal, 2); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <script>
        // Variables del servidor — inyectadas para uso en pago.js
        window.PAGO_DATA = {
            granTotal:   <?php echo $granTotal; ?>,
            idPropiedad: '<?php echo $idPropiedad; ?>',
            idUsuario:   '<?php echo $idUsuarioHuesped; ?>',
            fechaInicio: '<?php echo $fechaInicio; ?>',
            fechaFin:    '<?php echo $fechaFin; ?>',
            huespedes:   '<?php echo $huespedes; ?>'
        };
    </script>
    <script src="../../recursos/js/huesped/pago.js"></script>

</body>
</html>
