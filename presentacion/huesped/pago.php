<?php
require_once '../../negocio/auth/verificar_sesion.php';
validarSesion('huesped', '../../');
require_once '../../datos/conexion.php';

// Obtener datos del POST
$idPropiedad = isset($_POST['idPropiedad']) ? intval($_POST['idPropiedad']) : 0;
$fechaInicio = isset($_POST['fechaInicio']) ? $_POST['fechaInicio'] : '';
$fechaFin = isset($_POST['fechaFin']) ? $_POST['fechaFin'] : '';
$huespedes = isset($_POST['huespedes']) ? intval($_POST['huespedes']) : 1;
$totalBase = isset($_POST['total']) ? floatval($_POST['total']) : 0;
$noches = isset($_POST['noches']) ? intval($_POST['noches']) : 0;
$precioNoche = isset($_POST['precioNoche']) ? floatval($_POST['precioNoche']) : 0;

if ($idPropiedad <= 0 || empty($fechaInicio) || empty($fechaFin)) {
    header("Location: home.php");
    exit();
}

// Consultar detalles para el resumen
$sql = "SELECT p.*, tp.vNombreCategoria as tipo FROM tbl_propiedad p 
        LEFT JOIN tbl_tipo_propiedad tp ON p.idTipoPropiedad = tp.idTipoPropiedad
        WHERE idPropiedad = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $idPropiedad);
$stmt->execute();
$prop = $stmt->get_result()->fetch_assoc();

// Imagen principal
$sqlImg = "SELECT vImagen FROM tbl_imagen_propiedad WHERE idPropiedad = ? LIMIT 1";
$stmtImg = $conexion->prepare($sqlImg);
$stmtImg->bind_param("i", $idPropiedad);
$stmtImg->execute();
$imgRow = $stmtImg->get_result()->fetch_assoc();
$mainImage = $imgRow ? $imgRow['vImagen'] : "https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=600&q=80";

$tarifaLimpieza = 1200;
$impuestos = ($precioNoche * $noches) * 0.16;
$granTotal = $totalBase + $impuestos;

// Simular usuario logueado si no hay sesión
$idUsuarioHuesped = isset($_SESSION['idUsuario']) ? $_SESSION['idUsuario'] : 2; // ID 2 por defecto para pruebas
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
                            <div style="font-size: 13px; margin-top: 8px;"><i class="fa-solid fa-star"></i> 4.98 <span style="color: #999;">(Exclente)</span></div>
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
                        <h4 style="font-size: 12px; font-weight: 800; text-transform: uppercase; margin-bottom: 1.5rem;">Resumen de precios</h4>
                        <div class="price-row" style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                            <span>$<?php echo number_format($precioNoche, 2); ?> x <?php echo $noches; ?> noches</span>
                            <span>$<?php echo number_format($precioNoche * $noches, 2); ?></span>
                        </div>
                        <div class="price-row" style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                            <span>Tarifa de limpieza</span>
                            <span>$<?php echo number_format($tarifaLimpieza, 2); ?></span>
                        </div>
                        <div class="price-row" style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                            <span>Impuestos</span>
                            <span>$<?php echo number_format($impuestos, 2); ?></span>
                        </div>

                        <div class="price-row" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #000; font-size: 1.25rem; font-weight: 800; display: flex; justify-content: space-between;">
                            <span>Total (MXN)</span>
                            <span id="total-amount">$<?php echo number_format($granTotal, 2); ?></span>
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
