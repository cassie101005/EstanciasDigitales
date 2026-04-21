<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmar y Pagar | Estancias Digitales</title>
    <link rel="stylesheet" href="../../recursos/css/variables.css">
    <link rel="stylesheet" href="../../recursos/css/main.css">
    <link rel="stylesheet" href="../../recursos/css/huesped/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 2rem;">Elige cómo pagar</h2>
                
                <!-- Payment Option: FULL -->
                <div class="payment-option-card active" id="opt-full" onclick="selectPayment('full')">
                    <div style="display: flex; gap: 1.5rem;">
                         <input type="radio" name="pay_method" checked style="margin-top: 5px; accent-color: var(--primary);">
                         <div>
                             <strong style="font-size: 1.1rem;">Pago total</strong>
                             <p style="font-size: 14px; color: #64748b; margin-top: 4px;">Paga el total ahora y olvídate de cargos adicionales durante tu estancia.</p>
                         </div>
                    </div>
                    <strong style="font-size: 1.25rem;">$14,500.00</strong>
                </div>

                <!-- Payment Option: ANTICIPO (ABONOS) -->
                <div class="payment-option-card" id="opt-part" onclick="selectPayment('part')">
                    <div style="display: flex; gap: 1.5rem;">
                         <input type="radio" name="pay_method" style="margin-top: 5px; accent-color: var(--primary);">
                         <div>
                             <strong style="font-size: 1.1rem;">Anticipo + Saldo (Abonos)</strong>
                             <p style="font-size: 14px; color: #64748b; margin-top: 4px;">Reserva con un anticipo y liquida el resto antes de tu llegada.</p>
                         </div>
                    </div>
                    <strong style="font-size: 1.25rem;">$4,350.00 <span style="font-size: 11px; font-weight: 400; color: #999;">hoy</span></strong>
                </div>

                <!-- Payment Details Form -->
                <div style="margin-top: 4rem; padding-top: 3rem; border-top: 1px solid #eee;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                        <h2 style="font-size: 1.5rem; font-weight: 800;">Detalles del pago</h2>
                        <div style="display: flex; gap: 0.5rem; color: #ddd; font-size: 1.5rem;">
                            <i class="fa-brands fa-cc-visa"></i>
                            <i class="fa-brands fa-cc-mastercard"></i>
                        </div>
                    </div>

                    <div class="card-form-grid">
                        <div class="input-group-v2" style="grid-column: span 2;">
                            <label>Nombre en la tarjeta</label>
                            <input type="text" placeholder="Ej. Juan Pérez">
                        </div>
                        <div class="input-group-v2" style="grid-column: span 2;">
                            <label>Número de tarjeta</label>
                            <div style="position: relative;">
                                <input type="text" placeholder="0000 0000 0000 0000" style="width: 100%; box-sizing: border-box;">
                                <i class="fa-regular fa-credit-card" style="position: absolute; right: 1.5rem; top: 1.25rem; opacity: 0.3;"></i>
                            </div>
                        </div>
                        <div class="input-group-v2">
                            <label>Vencimiento</label>
                            <input type="text" placeholder="MM / AA">
                        </div>
                        <div class="input-group-v2">
                            <label>CVV</label>
                            <input type="text" placeholder="123">
                        </div>
                    </div>
                </div>

                <div class="tonal-card" style="margin-top: 3rem; background: #f8f9fa;">
                    <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1rem;">Políticas de pago</h3>
                    <p style="font-size: 13.5px; color: #64748b; line-height: 1.6;">
                        Tu pago está protegido bajo nuestro sistema de seguridad bancaria. 
                        Los anticipos no son reembolsables si se cancela dentro de los 7 días previos a la estancia. 
                        Al confirmar, aceptas nuestros términos de servicio y políticas de cancelación.
                    </p>
                    <div style="display: flex; align-items: center; gap: 1rem; margin-top: 1.5rem; color: #008a60; font-weight: 700; font-size: 14px;">
                        <i class="fa-solid fa-shield-check"></i> Pago seguro y verificado por AirCover
                    </div>
                </div>

                <button class="btn btn-primary" id="btn-submit-pay" style="width: 100%; justify-content: center; padding: 1.5rem; margin-top: 3rem; font-size: 1.1rem;">Confirmar y Pagar $14,500.00</button>
            </section>

            <aside>
                <div class="summary-sidebar-v2">
                    <div class="preview-box">
                        <div class="preview-img"><img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=600&q=80"></div>
                        <div>
                            <span style="font-size: 11px; font-weight: 800; color: #999; text-transform: uppercase;">Villa Paraíso</span>
                            <h3 style="font-size: 15px; font-weight: 700; margin-top: 4px;">Residencia de Élite frente al Mar</h3>
                            <div style="font-size: 13px; margin-top: 8px;"><i class="fa-solid fa-star"></i> 4.98 <span style="color: #999;">(128 reseñas)</span></div>
                        </div>
                    </div>

                    <div style="margin-bottom: 2rem; border-bottom: 1px solid #eee; padding-bottom: 2rem;">
                        <h4 style="font-size: 12px; font-weight: 800; text-transform: uppercase; margin-bottom: 1.5rem;">Tu estancia</h4>
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                            <div>
                                <span style="display:block; font-size: 13px; font-weight: 700;">Fechas</span>
                                <span style="font-size: 13px; color: #64748b;">12 Nov - 17 Nov, 2024</span>
                            </div>
                            <span style="font-size: 13px; font-weight: 700; text-decoration: underline; cursor: pointer;">Editar</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div>
                                <span style="display:block; font-size: 13px; font-weight: 700;">Huéspedes</span>
                                <span style="font-size: 13px; color: #64748b;">4 adultos</span>
                            </div>
                            <span style="font-size: 13px; font-weight: 700; text-decoration: underline; cursor: pointer;">Editar</span>
                        </div>
                    </div>

                    <div id="summary-details">
                        <h4 style="font-size: 12px; font-weight: 800; text-transform: uppercase; margin-bottom: 1.5rem;">Resumen de precios</h4>
                        <div class="price-row">
                            <span>$2,900.00 x 5 noches</span>
                            <span>$14,500.00</span>
                        </div>
                        <div class="price-row">
                            <span>Tarifa de limpieza</span>
                            <span>$1,200.00</span>
                        </div>
                        <div class="price-row">
                            <span>Impuestos</span>
                            <span>$2,450.00</span>
                        </div>
                        
                        <div id="deposit-details" style="display: none; border-top: 1px dashed #ddd; margin-top: 1.5rem; padding-top: 1.5rem;">
                             <div class="price-row" style="color: var(--primary); font-weight: 700;">
                                <span>Monto de Anticipo</span>
                                <span>$4,350.00</span>
                            </div>
                            <div class="price-row" style="color: #e11d48; font-weight: 700;">
                                <span>Saldo Pendiente</span>
                                <span>$13,800.00</span>
                            </div>
                            <div style="font-size: 12px; color: #94a3b8; text-align: right; margin-top: 0.5rem;">
                                Fecha límite: 05 de Noviembre, 2024
                            </div>
                        </div>

                        <div class="price-row" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #000; font-size: 1.25rem; font-weight: 800;">
                            <span>Total (MXN)</span>
                            <span id="total-amount">$18,150.00</span>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <script>
        function selectPayment(type) {
            const optFull = document.getElementById('opt-full');
            const optPart = document.getElementById('opt-part');
            const depositDiv = document.getElementById('deposit-details');
            const btnPay = document.getElementById('btn-submit-pay');

            if (type === 'full') {
                optFull.classList.add('active');
                optPart.classList.remove('active');
                optFull.querySelector('input').checked = true;
                depositDiv.style.display = 'none';
                btnPay.innerText = 'Confirmar y Pagar $18,150.00';
            } else {
                optPart.classList.add('active');
                optFull.classList.remove('active');
                optPart.querySelector('input').checked = true;
                depositDiv.style.display = 'block';
                btnPay.innerText = 'Confirmar y Pagar $4,350.00';
            }
        }
    </script>
</body>
</html>
