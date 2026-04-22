<?php
// user_profile_modal.php - Ventanas Design System
// Paleta: Morado #7C3AED | Azul #1E40AF | Fondo #F9FAFB | Texto #111827 | Acento #F59E0B
$role_title = "Viajero";
if ($is_admin ?? false) {
    $role_title = "Administrador de Sistema";
} elseif ($is_host ?? false) {
    $role_title = "Anfitrión";
}
$logout_url = rtrim($base_path ?? '../../', '/') . '/index.php';
?>
<!-- ========== MODAL PERFIL DE USUARIO ========== -->
<div class="profile-modal-overlay" id="profileModal" onclick="closeProfileModal(event)">
    <div class="profile-modal-content" onclick="event.stopPropagation()">

        <!-- Header -->
        <div class="profile-modal-header">
            <div>
                <h2>Perfil de Usuario</h2>
                <p class="role-badge-label"><?php echo $role_title; ?></p>
            </div>
            <button class="btn-close-modal" onclick="closeProfileModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="profile-modal-body">

            <!-- Avatar -->
            <div class="profile-avatar-section">
                <div class="avatar-wrapper">
                    <img src="https://i.pravatar.cc/100?u=<?php echo ($is_host ?? false) ? 'host' : (($is_admin ?? false) ? 'admin' : 'huesped'); ?>" alt="Foto de perfil">
                    <div class="avatar-edit-icon"><i class="fa-solid fa-pencil"></i></div>
                </div>
                <button class="btn-outline-primary">Cambiar foto</button>
            </div>

            <!-- Form -->
            <form id="profileForm" onsubmit="event.preventDefault()">

                <!-- Nombre / Apellido -->
                <div class="form-row-2">
                    <div class="form-group-modal">
                        <label>Nombre</label>
                        <input type="text" value="Ricardo" class="modal-input" placeholder="Tu nombre">
                    </div>
                    <div class="form-group-modal">
                        <label>Apellido</label>
                        <input type="text" value="García" class="modal-input" placeholder="Tu apellido">
                    </div>
                </div>

                <!-- Fecha de nacimiento -->
                <div class="form-group-modal">
                    <label>Fecha de nacimiento</label>
                    <div class="input-icon-right">
                        <input type="date" value="1985-05-12" class="modal-input">
                        <i class="fa-regular fa-calendar"></i>
                    </div>
                </div>

                <!-- Sección Contacto -->
                <div class="modal-section-title">
                    <i class="fa-regular fa-envelope"></i> CONTACTO
                </div>

                <div class="form-group-modal">
                    <label>Email</label>
                    <input type="email" value="ricardo.garcia@estancias.com" class="modal-input" placeholder="correo@ejemplo.com">
                </div>

                <div class="form-group-modal">
                    <label>Teléfono</label>
                    <input type="text" value="+34 600 000 000" class="modal-input" placeholder="+52 000 000 0000">
                </div>

                <!-- Sección Seguridad -->
                <div class="modal-section-title">
                    <i class="fa-solid fa-lock"></i> SEGURIDAD
                </div>

                <div class="form-group-modal">
                    <label>Contraseña actual</label>
                    <div class="input-icon-right">
                        <input type="password" id="pw_current" value="password123" class="modal-input">
                        <i class="fa-regular fa-eye" style="cursor:pointer;" onclick="togglePw('pw_current', this)"></i>
                    </div>
                </div>

                <div class="form-group-modal" style="margin-bottom: 0.5rem;">
                    <label>Confirmar contraseña</label>
                    <div class="input-icon-right">
                        <input type="password" id="pw_confirm" value="" class="modal-input" placeholder="Nueva contraseña">
                        <i class="fa-regular fa-eye" style="cursor:pointer;" onclick="togglePw('pw_confirm', this)"></i>
                    </div>
                </div>

            </form>
        </div>

        <!-- Footer -->
        <div class="profile-modal-footer">
            <div class="footer-actions-row">
                <button type="button" class="btn-modal-primary" onclick="closeProfileModal()">Guardar cambios</button>
                <button type="button" class="btn-modal-secondary" onclick="closeProfileModal()">Cancelar</button>
            </div>
            <button type="button" class="btn-modal-logout" onclick="window.location.href='<?php echo $logout_url; ?>'">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Cerrar sesión
            </button>
        </div>

    </div>
</div>

<script>
// Portal: mover el modal al body para evitar conflictos de z-index con sidebars/grids
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('profileModal');
    if (modal && modal.parentNode !== document.body) {
        document.body.appendChild(modal);
    }
});

function openProfileModal() {
    var modal = document.getElementById('profileModal');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeProfileModal(e) {
    var modal = document.getElementById('profileModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

function togglePw(inputId, icon) {
    var input = document.getElementById(inputId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
