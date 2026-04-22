<?php
// user_profile_modal.php
$role_title = "Viajero";
if ($is_admin ?? false) {
    $role_title = "Administrador de Sistema";
} else if ($is_host ?? false) {
    $role_title = "Anfitrión";
}
?>
<!-- Profile Modal Overlay -->
<div class="profile-modal-overlay" id="profileModal" onclick="closeProfileModal(event)">
    <div class="profile-modal-content" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="profile-modal-header">
            <div>
                <h2>Perfil de Usuario</h2>
                <p><?php echo $role_title; ?></p>
            </div>
            <button class="btn-close-modal" onclick="closeProfileModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Scrollable Body -->
        <div class="profile-modal-body">
            <!-- Avatar Section -->
            <div class="profile-avatar-section">
                <div class="avatar-wrapper">
                    <img src="https://i.pravatar.cc/100?u=<?php echo ($is_host ?? false) ? 'host' : 'huesped'; ?>" alt="Profile">
                    <div class="avatar-edit-icon"><i class="fa-solid fa-pencil"></i></div>
                </div>
                <button class="btn-outline-primary">Cambiar foto</button>
            </div>

            <!-- Form -->
            <form id="profileForm">
                <div class="form-row-2">
                    <div class="form-group-modal">
                        <label>Nombre</label>
                        <input type="text" value="Ricardo" class="modal-input">
                    </div>
                    <div class="form-group-modal">
                        <label>Apellido</label>
                        <input type="text" value="García" class="modal-input">
                    </div>
                </div>

                <div class="form-group-modal">
                    <label>Fecha de nacimiento</label>
                    <div class="input-icon-right">
                        <input type="text" value="12/05/1985" class="modal-input">
                        <i class="fa-regular fa-calendar"></i>
                    </div>
                </div>

                <div class="modal-section-title">
                    <i class="fa-regular fa-envelope"></i> CONTACTO
                </div>
                <div class="form-group-modal">
                    <label>Email</label>
                    <input type="email" value="ricardo.garcia@estancias.com" class="modal-input">
                </div>
                <div class="form-group-modal">
                    <label>Teléfono</label>
                    <input type="text" value="+34 600 000 000" class="modal-input">
                </div>

                <div class="modal-section-title">
                    <i class="fa-solid fa-lock" style="font-size: 12px;"></i> SEGURIDAD
                </div>
                <div class="form-group-modal">
                    <label>Contraseña actual</label>
                    <div class="input-icon-right">
                        <input type="password" value="password123" class="modal-input">
                        <i class="fa-regular fa-eye"></i>
                    </div>
                </div>
                <div class="form-group-modal" style="margin-bottom: 2rem;">
                    <label>Confirmar contraseña</label>
                    <input type="password" value="" class="modal-input">
                </div>
            </form>
        </div>

        <!-- Footer Actions -->
        <div class="profile-modal-footer">
            <div class="footer-actions-row">
                <button type="button" class="btn-modal-primary" onclick="closeProfileModal()">Guardar cambios</button>
                <button type="button" class="btn-modal-secondary" onclick="closeProfileModal()">Cancelar</button>
            </div>
            <button type="button" class="btn-modal-logout" onclick="window.location.href='<?php echo rtrim($base_path ?? '', '/'); ?>/index.php'">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Cerrar sesión
            </button>
        </div>
    </div>
</div>

<script>
// Mover el modal al nivel del body para escapar cualquier stacking context del grid/sidebar
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('profileModal');
    if (modal && modal.parentNode !== document.body) {
        document.body.appendChild(modal);
    }
});

function openProfileModal() {
    document.getElementById('profileModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeProfileModal(e) {
    document.getElementById('profileModal').classList.remove('active');
    document.body.style.overflow = '';
}
</script>
