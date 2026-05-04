/**
 * detalle.js — Lógica del Detalle de Propiedad (Huésped)
 * recursos/js/huesped/detalle.js
 * Requiere window.DETALLE_DATA = { precioNoche, reservedRanges, specialRates } inyectado por detalle.php
 */

(function () {
    const precioNoche    = window.DETALLE_DATA.precioNoche;
    const tarifaLimpieza = 1200;
    const reservedRanges = window.DETALLE_DATA.reservedRanges || [];
    const specialRates   = window.DETALLE_DATA.specialRates || [];

    // ── Obtener precio para una fecha específica (considerando tarifas especiales) ──
    function getPrecioParaFecha(dateStr) {
        for (let rate of specialRates) {
            if (dateStr >= rate.start && dateStr <= rate.end) {
                return parseFloat(rate.precio);
            }
        }
        return parseFloat(precioNoche);
    }

    // ── Verificar solapamiento de fechas (Reservas y Bloqueos) ──
    function checkOverlap(startStr, endStr) {
        const start = new Date(startStr + 'T00:00:00');
        const end   = new Date(endStr + 'T00:00:00');
        
        for (let range of reservedRanges) {
            const rStart = new Date(range.start + 'T00:00:00');
            const rEnd   = new Date(range.end + 'T00:00:00');
            
            // Lógica de solapamiento: (A.inicio < B.fin) && (A.fin > B.inicio)
            if (start < rEnd && end > rStart) return true;
        }
        return false;
    }

    // ── Actualizar resumen de precio en sidebar (Sincronizado con API) ──
    window.updateReservationSummary = async function () {
        const fechaInicioVal = document.getElementById('fechaInicio').value;
        const fechaFinVal    = document.getElementById('fechaFin').value;
        const summaryList    = document.getElementById('summaryList');
        const idPropiedad    = window.DETALLE_DATA.idPropiedad;

        if (fechaInicioVal && fechaFinVal) {
            const start = new Date(fechaInicioVal + 'T00:00:00');
            const end   = new Date(fechaFinVal + 'T00:00:00');

            if (end > start) {
                try {
                    const res  = await fetch(`../../apis/huesped/calcular_precio.php?idPropiedad=${idPropiedad}&fechaInicio=${fechaInicioVal}&fechaFin=${fechaFinVal}`);
                    const data = await res.json();

                    if (data.ok) {
                        if (!data.disponible) {
                            alert('Lo sentimos, estas fechas ya no están disponibles (reservadas o bloqueadas). Por favor elige otro rango.');
                            document.getElementById('fechaInicio').value = '';
                            document.getElementById('fechaFin').value    = '';
                            summaryList.style.display = 'none';
                            return;
                        }

                        const d = data.desglose;
                        document.getElementById('summaryNights').innerText    = d.noches;
                        document.getElementById('summaryBasePrice').innerText = '$' + d.totalBase.toLocaleString('es-MX') + ' MXN';
                        document.getElementById('summaryTotal').innerText     = '$' + d.granTotal.toLocaleString('es-MX') + ' MXN';
                        document.getElementById('nochesInput').value          = d.noches;
                        document.getElementById('totalInput').value           = d.granTotal;

                        summaryList.style.display = 'block';
                    } else {
                        console.error(data.mensaje);
                    }
                } catch (e) {
                    console.error("Error al sincronizar precio:", e);
                }
            } else {
                summaryList.style.display = 'none';
            }
        }
    };

    // ── Fechas mínimas y restricciones ──
    const today = new Date();
    const todayStr = today.toISOString().split('T')[0];
    const fechaInicioEl = document.getElementById('fechaInicio');
    const fechaFinEl = document.getElementById('fechaFin');

    if (fechaInicioEl) {
        fechaInicioEl.min = todayStr;
        fechaInicioEl.addEventListener('change', () => {
            const nextDay = new Date(fechaInicioEl.value + 'T00:00:00');
            nextDay.setDate(nextDay.getDate() + 1);
            fechaFinEl.min = nextDay.toISOString().split('T')[0];
            if (fechaFinEl.value && fechaFinEl.value <= fechaInicioEl.value) {
                fechaFinEl.value = '';
            }
            window.updateReservationSummary();
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
                    alert(data.error || 'Error al enviar reseña');
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
