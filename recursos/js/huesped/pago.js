/**
 * pago.js — Lógica de Confirmar y Pagar (Huésped)
 * recursos/js/huesped/pago.js
 * Requiere: SweetAlert2
 * Requiere variables globales inyectadas por pago.php:
 *   window.PAGO_DATA = { granTotal, idPropiedad, idUsuario, fechaInicio, fechaFin, huespedes }
 */

function selectPayment(type) {
    const optFull = document.getElementById('opt-full');
    const optPart = document.getElementById('opt-part');
    const btnPay  = document.getElementById('btn-submit-pay');
    const total   = window.PAGO_DATA.granTotal;

    if (type === 'full') {
        optFull.classList.add('active');
        if (optPart) optPart.classList.remove('active');
        btnPay.innerText = 'Confirmar y Pagar $' + total.toLocaleString('en-US', { minimumFractionDigits: 2 });
    } else {
        if (optPart) optPart.classList.add('active');
        optFull.classList.remove('active');
        btnPay.innerText = 'Confirmar y Pagar $' + (total * 0.3).toLocaleString('en-US', { minimumFractionDigits: 2 });
    }
}

async function procesarPago() {
    Swal.fire({
        title: 'Procesando pago...',
        text: 'Por favor espera un momento',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    const d = window.PAGO_DATA;
    const formData = new FormData();
    formData.append('idPropiedad', d.idPropiedad);
    formData.append('idUsuario',   d.idUsuario);
    formData.append('fechaInicio', d.fechaInicio);
    formData.append('fechaFin',    d.fechaFin);
    formData.append('total',       d.granTotal);
    formData.append('huespedes',   d.huespedes);

    try {
        const response = await fetch('../../apis/huesped/guardar_reserva.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.ok) {
            Swal.fire({
                icon: 'success',
                title: '¡Pago Exitoso!',
                text: 'Tu reservación ha sido confirmada correctamente.',
                confirmButtonText: 'Ver mis reservas',
                confirmButtonColor: '#7C3AED'
            }).then(() => { window.location.href = 'reservas.php'; });
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: result.mensaje || 'No se pudo procesar la reservación.' });
        }
    } catch (error) {
        console.error(error);
        Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'Hubo un problema al conectar con el servidor.' });
    }
}
