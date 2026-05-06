let cancelacionPendiente = null;
let aprobacionPendiente = null;
let motivoAprobacion = '';

function verDetallesGenerales(idReserva, status, obs, cancelInfo = null) {
    document.getElementById('modalDetallesTitle').innerText = 'Detalles de la Reserva';
    document.getElementById('modalDetallesSubtitle').innerText = 'Información general de esta reserva';
    document.getElementById('modalDetallesLabel').innerText = 'Información Detallada';
    document.getElementById('modalIcon').className = 'fa-solid fa-circle-info';
    document.getElementById('modalIconContainer').style.background = '#f1f5f9';
    document.getElementById('modalIconContainer').style.color = '#475569';
    
    let statusColor = 'var(--primary)';
    let statusBg = '#f3e8ff';
    if (status === 'Cancelada') { statusColor = '#dc2626'; statusBg = '#fee2e2'; }
    if (status === 'Finalizada') { statusColor = '#64748b'; statusBg = '#f1f5f9'; }
    if (status === 'En curso') { statusColor = '#2563eb'; statusBg = '#dbeafe'; }

    let cancelHtml = '';
    if (status === 'Cancelada' && cancelInfo && cancelInfo.reembolso > 0) {
        cancelHtml = `
            <div style="margin-top: 1.25rem; padding-top: 1.25rem; border-top: 2px dashed #f1f5f9;">
                <div style="font-size: 10px; font-weight: 800; color: #94a3b8; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 1px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-receipt" style="color: #dc2626; font-size: 13px;"></i> LIQUIDACIÓN DE CANCELACIÓN
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 13px; color: #64748b; font-weight: 500;">Tipo de cargo:</span>
                        <span style="font-size: 13px; font-weight: 700; color: #0f172a; background: #f8fafc; padding: 3px 8px; border-radius: 6px; border: 1px solid #f1f5f9;">${cancelInfo.tipo}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 13px; color: #64748b; font-weight: 500;">Penalización:</span>
                        <span style="font-size: 13px; font-weight: 700; color: #dc2626;">$${parseFloat(cancelInfo.penalizacion).toLocaleString()}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.25rem; padding: 0.875rem; border-radius: 12px; background: #ecfdf5; border: 1px solid #d1fae5;">
                        <span style="font-size: 12px; color: #065f46; font-weight: 700;">REEMBOLSO TOTAL:</span>
                        <span style="font-size: 16px; font-weight: 900; color: #059669;">$${parseFloat(cancelInfo.reembolso).toLocaleString()}</span>
                    </div>
                </div>
            </div>
        `;
    }

    document.getElementById('motivoSolicitud').innerHTML = `
        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 1rem; border-bottom: 1px solid #f1f5f9;">
                <span style="font-size: 12px; font-weight: 700; color: #64748b;">ESTADO ACTUAL</span>
                <span style="background: ${statusBg}; color: ${statusColor}; padding: 4px 12px; border-radius: 8px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; border: 1px solid rgba(0,0,0,0.05);">${status}</span>
            </div>

            <div>
                <div style="font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.5rem; letter-spacing: 1px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-message" style="color: var(--primary); font-size: 12px;"></i> NOTAS DEL ANFITRIÓN
                </div>
                <div style="font-size: 14px; color: #334155; line-height: 1.5; font-weight: 500;">
                    ${obs || 'No hay observaciones adicionales para esta reserva.'}
                </div>
            </div>

            ${cancelHtml}
        </div>
    `;
    
    document.getElementById('btnAprobarCancelacionHost').style.display = 'none';
    document.getElementById('modalAprobarCancelacion').style.display = 'flex';
}

function verSolicitudCancelacion(idReserva, motivo, idUsuario, fechaRegistroStr, total) {
    aprobacionPendiente = { idReserva, idUsuario };
    motivoAprobacion = motivo;
    
    document.getElementById('modalDetallesTitle').innerText = 'Solicitud de Cancelación';
    document.getElementById('modalDetallesSubtitle').innerText = 'El huésped desea cancelar su reserva';
    document.getElementById('modalDetallesLabel').innerText = 'Motivo reportado por el huésped';
    document.getElementById('modalIcon').className = 'fa-solid fa-envelope-open-text';
    document.getElementById('modalIconContainer').style.background = '#fef9c3';
    document.getElementById('modalIconContainer').style.color = '#854d0e';
    
    document.getElementById('motivoSolicitud').innerHTML = `
        <div style="background: #f8fafc; padding: 1.25rem; border-radius: 12px; border: 1px solid #f1f5f9;">
            <div style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px; display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-comment-dots" style="font-size: 12px;"></i> EXPLICACIÓN DEL HUÉSPED
            </div>
            <p style="margin: 0; font-size: 14px; color: #334155; line-height: 1.6; font-weight: 500;">${motivo || 'Sin motivo especificado.'}</p>
        </div>
    `;
    
    // Calcular penalización y ganancias basado en la fecha de creación de la reserva
    let diffHoras = 999; // Default: >24h
    console.log("DEBUG APPROVE: total:", total);
    console.log("DEBUG APPROVE: fechaRegistroStr:", fechaRegistroStr);
    
    if (fechaRegistroStr && fechaRegistroStr !== 'null' && fechaRegistroStr !== '') {
        const cleanDate = fechaRegistroStr.replace(/-/g, '/').replace('T', ' ');
        const fechaRegistro = new Date(cleanDate);
        const ahora = new Date();
        
        if (!isNaN(fechaRegistro.getTime())) {
            diffHoras = Math.abs(ahora - fechaRegistro) / (1000 * 60 * 60);
            console.log("DEBUG APPROVE: diffHoras:", diffHoras.toFixed(2));
        }
    }

    const settlementBox       = document.getElementById('hostSettlementBox');
    const settlementIndicator = document.getElementById('hostSettlementIndicator');
    const settlementStatus    = document.getElementById('hostSettlementStatus');
    const settlementDetail    = document.getElementById('hostSettlementDetail');
    const guestRefundText     = document.getElementById('guestRefundTextHost');
    const hostEarningsText    = document.getElementById('hostEarningsText');

    settlementBox.style.display = 'block';
    const cleanTotal = parseFloat(String(total).replace(/,/g, '')) || 0;

    if (diffHoras < 24) {
        // Menos de 24h: reembolso total al huésped, sin ganancia para el anfitrión
        settlementIndicator.style.borderLeftColor = '#64748b';
        settlementStatus.innerText = '✅ Reembolso Total — Sin Comisión';
        settlementStatus.style.color = '#64748b';
        settlementDetail.innerText = 'Cancelación dentro de las primeras 24h. El huésped recibe el 100% de reembolso.';
        guestRefundText.innerText  = '$' + cleanTotal.toLocaleString('es-MX', {minimumFractionDigits: 2});
        hostEarningsText.innerText = '$0.00';
        hostEarningsText.style.color = '#64748b';
    } else {
        // Más de 24h: se cobra 10% de comisión al huésped
        const comision  = cleanTotal * 0.10;
        const reembolso = cleanTotal * 0.90;

        settlementIndicator.style.borderLeftColor = '#10b981';
        settlementStatus.innerText = '⚡ Comisión del 10% Aplicable';
        settlementStatus.style.color = '#10b981';
        settlementDetail.innerText = 'Han pasado más de 24h desde la reserva. Se cobra un 10% de comisión. Tú recibirás esa ganancia.';
        guestRefundText.innerText  = '$' + reembolso.toLocaleString('es-MX', {minimumFractionDigits: 2});
        hostEarningsText.innerText = '$' + comision.toLocaleString('es-MX', {minimumFractionDigits: 2});
        hostEarningsText.style.color = '#10b981';
    }
    console.log("DEBUG APPROVE: Final Commission Displayed:", hostEarningsText.innerText);

    document.getElementById('btnAprobarCancelacionHost').style.display = 'block';
    document.getElementById('modalAprobarCancelacion').style.display = 'flex';
}

function cerrarModalAprobar() {
    document.getElementById('modalAprobarCancelacion').style.display = 'none';
    aprobacionPendiente = null;
}

function aprobarCancelacion() {
    if (!aprobacionPendiente) return;

    // Guardar datos ANTES de que cerrarModalAprobar() limpie aprobacionPendiente
    const idReservaGuardado = aprobacionPendiente.idReserva;

    // Capturar la ganancia ya calculada en el modal para mostrarla en el SweetAlert
    const gananciaTexto = document.getElementById('hostEarningsText')?.innerText || '$0.00';
    const gananciaEsCero = gananciaTexto === '$0.00';

    const formData = new FormData();
    formData.append('idReserva', idReservaGuardado);
    formData.append('role', 'anfitrion');
    formData.append('idUsuario', aprobacionPendiente.idUsuario);
    formData.append('motivo', motivoAprobacion);
    formData.append('csrf_token', document.getElementById('global_csrf_token').value);

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
        btnConf.innerHTML = textoOriginal;
        btnConf.disabled = false;

        if (data.ok) {
            cerrarModalAprobar();

            // Actualizar la fila en tiempo real
            const fila = Array.from(document.querySelectorAll('.reserva-row')).find(row =>
                row.innerHTML.includes(`verSolicitudCancelacion(${idReservaGuardado}`)
            );
            if (fila) {
                const statusCell  = fila.querySelector('td:nth-child(4)');
                const actionsCell = fila.querySelector('td:nth-child(6)');
                const detailsCell = fila.querySelector('td:last-child');
                if (statusCell)  statusCell.innerHTML  = '<span class="status-tag" style="background:#fee2e2;color:#991b1b;">Cancelada</span>';
                if (actionsCell) actionsCell.innerHTML = '';
                if (detailsCell) detailsCell.innerHTML = '<span style="font-size:12px;font-weight:700;color:#94a3b8;"><i class="fa-solid fa-check"></i> Cancelada</span>';
                fila.dataset.estado = 'cancelada';
            }

            // Mensaje de éxito con ganancia del anfitrión
            const htmlMsg = gananciaEsCero
                ? `<p style="color:#64748b;margin:0">El huésped recibirá un reembolso del <strong>100%</strong>. Sin comisión aplicada (cancelación dentro de 24h).</p>`
                : `<p style="margin:0">Se aplicó una comisión del <strong>10%</strong>.<br><br>
                   <span style="font-size:1.1rem;font-weight:800;color:#10b981">Tu ganancia: ${gananciaTexto}</span><br>
                   <span style="font-size:0.85rem;color:#64748b">El reembolso al huésped se ha procesado automáticamente.</span></p>`;

            Swal.fire({
                icon: 'success',
                title: '¡Cancelación confirmada!',
                html: htmlMsg,
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#7C3AED'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.mensaje || 'No se pudo procesar la cancelación.',
                confirmButtonColor: '#7C3AED'
            });
        }
    })
    .catch(err => {
        console.error(err);
        btnConf.innerHTML = textoOriginal;
        btnConf.disabled = false;
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'Hubo un problema al conectar con el servidor.',
            confirmButtonColor: '#7C3AED'
        });
    });
}

// ── Filtros + Paginación de Reservas ─────────────────────────────────────────
let filtroActual = "todas";
let paginaActual = 1;
const reservasPorPagina = 5;

function renderReservas() {
    const filas = Array.from(document.querySelectorAll(".reserva-row"));

    const filtradas = filas.filter(row => {
        const estado = row.dataset.estado || "";
        return filtroActual === "todas" || estado === filtroActual;
    });

    // Ocultar todas primero
    filas.forEach(row => row.style.display = "none");

    // Mostrar solo las de la página actual dentro del filtro
    const inicio = (paginaActual - 1) * reservasPorPagina;
    const fin    = inicio + reservasPorPagina;
    filtradas.slice(inicio, fin).forEach(row => row.style.display = "");

    renderPaginacionReservas(filtradas.length);
}

function renderPaginacionReservas(total) {
    const container = document.getElementById("paginationContainer");
    if (!container) return;

    const totalPaginas = Math.ceil(total / reservasPorPagina);
    container.innerHTML = "";
    if (totalPaginas <= 1) return;

    const mkBtn = (label, page, activo, disabled) => {
        const btn = document.createElement("button");
        btn.innerHTML = label;
        btn.className = "pagination-btn" + (activo ? " active" : "") + (disabled ? " disabled" : "");
        if (!disabled) btn.addEventListener("click", () => { paginaActual = page; renderReservas(); });
        return btn;
    };

    container.appendChild(mkBtn('<i class="fa-solid fa-chevron-left"></i> Anterior', paginaActual - 1, false, paginaActual === 1));

    for (let i = 1; i <= totalPaginas; i++) {
        if (i === 1 || i === totalPaginas || (i >= paginaActual - 1 && i <= paginaActual + 1)) {
            container.appendChild(mkBtn(i, i, i === paginaActual, false));
        }
    }

    container.appendChild(mkBtn('Siguiente <i class="fa-solid fa-chevron-right"></i>', paginaActual + 1, false, paginaActual === totalPaginas));
}

// Escuchar clics en los botones de filtro
document.querySelectorAll(".filter-btn").forEach(btn => {
    btn.addEventListener("click", () => {
        filtroActual = btn.dataset.filter;
        paginaActual = 1;

        document.querySelectorAll(".filter-btn").forEach(b => {
            b.classList.remove("active");
            b.style.color = "#64748b";
            b.style.background = "#f8fafc";
        });
        btn.classList.add("active");
        btn.style.color = "white";
        btn.style.background = "var(--primary)";

        renderReservas();
    });
});

// ── Paginación de Reseñas (independiente, sin filtro) ─────────────────────────
function setupSimplePagination(rowClass, containerId, itemsPerPage) {
    const rows = Array.from(document.getElementsByClassName(rowClass));
    const container = document.getElementById(containerId);
    if (!rows.length || !container) return;

    let currentPage = 1;
    const totalPages = Math.ceil(rows.length / itemsPerPage);

    function showPage(page) {
        currentPage = page;
        const start = (page - 1) * itemsPerPage;
        const end   = start + itemsPerPage;
        rows.forEach((row, i) => row.style.display = (i >= start && i < end) ? "" : "none");
        renderControls();
    }

    function renderControls() {
        container.innerHTML = "";
        if (totalPages <= 1) return;

        const mkBtn = (label, page, activo, disabled) => {
            const btn = document.createElement("button");
            btn.innerHTML = label;
            btn.className = "pagination-btn" + (activo ? " active" : "") + (disabled ? " disabled" : "");
            if (!disabled) btn.addEventListener("click", () => showPage(page));
            return btn;
        };

        container.appendChild(mkBtn('<i class="fa-solid fa-chevron-left"></i> Anterior', currentPage - 1, false, currentPage === 1));
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                container.appendChild(mkBtn(i, i, i === currentPage, false));
            }
        }
        container.appendChild(mkBtn('Siguiente <i class="fa-solid fa-chevron-right"></i>', currentPage + 1, false, currentPage === totalPages));
    }

    showPage(1);
}

document.addEventListener("DOMContentLoaded", () => {
    renderReservas();                                          // reservas con filtros
    setupSimplePagination("review-row", "reviewsPagination", 5); // reseñas sin filtros
});

function cancelarReserva(idReserva, role, idUsuario, fechaRegistroStr, total) {
    cancelacionPendiente = { idReserva, role, idUsuario };
    document.getElementById('motivoCancelacion').value = '';

    // Calcular penalización y ganancias en tiempo real
    let diffHoras = 999;
    console.log("DEBUG CANCEL: total:", total);
    console.log("DEBUG CANCEL: fechaRegistroStr:", fechaRegistroStr);

    if (fechaRegistroStr && fechaRegistroStr !== 'null' && fechaRegistroStr !== '') {
        const cleanDate = fechaRegistroStr.replace(/-/g, '/').replace('T', ' ');
        const fechaRegistro = new Date(cleanDate);
        const ahora = new Date();
        
        if (!isNaN(fechaRegistro.getTime())) {
            diffHoras = Math.abs(ahora - fechaRegistro) / (1000 * 60 * 60);
            console.log("DEBUG CANCEL: diffHoras:", diffHoras.toFixed(2));
        }
    }

    const indicator = document.getElementById('hostCancelIndicator');
    const status    = document.getElementById('hostCancelStatus');
    const detail    = document.getElementById('hostCancelDetail');
    const refund    = document.getElementById('guestRefundTextHostInitiated');
    const earnings  = document.getElementById('hostEarningsTextHostInitiated');

    const cleanTotal = parseFloat(String(total).replace(/,/g, '')) || 0;

    if (diffHoras < 24) {
        indicator.style.borderLeftColor = '#64748b';
        status.innerText = '✅ Reembolso Total — Sin Comisión';
        status.style.color = '#64748b';
        detail.innerText = 'Cancelación dentro de las primeras 24h. El huésped recibe el 100% de reembolso.';
        refund.innerText = '$' + cleanTotal.toLocaleString('es-MX', {minimumFractionDigits: 2});
        earnings.innerText = '$0.00';
        earnings.style.color = '#64748b';
    } else {
        const comision  = cleanTotal * 0.10;
        const reembolso = cleanTotal * 0.90;
        indicator.style.borderLeftColor = '#10b981';
        status.innerText = '⚡ Comisión del 10% Aplicable';
        status.style.color = '#10b981';
        detail.innerText = 'Han pasado más de 24h desde la reserva. Se cobrará un 10% de comisión. Tú recibirás esa ganancia.';
        refund.innerText = '$' + reembolso.toLocaleString('es-MX', {minimumFractionDigits: 2});
        earnings.innerText = '$' + comision.toLocaleString('es-MX', {minimumFractionDigits: 2});
        earnings.style.color = '#10b981';
    }
    console.log("DEBUG CANCEL: Final Earnings Displayed:", earnings.innerText);

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

    // Guardar el id ANTES de cerrar el modal (cerrarModalCancelacion lo pone en null)
    const { idReserva, role, idUsuario } = cancelacionPendiente;

    // Capturar la ganancia ya calculada en el modal para mostrarla en el SweetAlert
    const gananciaTexto = document.getElementById('hostEarningsTextHostInitiated')?.innerText || '$0.00';
    const gananciaEsCero = gananciaTexto === '$0.00';
    
    const formData = new FormData();
    formData.append('idReserva', idReserva);
    formData.append('role', role);
    formData.append('idUsuario', idUsuario);
    formData.append('motivo', motivo);
    formData.append('csrf_token', document.getElementById('global_csrf_token').value);

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
        btnConf.innerHTML = textoOriginal;
        btnConf.disabled = false;

        if (data.ok) {
            cerrarModalCancelacion(); // Cerrar ANTES de buscar la fila (ya tenemos idReserva guardado)

            const fila = Array.from(document.querySelectorAll('.reserva-row')).find(row =>
                row.innerHTML.includes(`cancelarReserva(${idReserva}`) ||
                row.innerHTML.includes(`verSolicitudCancelacion(${idReserva}`)
            );
            if (fila) {
                const statusCell  = fila.querySelector('td:nth-child(4)');
                const actionsCell = fila.querySelector('td:nth-child(6)');
                const detailsCell = fila.querySelector('td:last-child');
                if (statusCell)  statusCell.innerHTML  = '<span class="status-tag" style="background:#fee2e2;color:#991b1b;">Cancelada</span>';
                if (actionsCell) actionsCell.innerHTML = '';
                if (detailsCell) detailsCell.innerHTML = '<span style="font-size:12px;font-weight:700;color:#94a3b8;"><i class="fa-solid fa-check"></i> Cancelada</span>';
                fila.dataset.estado = 'cancelada';
            }

            // Mensaje de éxito con ganancia del anfitrión
            const htmlMsg = gananciaEsCero
                ? `<p style="color:#64748b;margin:0">La reserva se canceló sin comisión (dentro de las 24h). El huésped recibe el 100%.</p>`
                : `<p style="margin:0">Se aplicó una comisión del <strong>10%</strong>.<br><br>
                   <span style="font-size:1.1rem;font-weight:800;color:#10b981">Tu ganancia: ${gananciaTexto}</span><br>
                   <span style="font-size:0.85rem;color:#64748b">El reembolso al huésped se ha procesado automáticamente.</span></p>`;

            Swal.fire({
                icon: 'success',
                title: '¡Cancelación confirmada!',
                html: htmlMsg,
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#7C3AED'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.mensaje || 'No se pudo cancelar la reserva.',
                confirmButtonColor: '#7C3AED'
            });
        }
    })
    .catch(err => {
        console.error(err);
        btnConf.innerHTML = textoOriginal;
        btnConf.disabled = false;
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'Hubo un problema al conectar con el servidor.',
            confirmButtonColor: '#7C3AED'
        });
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

    const btn = document.querySelector('#modalRespuesta button:last-child');
    if (btn) {
        btn.disabled = true;
        btn.innerText = 'Enviando...';
    }

    const fd = new FormData();
    fd.append('tipo', respuestaPendiente.tipo);
    fd.append('id', respuestaPendiente.id);
    fd.append('respuesta', respuesta);
    fd.append('csrf_token', document.getElementById('global_csrf_token').value);

    fetch('../../apis/anfitrion/responder_comentario.php', {
        method: 'POST',
        body: fd
    })
    .then(async response => {
        const text = await response.text();
        console.log("Respuesta API responder reseña:", text);
        try {
            return JSON.parse(text);
        } catch (err) {
            throw new Error("Respuesta no es JSON válido: " + text);
        }
    })
    .then(data => {
        if (data.ok) {
            const cellId = `respCell_${respuestaPendiente.tipo}_${respuestaPendiente.id}`;
            const cell = document.getElementById(cellId);
            if (cell) {
                cell.innerHTML = `
                    <div style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${respuesta.replace(/"/g, '&quot;')}">
                        <i class="fa-solid fa-reply"></i> ${respuesta.replace(/</g, '&lt;').replace(/>/g, '&gt;')}
                    </div>
                `;
                cell.style.color = 'var(--primary)';
                
                const btnAction = cell.closest('tr').querySelector('button[onclick^="abrirModalRespuesta"]');
                if (btnAction) {
                    btnAction.innerHTML = '<i class="fa-solid fa-reply"></i> Editar';
                }
            }
            // Limpiar manualmente el campo
            document.getElementById('txtRespuesta').value = '';
            cerrarModalRespuesta();
            Swal.fire({
                icon: 'success',
                title: 'Respuesta enviada',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
            btn.disabled = false;
            btn.innerText = 'Enviar Respuesta';
        } else {
            alert('Error: ' + data.error);
            btn.disabled = false;
            btn.innerText = 'Enviar Respuesta';
        }
    })
    .catch(e => {
        console.error("Error en enviarRespuesta:", e);
        alert('Error de red al enviar la respuesta. Revisa la consola.');
        btn.disabled = false;
        btn.innerText = 'Enviar Respuesta';
    });
}
