/**
 * detalle_reserva.js — Lógica del Detalle de Reserva (Huésped)
 * recursos/js/huesped/detalle_reserva.js
 * Requiere window.RESERVA_DATA = { fechaInicio, totalReserva } inyectado por detalle_reserva.php
 */

let cancelacionPendiente = null;

function cancelarReserva(idReserva, role, idUsuario) {
    cancelacionPendiente = { idReserva, role, idUsuario };
    document.getElementById('motivoCancelacion').value = '';

    const { fechaRegistro: fechaRegistroStr, totalReserva } = window.RESERVA_DATA;

    // Calcular reembolso en tiempo real basado en la creación
    let diffHoras = 999; // Default: >24h (se cobra comisión)
    console.log("DEBUG GUEST: totalReserva:", totalReserva);
    console.log("DEBUG GUEST: fechaRegistroStr:", fechaRegistroStr);

    if (fechaRegistroStr && fechaRegistroStr !== 'null' && fechaRegistroStr !== '') {
        // Limpiar la fecha de caracteres extraños y asegurar formato YYYY/MM/DD para compatibilidad
        const cleanDate = fechaRegistroStr.replace(/-/g, '/').replace('T', ' ');
        const fechaRegistro = new Date(cleanDate);
        const ahora = new Date();
        
        if (!isNaN(fechaRegistro.getTime())) {
            // Diferencia absoluta en horas
            diffHoras = Math.abs(ahora - fechaRegistro) / (1000 * 60 * 60);
            console.log("DEBUG GUEST: diffHoras calculado:", diffHoras.toFixed(2));
        } else {
            console.warn("DEBUG GUEST: Invalid Date parsing:", cleanDate);
        }
    } else {
        console.log("DEBUG GUEST: No hay fecha de registro, se asume >24h");
    }

    const infoBox      = document.getElementById('refundInfoBox');
    const statusText   = document.getElementById('refundStatusText');
    const policyDetail = document.getElementById('refundPolicyDetail');
    const chargeText   = document.getElementById('cancelChargeText');
    const amountText   = document.getElementById('refundAmountText');

    if (diffHoras < 24) {
        infoBox.style.borderLeftColor = '#10b981';
        statusText.innerText   = '¡Reembolso Total Disponible!';
        statusText.style.color = '#10b981';
        policyDetail.innerText = 'Estás cancelando dentro de las primeras 24 horas. Recibirás el 100% de tu pago.';
        chargeText.innerText   = '$0.00';
        amountText.innerText   = '$' + parseFloat(totalReserva).toLocaleString('es-MX', { minimumFractionDigits: 2 });
    } else {
        const cleanTotal = parseFloat(String(totalReserva).replace(/,/g, '')) || 0;
        const penalizacion = cleanTotal * 0.10;
        const reembolso    = cleanTotal * 0.90;
        infoBox.style.borderLeftColor = '#ef4444';
        statusText.innerText   = 'Cargo del 10% Aplicable';
        statusText.style.color = '#ef4444';
        policyDetail.innerText = 'Han pasado más de 24 horas desde la reserva. Se retendrá un 10% por gastos administrativos.';
        chargeText.innerText   = '$' + penalizacion.toLocaleString('es-MX', { minimumFractionDigits: 2 });
        amountText.innerText   = '$' + reembolso.toLocaleString('es-MX', { minimumFractionDigits: 2 });
    }
    
    console.log("DEBUG GUEST: Final Penalty Displayed:", chargeText.innerText);

    document.getElementById('modalCancelacion').style.display = 'flex';
}

function cerrarModalCancelacion() {
    document.getElementById('modalCancelacion').style.display = 'none';
    cancelacionPendiente = null;
}

function confirmarCancelacion() {
    if (!cancelacionPendiente) return;

    const motivo = document.getElementById('motivoCancelacion').value.trim();
    if (!motivo) {
        Swal.fire({
            icon: 'warning',
            title: 'Campo requerido',
            text: 'Por favor, ingresa el motivo de la cancelación.',
            confirmButtonColor: '#7C3AED'
        });
        return;
    }

    const { idReserva, role, idUsuario } = cancelacionPendiente;

    const formData = new FormData();
    formData.append('idReserva', idReserva);
    formData.append('role',      role);
    formData.append('idUsuario', idUsuario);
    formData.append('motivo',    motivo);

    const btnConf      = event.target;
    const textoOriginal = btnConf.innerHTML;
    btnConf.innerHTML  = '<i class="fa-solid fa-spinner fa-spin"></i> Procesando...';
    btnConf.disabled   = true;

    fetch('../../apis/huesped/cancelar_reserva.php', { method: 'POST', body: formData })
        .then(res  => res.json())
        .then(data => {
            btnConf.innerHTML = textoOriginal;
            btnConf.disabled  = false;

            if (data.ok) {
                cerrarModalCancelacion();
                
                // Actualizar UI
                const statusPills = document.querySelectorAll('.status-pill');
                if (statusPills.length > 0) {
                    statusPills[0].style.background = '#FEF3C7';
                    statusPills[0].style.color = '#92400E';
                    statusPills[0].innerHTML = '<i class="fa-solid fa-circle" style="font-size: 8px;"></i> PENDIENTE CANCELACIÓN';
                }
                const btnCancel = document.querySelector('.btn-cancel');
                if (btnCancel) {
                    btnCancel.style.display = 'none';
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Solicitud enviada',
                    text: 'Tu solicitud de cancelación ha sido enviada al anfitrión correctamente.',
                    confirmButtonColor: '#7C3AED'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.mensaje || 'No se pudo procesar la solicitud.',
                    confirmButtonColor: '#7C3AED'
                });
            }
        })
        .catch(err => {
            console.error(err);
            btnConf.innerHTML = textoOriginal;
            btnConf.disabled  = false;
            Swal.fire({
                icon: 'error',
                title: 'Error de red',
                text: 'Ocurrió un error al conectar con el servidor.',
                confirmButtonColor: '#7C3AED'
            });
        });
}
