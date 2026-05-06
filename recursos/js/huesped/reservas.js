/**
 * reservas.js — Lógica de Mis Reservaciones (Huésped)
 * recursos/js/huesped/reservas.js
 * Requiere: SweetAlert2
 */

let currentPage = 1;
const rowsPerPage = 5;

function renderTable() {
    const filas = Array.from(document.querySelectorAll('.res-card-row'));
    if (filas.length === 0) return;

    const totalRows = filas.length;
    const totalPages = Math.ceil(totalRows / rowsPerPage);

    // Ocultar todas las filas primero
    filas.forEach(f => f.style.display = 'none');

    // Mostrar solo las de la página actual
    const start = (currentPage - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    
    filas.slice(start, end).forEach(f => f.style.display = 'flex');

    renderPagination(totalPages);
}

function renderPagination(totalPages) {
    const container = document.getElementById('paginationContainer');
    if (!container) return;

    if (totalPages <= 1) {
        container.innerHTML = '';
        return;
    }

    let html = `
        <button onclick="changePage(${currentPage - 1})" class="pagination-btn ${currentPage === 1 ? 'disabled' : ''}">
            <i class="fa-solid fa-chevron-left"></i> Anterior
        </button>
    `;

    for (let i = 1; i <= totalPages; i++) {
        html += `
            <button onclick="changePage(${i})" class="pagination-btn ${currentPage === i ? 'active' : ''}">
                ${i}
            </button>
        `;
    }

    html += `
        <button onclick="changePage(${currentPage + 1})" class="pagination-btn ${currentPage === totalPages ? 'disabled' : ''}">
            Siguiente <i class="fa-solid fa-chevron-right"></i>
        </button>
    `;

    container.innerHTML = html;
}

function changePage(page) {
    currentPage = page;
    renderTable();
    // Scroll suave hacia arriba de la lista
    document.querySelector('.reservations-list').scrollIntoView({ behavior: 'smooth' });
}

// Inicializar al cargar
document.addEventListener('DOMContentLoaded', () => {
    renderTable();
});

function openCommentModal(idRes, idProp, title) {
    document.getElementById('modalIdReserva').value  = idRes;
    document.getElementById('modalIdPropiedad').value = idProp;
    document.getElementById('modalPropTitle').innerText = 'Opinión sobre ' + title;
    document.getElementById('commentModal').style.display = 'flex';
}

function closeCommentModal() {
    document.getElementById('commentModal').style.display = 'none';
    const form = document.getElementById('commentForm');
    if (form) {
        const txt = form.querySelector('textarea');
        if (txt) txt.value = '';
        form.reset();
    }
}

async function saveComment() {
    const formData = new FormData(document.getElementById('commentForm'));
    try {
        const response = await fetch('../../apis/huesped/guardar_comentario.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (data.ok) {
            Swal.fire('¡Gracias!', data.mensaje, 'success').then(() => {
                closeCommentModal();
                const idReserva = document.getElementById('modalIdReserva').value;
                const btns = document.querySelectorAll(`button[onclick*="openCommentModal(${idReserva},"]`);
                btns.forEach(btn => btn.style.display = 'none');
            });
        } else {
            Swal.fire('Error', data.mensaje, 'error');
        }
    } catch (e) {
        Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
    }
}
