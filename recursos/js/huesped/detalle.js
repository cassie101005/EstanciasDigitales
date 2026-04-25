/**
 * detalle.js — Lógica del Detalle de Propiedad (Huésped)
 * recursos/js/huesped/detalle.js
 * Requiere window.DETALLE_DATA = { precioNoche, reservedRanges } inyectado por detalle.php
 */

(function () {
    const precioNoche    = window.DETALLE_DATA.precioNoche;
    const tarifaLimpieza = 1200;
    const reservedRanges = window.DETALLE_DATA.reservedRanges;

    // ── Verificar solapamiento de fechas ──
    function checkOverlap(startStr, endStr) {
        const start = new Date(startStr);
        const end   = new Date(endStr);
        for (let range of reservedRanges) {
            const rStart = new Date(range.start);
            const rEnd   = new Date(range.end);
            if (start < rEnd && end > rStart) return true;
        }
        return false;
    }

    // ── Actualizar resumen de precio en sidebar ──
    window.updateReservationSummary = function () {
        const fechaInicio = document.getElementById('fechaInicio').value;
        const fechaFin    = document.getElementById('fechaFin').value;
        const summaryList = document.getElementById('summaryList');

        if (fechaInicio && fechaFin) {
            const start = new Date(fechaInicio);
            const end   = new Date(fechaFin);

            if (end > start) {
                if (checkOverlap(fechaInicio, fechaFin)) {
                    alert('Lo sentimos, algunas de las fechas seleccionadas ya están reservadas. Por favor elige otro rango.');
                    document.getElementById('fechaInicio').value = '';
                    document.getElementById('fechaFin').value    = '';
                    summaryList.style.display = 'none';
                    return;
                }

                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                const basePrice = diffDays * precioNoche;
                const total     = basePrice + tarifaLimpieza;

                document.getElementById('summaryNights').innerText    = diffDays;
                document.getElementById('summaryBasePrice').innerText = '$' + basePrice.toLocaleString() + ' MXN';
                document.getElementById('summaryTotal').innerText     = '$' + total.toLocaleString() + ' MXN';
                document.getElementById('nochesInput').value          = diffDays;
                document.getElementById('totalInput').value           = total;

                summaryList.style.display = 'block';
            } else {
                summaryList.style.display = 'none';
            }
        }
    };

    // ── Fechas mínimas ──
    const today    = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    const fechaInicioEl = document.getElementById('fechaInicio');
    if (fechaInicioEl) {
        fechaInicioEl.min = tomorrow.toISOString().split('T')[0];
        fechaInicioEl.addEventListener('change', () => {
            const nextDay = new Date(fechaInicioEl.value);
            nextDay.setDate(nextDay.getDate() + 1);
            document.getElementById('fechaFin').min = nextDay.toISOString().split('T')[0];
        });
    }

    // ── Lógica de Reseñas — estrellas ──
    document.querySelectorAll('.star-btn').forEach(star => {
        star.onclick = function () {
            const val = this.getAttribute('data-value');
            document.getElementById('inputCalificacion').value = val;
            document.querySelectorAll('.star-btn').forEach(s => {
                s.style.color = s.getAttribute('data-value') <= val ? '#fbbf24' : '#cbd5e1';
            });
        };
    });

    // ── Envío de reseña ──
    const formResenia = document.getElementById('formResenia');
    if (formResenia) {
        formResenia.onsubmit = async (e) => {
            e.preventDefault();
            const fd  = new FormData(formResenia);
            const btn = formResenia.querySelector('button');
            btn.disabled  = true;
            btn.innerText = 'Enviando...';

            try {
                const res  = await fetch('../../apis/huesped/resenia.php', { method: 'POST', body: fd });
                const data = await res.json();
                if (data.ok) {
                    location.reload();
                } else {
                    console.error(data.error);
                }
            } catch (err) {
                console.error(err);
                alert('Error de conexión al enviar la reseña.');
            } finally {
                btn.disabled  = false;
                btn.innerText = 'Enviar Reseña';
            }
        };
    }
})();
