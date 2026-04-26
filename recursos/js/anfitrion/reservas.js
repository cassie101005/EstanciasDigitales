let cancelacionPendiente = null;
let aprobacionPendiente = null;
let motivoAprobacion = '';

function verDetallesGenerales(idReserva, status, obs, cancelInfo = null) {
    document.getElementById('modalDetallesTitle').innerText = 'Detalles de la Reserva';
    document.getElementById('modalDetallesSubtitle').innerText = 'Información general de esta reserva';
    document.getElementById('modalDetallesLabel').innerText = 'Observaciones adicionales';
    document.getElementById('modalIcon').className = 'fa-solid fa-circle-info';
    document.getElementById('modalIconContainer').style.background = '#f8fafc';
    document.getElementById('modalIconContainer').style.color = '#475569';
    
    let cancelHtml = '';
    if (status === 'Cancelada' && cancelInfo && cancelInfo.reembolso > 0) {
        cancelHtml = `
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed #e2e8f0;">
                <div style="font-size: 12px; font-weight: 800; color: #dc2626; margin-bottom: 8px; text-transform: uppercase;">Información de Liquidación</div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                    <span style="font-size: 13px; color: #64748b;">Tipo:</span>
                    <span style="font-size: 13px; font-weight: 700; color: #0f172a;">${cancelInfo.tipo}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                    <span style="font-size: 13px; color: #64748b;">Penalización:</span>
                    <span style="font-size: 13px; font-weight: 700; color: #dc2626;">$${parseFloat(cancelInfo.penalizacion).toLocaleString()}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="font-size: 13px; color: #64748b;">Reembolso al Huésped:</span>
                    <span style="font-size: 14px; font-weight: 800; color: #10b981;">$${parseFloat(cancelInfo.reembolso).toLocaleString()}</span>
                </div>
            </div>
        `;
    }

    document.getElementById('motivoSolicitud').innerHTML = `
        <div style="margin-bottom: 8px;"><strong>Estado Actual:</strong> <span style="color:var(--primary); font-weight:800;">${status}</span></div>
        <div><strong>Notas:</strong> ${obs || 'Sin notas u observaciones especiales'}</div>
        ${cancelHtml}
    `;
    
    document.getElementById('btnAprobarCancelacionHost').style.display = 'none';
    document.getElementById('modalAprobarCancelacion').style.display = 'flex';
}

function verSolicitudCancelacion(idReserva, motivo, idUsuario, fechaInicioStr, total) {
    aprobacionPendiente = { idReserva, idUsuario };
    motivoAprobacion = motivo;
    
    document.getElementById('modalDetallesTitle').innerText = 'Solicitud de Cancelación';
    document.getElementById('modalDetallesSubtitle').innerText = 'El huésped desea cancelar su reserva';
    document.getElementById('modalDetallesLabel').innerText = 'Motivo reportado por el huésped';
    document.getElementById('modalIcon').className = 'fa-solid fa-envelope-open-text';
    document.getElementById('modalIconContainer').style.background = '#fef08a';
    document.getElementById('modalIconContainer').style.color = '#854d0e';
    
    document.getElementById('motivoSolicitud').innerText = motivo || 'Sin motivo especificado.';
    
    // Calcular penalización y ganancias para el anfitrión
    const fechaInicio = new Date(fechaInicioStr + 'T15:00:00');
    const ahora = new Date();
    const diffHoras = (fechaInicio - ahora) / (1000 * 60 * 60);
    
    const settlementBox = document.getElementById('hostSettlementBox');
    const settlementIndicator = document.getElementById('hostSettlementIndicator');
    const settlementStatus = document.getElementById('hostSettlementStatus');
    const settlementDetail = document.getElementById('hostSettlementDetail');
    const guestRefundText = document.getElementById('guestRefundTextHost');
    const hostEarningsText = document.getElementById('hostEarningsText');
    
    settlementBox.style.display = 'block';
    if (diffHoras >= 24) {
        settlementIndicator.style.borderLeftColor = '#64748b';
        settlementStatus.innerText = 'Reembolso Total (Sin Ganancia)';
        settlementStatus.style.color = '#64748b';
        settlementDetail.innerText = 'Cancelación solicitada con >24h de antelación. El huésped recibe el 100%.';
        guestRefundText.innerText = '$' + parseFloat(total).toLocaleString(undefined, {minimumFractionDigits: 2});
        hostEarningsText.innerText = '$0.00';
        hostEarningsText.style.color = '#64748b';
    } else {
        const penalizacion = total * 0.10;
        const reembolso = total * 0.90;
        
        settlementIndicator.style.borderLeftColor = '#10b981';
        settlementStatus.innerText = 'Penalización Aplicable (10%)';
        settlementStatus.style.color = '#10b981';
        settlementDetail.innerText = 'Faltan menos de 24h. Recibirás el 10% del total como compensación.';
        guestRefundText.innerText = '$' + reembolso.toLocaleString(undefined, {minimumFractionDigits: 2});
        hostEarningsText.innerText = '$' + penalizacion.toLocaleString(undefined, {minimumFractionDigits: 2});
        hostEarningsText.style.color = '#10b981';
    }
    
    document.getElementById('btnAprobarCancelacionHost').style.display = 'block';
    document.getElementById('modalAprobarCancelacion').style.display = 'flex';
}

function cerrarModalAprobar() {
    document.getElementById('modalAprobarCancelacion').style.display = 'none';
    aprobacionPendiente = null;
}

function aprobarCancelacion() {
    if (!aprobacionPendiente) return;
    
    const formData = new FormData();
    formData.append('idReserva', aprobacionPendiente.idReserva);
    formData.append('role', 'anfitrion');
    formData.append('idUsuario', aprobacionPendiente.idUsuario);
    formData.append('motivo', motivoAprobacion);

    const btnConf = event.target;
    const textoOriginal = btnConf.innerHTML;
    btnConf.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Aprobando...';
    btnConf.disabled = true;

    fetch('../../apis/anfitrion/cancelar_reserva.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.ok) {
            alert(data.mensaje);
            window.location.reload();
        } else {
            alert("Error: " + data.mensaje);
            btnConf.innerHTML = textoOriginal;
            btnConf.disabled = false;
        }
    })
    .catch(err => {
        console.error(err);
        alert("Ocurrió un error en el servidor.");
        btnConf.innerHTML = textoOriginal;
        btnConf.disabled = false;
    });
}

function filtrarReservas(estado, btn) {
    // Actualizar estilos visuales de los botones
    const botones = document.querySelectorAll('.filtro-btn');
    botones.forEach(b => {
        b.style.color = '#64748b';
        b.style.background = '#f8fafc';
    });
    btn.style.color = 'white';
    btn.style.background = 'var(--primary)';

    // Mostrar/Ocultar filas
    const filas = document.querySelectorAll('.reserva-row');
    filas.forEach(fila => {
        if (estado === 'Todas' || fila.getAttribute('data-status') === estado) {
            fila.style.display = '';
        } else {
            fila.style.display = 'none';
        }
    });
}

function cancelarReserva(idReserva, role, idUsuario) {
    cancelacionPendiente = { idReserva, role, idUsuario };
    document.getElementById('motivoCancelacion').value = '';
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
    formData.append('role', role);
    formData.append('idUsuario', idUsuario);
    formData.append('motivo', motivo);

    const btnConf = event.target;
    const textoOriginal = btnConf.innerHTML;
    btnConf.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Cancelando...';
    btnConf.disabled = true;

    fetch('../../apis/anfitrion/cancelar_reserva.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.ok) {
            alert("Reserva cancelada exitosamente.");
            window.location.reload();
        } else {
            alert("Error: " + data.mensaje);
            btnConf.innerHTML = textoOriginal;
            btnConf.disabled = false;
        }
    })
    .catch(err => {
        console.error(err);
        alert("Ocurrió un problema de red al intentar cancelar.");
        btnConf.innerHTML = textoOriginal;
        btnConf.disabled = false;
    });
}

let respuestaPendiente = null;

function abrirModalRespuesta(tipo, id, nombre) {
    respuestaPendiente = { tipo, id };
    document.getElementById('respGuestName').innerText = nombre;
    document.getElementById('txtRespuesta').value = '';
    document.getElementById('modalRespuesta').style.display = 'flex';
}

function cerrarModalRespuesta() {
    document.getElementById('modalRespuesta').style.display = 'none';
    respuestaPendiente = null;
}

function enviarRespuesta() {
    if (!respuestaPendiente) return;
    const respuesta = document.getElementById('txtRespuesta').value.trim();
    if (!respuesta) {
        alert('Escribe una respuesta antes de enviar.');
        return;
    }

    const btn = event.target;
    btn.disabled = true;
    btn.innerText = 'Enviando...';

    const fd = new FormData();
    fd.append('tipo', respuestaPendiente.tipo);
    fd.append('id', respuestaPendiente.id);
    fd.append('respuesta', respuesta);

    fetch('../../apis/anfitrion/responder_comentario.php', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) {
            window.location.reload();
        } else {
            alert('Error: ' + data.error);
            btn.disabled = false;
            btn.innerText = 'Enviar Respuesta';
        }
    })
    .catch(e => {
        console.error(e);
        alert('Error de red al enviar la respuesta.');
        btn.disabled = false;
        btn.innerText = 'Enviar Respuesta';
    });
}
