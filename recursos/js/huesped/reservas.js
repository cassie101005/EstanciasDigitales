/**
 * reservas.js — Lógica de Mis Reservaciones (Huésped)
 * recursos/js/huesped/reservas.js
 * Requiere: SweetAlert2
 */

function openCommentModal(idRes, idProp, title) {
    document.getElementById('modalIdReserva').value  = idRes;
    document.getElementById('modalIdPropiedad').value = idProp;
    document.getElementById('modalPropTitle').innerText = 'Opinión sobre ' + title;
    document.getElementById('commentModal').style.display = 'flex';
}

function closeCommentModal() {
    document.getElementById('commentModal').style.display = 'none';
    document.getElementById('commentForm').reset();
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
            Swal.fire('¡Gracias!', data.mensaje, 'success').then(() => location.reload());
        } else {
            Swal.fire('Error', data.mensaje, 'error');
        }
    } catch (e) {
        Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
    }
}
