/**
 * home.js — Lógica del Marketplace (Huésped)
 * recursos/js/huesped/home.js
 */
document.addEventListener('DOMContentLoaded', function () {
    const startInput = document.querySelector('input[name="fecha_inicio"]');
    const endInput   = document.querySelector('input[name="fecha_fin"]');

    if (!startInput || !endInput) return;

    // Fecha local de hoy en formato YYYY-MM-DD
    const now   = new Date();
    const year  = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day   = String(now.getDate()).padStart(2, '0');
    const today = `${year}-${month}-${day}`;

    startInput.setAttribute('min', today);

    const updateMinEnd = () => {
        if (startInput.value) {
            endInput.setAttribute('min', startInput.value);
            if (endInput.value && endInput.value < startInput.value) {
                endInput.value = startInput.value;
            }
        } else {
            endInput.setAttribute('min', today);
        }
    };

    startInput.addEventListener('change', updateMinEnd);
    updateMinEnd(); // ejecutar al cargar si ya hay valores
});
