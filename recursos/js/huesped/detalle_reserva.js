/**
 * detalle_reserva.js — Lógica del Detalle de Reserva (Huésped)
 * recursos/js/huesped/detalle_reserva.js
 * Requiere window.RESERVA_DATA = { fechaInicio, totalReserva } inyectado por detalle_reserva.php
 */

let cancelacionPendiente = null;

function cancelarReserva(idReserva, role, idUsuario) {
    cancelacionPendiente = { idReserva, role, idUsuario };
    document.getElementById('motivoCancelacion').value = '';

    const { fechaInicio: fechaInicioStr, totalReserva } = window.RESERVA_DATA;

    // Calcular reembolso en tiempo real
    const fechaInicio = new Date(fechaInicioStr + ' 15:00:00'); // check-in a las 3pm
    const ahora       = new Date();
    const diffHoras   = (fechaInicio - ahora) / (1000 * 60 * 60);

    const infoBox     = document.getElementById('refundInfoBox');
    const statusText  = document.getElementById('refundStatusText');
    const policyDetail = document.getElementById('refundPolicyDetail');
    const chargeText  = document.getElementById('cancelChargeText');
    const amountText  = document.getElementById('refundAmountText');

    if (diffHoras >= 24) {
        infoBox.style.borderLeftColor = '#10b981';
        statusText.innerText   = '¡Reembolso Total Disponible!';
        statusText.style.color = '#10b981';
        policyDetail.innerText = 'Faltan más de 24 horas para el check-in. No se aplicarán cargos.';
        chargeText.innerText   = '$0.00';
        amountText.innerText   = '$' + totalReserva.toLocaleString(undefined, { minimumFractionDigits: 2 });
    } else {
        const penalizacion = totalReserva * 0.10;
        const reembolso    = totalReserva * 0.90;
        infoBox.style.borderLeftColor = '#ef4444';
        statusText.innerText   = 'Se aplicará cargo del 10%';
        statusText.style.color = '#ef4444';
        policyDetail.innerText = 'Faltan menos de 24 horas para el check-in. Se retendrá una comisión.';
        chargeText.innerText   = '$' + penalizacion.toLocaleString(undefined, { minimumFractionDigits: 2 });
        amountText.innerText   = '$' + reembolso.toLocaleString(undefined, { minimumFractionDigits: 2 });
    }

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
        alert('Por favor, ingresa el motivo de la cancelación.');
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
    btnConf.innerHTML  = '<i class="fa-solid fa-spinner fa-spin"></i> Cancelando...';
    btnConf.disabled   = true;

    fetch('../../apis/cancelar_reserva.php', { method: 'POST', body: formData })
        .then(res  => res.json())
        .then(data => {
            if (data.ok) {
                alert('Reserva cancelada correctamente.');
                window.location.reload();
            } else {
                alert('Error: ' + data.mensaje);
                btnConf.innerHTML = textoOriginal;
                btnConf.disabled  = false;
            }
        })
        .catch(err => {
            console.error(err);
            alert('Ocurrió un error en el servidor.');
            btnConf.innerHTML = textoOriginal;
            btnConf.disabled  = false;
        });
}
