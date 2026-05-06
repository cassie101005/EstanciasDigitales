/**
 * detalle.js — Lógica del Detalle de Propiedad (Huésped)
 * recursos/js/huesped/detalle.js
 * Requiere window.DETALLE_DATA = { precioNoche, reservedRanges, specialRates } inyectado por detalle.php
 */

(function () {
    const precioNoche    = window.DETALLE_DATA.precioNoche;
    const categoria      = window.DETALLE_DATA.categoria || '';
    
    function obtenerTarifaLimpieza(subtotal) {
        const porcentajes = {
            'habitacion': 0.05, 'habitación': 0.05,
            'departamento': 0.08,
            'casa': 0.10,
            'cabaña': 0.12, 'cabana': 0.12,
            'villa': 0.15,
            'lujo': 0.18, 'premium': 0.18
        };
        const catNormalizada = categoria.toLowerCase().trim();
        const porcentaje = porcentajes[catNormalizada] || 0.08;
        return parseFloat((subtotal * porcentaje).toFixed(2));
    }
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
            const huespedesVal = document.getElementById('huespedes').value;

            if (end > start) {
                try {
                    const res  = await fetch(`../../apis/huesped/calcular_precio.php?idPropiedad=${idPropiedad}&fechaInicio=${fechaInicioVal}&fechaFin=${fechaFinVal}&huespedes=${huespedesVal}`);
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
                        document.getElementById('summaryBasePrice').innerText = '$' + d.totalBase.toLocaleString('es-MX', { minimumFractionDigits: 2 }) + ' MXN';
                        document.getElementById('summaryCleaning').innerText  = '$' + d.limpieza.toLocaleString('es-MX', { minimumFractionDigits: 2 }) + ' MXN';
                        document.getElementById('summaryTax').innerText       = '$' + d.impuestos.toLocaleString('es-MX', { minimumFractionDigits: 2 }) + ' MXN';
                        document.getElementById('summaryTotal').innerText     = '$' + d.granTotal.toLocaleString('es-MX', { minimumFractionDigits: 2 }) + ' MXN';
                        
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
    
    const maxDate = new Date();
    maxDate.setFullYear(maxDate.getFullYear() + 1);
    const maxDateStr = maxDate.toISOString().split('T')[0];

    const fechaInicioEl = document.getElementById('fechaInicio');
    const fechaFinEl = document.getElementById('fechaFin');

    if (fechaInicioEl) {
        fechaInicioEl.min = todayStr;
        fechaInicioEl.max = maxDateStr;
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

    if (fechaFinEl) {
        fechaFinEl.max = maxDateStr;
    }

    // ── Validación de Huéspedes y Reinicio Automático ──
    const huespedesEl = document.getElementById('huespedes');
    if (huespedesEl) {
        huespedesEl.addEventListener('change', () => {
            const val = parseInt(huespedesEl.value);
            const max = parseInt(window.DETALLE_DATA.capacidadHuespedes);

            if (val > max) {
                alert(`Error: Esta propiedad solo permite un máximo de ${max} huéspedes.`);
                // Reiniciar el apartado automáticamente
                huespedesEl.value = 1; 
                fechaInicioEl.value = '';
                fechaFinEl.value = '';
                document.getElementById('summaryList').style.display = 'none';
                return;
            }
            window.updateReservationSummary();
        });
    }

    // ── Lógica de Reseñas — estrellas ──
    document.querySelectorAll('.star-btn').forEach(star => {
        star.onclick = function () {
            const val = this.getAttribute('data-value');
            // Si estamos editando un comentario existente (idResenia > 0), no permitimos cambiar las estrellas 
            // ya que el requerimiento se enfoca en actualizar el texto del comentario.
            if (document.getElementById('inputIdResenia').value !== '0') return;

            document.getElementById('inputCalificacion').value = val;
            document.querySelectorAll('.star-btn').forEach(s => {
                s.style.color = s.getAttribute('data-value') <= val ? '#fbbf24' : '#cbd5e1';
            });
        };
    });

    // Función para activar el modo edición
    window.editComment = function(id, texto) {
        document.getElementById('inputIdResenia').value = id;
        document.querySelector('textarea[name="vComentario"]').value = texto;
        
        const btn = formResenia.querySelector('button[type="submit"]');
        btn.innerText = 'Actualizar Reseña';
        
        // Hacer scroll al formulario
        document.getElementById('formReseniaBox').scrollIntoView({ behavior: 'smooth' });
        
        // Agregar botón de cancelar si no existe
        if (!document.getElementById('btnCancelarEdicion')) {
            const btnCancel = document.createElement('button');
            btnCancel.type = 'button';
            btnCancel.id = 'btnCancelarEdicion';
            btnCancel.innerText = 'Cancelar Edición';
            btnCancel.className = 'btn';
            btnCancel.style.marginLeft = '1rem';
            btnCancel.style.background = '#f1f5f9';
            btnCancel.style.color = '#475569';
            btnCancel.onclick = function() {
                document.getElementById('inputIdResenia').value = '0';
                document.querySelector('textarea[name="vComentario"]').value = '';
                btn.innerText = 'Enviar Reseña';
                this.remove();
            };
            btn.parentNode.appendChild(btnCancel);
        }
    };

    // ── Envío de reseña ──
    const formResenia = document.getElementById('formResenia');
    if (formResenia) {
        formResenia.onsubmit = async (e) => {
            e.preventDefault();
            
            const idResenia = document.getElementById('inputIdResenia').value;
            const totalActual = window.DETALLE_DATA.totalReseniasHuesped;

            // Verificar límite de 3 (solo si es nuevo)
            if (idResenia === '0' && totalActual >= 3) {
                alert('Has alcanzado el límite máximo de 3 comentarios.');
                return;
            }

            const fd  = new FormData(formResenia);
            const btn = formResenia.querySelector('button[type="submit"]');
            const originalText = btn.innerText;
            btn.disabled  = true;
            btn.innerText = 'Enviando...';

            try {
                const res  = await fetch('../../apis/huesped/resenia.php', { method: 'POST', body: fd });
                const data = await res.json();
                if (data.ok) {
                    // Lógica para limpiar el campo después de enviar correctamente
                    document.querySelector('textarea[name="vComentario"]').value = '';
                    formResenia.reset();
                    document.querySelectorAll('.star-btn').forEach(s => s.style.color = '#cbd5e1');

                    alert(data.mensaje || '¡Reseña enviada correctamente!');
                    
                    // Recargar para ver los cambios
                    window.location.reload();
                } else {
                    alert(data.error || 'Error al enviar reseña');
                    // TAMBIÉN limpiamos el campo si se detecta contenido malicioso o cualquier error del servidor
                    document.querySelector('textarea[name="vComentario"]').value = '';
                    formResenia.reset();
                    document.querySelectorAll('.star-btn').forEach(s => s.style.color = '#cbd5e1');
                }
            } catch (err) {
                console.error(err);
                alert('Error de conexión al enviar la reseña.');
            } finally {
                btn.disabled  = false;
                btn.innerText = originalText;
            }
        };
    }
})();
