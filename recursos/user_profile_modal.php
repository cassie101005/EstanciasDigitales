<?php
// user_profile_modal.php - Ventanas Design System
require_once __DIR__ . '/../datos/conexion.php';
require_once __DIR__ . '/../datos/auth/queries_auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userData = [
    'vNombre' => '',
    'vApellido' => '',
    'dFechaNacimiento' => '',
    'vCorreo' => '',
    'vTelefono' => '',
    'vContrasenia' => '',
    'vNombreRol' => 'Viajero'
];

if (isset($_SESSION['idUsuario'])) {
    $queriesAuth = new QueriesAuth($conexion);
    $resUser = $queriesAuth->obtenerUsuarioPorId($_SESSION['idUsuario']);
    if ($resUser && $resUser->num_rows > 0) {
        $userData = $resUser->fetch_assoc();
    }
}

$role_title = $userData['vNombreRol'] ?? "Viajero";
if ($role_title === 'admin') $role_title = "Administrador de Sistema";
elseif ($role_title === 'anfitrion') $role_title = "Anfitrión";
elseif ($role_title === 'huesped') $role_title = "Viajero";

$logout_url = rtrim($base_path ?? '../../', '/') . '/negocio/auth/logout.php';
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
                    <img id="profilePreview" src="<?php echo !empty($userData['vFoto']) ? ($base_path . $userData['vFoto']) : 'https://i.pravatar.cc/100?u=' . ($_SESSION['idUsuario'] ?? 'default'); ?>" alt="Foto de perfil">
                    <div class="avatar-edit-icon" onclick="document.getElementById('avatarInput').click()"><i class="fa-solid fa-pencil"></i></div>
                </div>
                <input type="file" id="avatarInput" name="fotoPerfil" accept="image/*" style="display: none;" onchange="previewImage(this)">
                <button type="button" class="btn-outline-primary" onclick="document.getElementById('avatarInput').click()">Cambiar foto</button>
            </div>

            <!-- Form -->
            <form id="profileForm" onsubmit="event.preventDefault()">

                <!-- Nombre / Apellido -->
                <div class="form-row-2">
                    <div class="form-group-modal">
                        <label>Nombre</label>
                        <input type="text" name="nombre" value="<?php echo htmlspecialchars($userData['vNombre'] ?? ''); ?>" class="modal-input" placeholder="Tu nombre">
                    </div>
                    <div class="form-group-modal">
                        <label>Apellido</label>
                        <input type="text" name="apellido" value="<?php echo htmlspecialchars($userData['vApellido'] ?? ''); ?>" class="modal-input" placeholder="Tu apellido">
                    </div>
                </div>

                <!-- Fecha de nacimiento -->
                <div class="form-group-modal">
                    <label>Fecha de nacimiento</label>
                    <div class="input-icon-right">
                        <input type="date" name="fechaNacimiento" value="<?php echo (isset($userData['dFechaNacimiento']) && $userData['dFechaNacimiento'] !== '0000-00-00') ? $userData['dFechaNacimiento'] : ''; ?>" class="modal-input">
                        <i class="fa-regular fa-calendar"></i>
                    </div>
                </div>

                <!-- Sección Contacto -->
                <div class="modal-section-title">
                    <i class="fa-regular fa-envelope"></i> CONTACTO
                </div>

                <div class="form-group-modal">
                    <label>Email</label>
                    <input type="email" name="correo" value="<?php echo htmlspecialchars($userData['vCorreo'] ?? ''); ?>" class="modal-input" placeholder="correo@ejemplo.com">
                </div>

                <div class="form-group-modal">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" value="<?php echo htmlspecialchars($userData['vTelefono'] ?? ''); ?>" class="modal-input" placeholder="Solo números" inputmode="numeric" onkeypress="return /[0-9]/.test(event.key)">
                </div>

                <!-- Sección Seguridad -->
                <div class="modal-section-title">
                    <i class="fa-solid fa-lock"></i> SEGURIDAD
                </div>

                <div class="form-group-modal">
                    <label>Contraseña actual</label>
                    <div class="input-icon-right">
                        <input type="password" name="contrasenia" id="pw_current" value="" class="modal-input" placeholder="Dejar en blanco para no cambiar">
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
                <button type="button" class="btn-modal-primary" onclick="guardarCambiosPerfil()">Guardar cambios</button>
                <button type="button" class="btn-modal-secondary" onclick="closeProfileModal()">Cancelar</button>
            </div>
            <button type="button" class="btn-modal-logout" onclick="window.location.href='<?php echo $logout_url; ?>'">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Cerrar sesión
            </button>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Portal: mover el modal al body para evitar conflictos de z-index con sidebars/grids
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('profileModal');
    if (modal && modal.parentNode !== document.body) {
        document.body.appendChild(modal);
    }
});

let initialProfileData = null;

function getProfileFormData() {
    const form = document.getElementById('profileForm');
    const formData = new FormData(form);
    const data = {};
    // Capturamos solo los campos de texto/fecha para comparar cambios simples
    formData.forEach((value, key) => {
        if (key !== 'contrasenia') data[key] = value;
    });
    return JSON.stringify(data);
}

function openProfileModal() {
    var modal = document.getElementById('profileModal');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        // Capturar estado inicial
        initialProfileData = getProfileFormData();
    }
}

async function closeProfileModal(e) {
    // Si el clic fue dentro del contenido y no fue el botón de cerrar, ignorar
    if (e && e.target !== e.currentTarget && e.target.id !== 'profileModal' && !e.target.closest('.btn-close-modal')) return;
    
    var modal = document.getElementById('profileModal');
    if (!modal || !modal.classList.contains('active')) return;

    // Forzar que SweetAlert esté siempre al frente
    const styleNotif = document.getElementById('swal-zindex-fix') || document.createElement('style');
    styleNotif.id = 'swal-zindex-fix';
    styleNotif.innerHTML = '.swal2-container { z-index: 999999 !important; }';
    if (!styleNotif.parentNode) document.head.appendChild(styleNotif);

    const currentData = getProfileFormData();
    
    if (initialProfileData !== null && currentData !== initialProfileData) {
        const result = await Swal.fire({
            title: '¿Deseas guardar los cambios?',
            text: "Has realizado modificaciones en tu perfil que no se han guardado.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: 'var(--primary)',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'No, descartar',
            reverseButtons: true
        });

        if (result.isConfirmed) {
            // Validar que no haya campos vacíos (excepto contraseña)
            const form = document.getElementById('profileForm');
            const requiredFields = ['nombre', 'apellido', 'fechaNacimiento', 'correo', 'telefono'];
            let hasEmpty = false;
            
            requiredFields.forEach(fieldName => {
                const input = form.querySelector(`[name="${fieldName}"]`);
                if (input && input.value.trim() === '') {
                    hasEmpty = true;
                    input.style.borderColor = 'red';
                } else if (input) {
                    input.style.borderColor = '';
                }
            });

            if (hasEmpty) {
                Swal.fire({
                    icon: 'error',
                    title: 'Campos incompletos',
                    text: 'Los campos de perfil no se pueden quedar vacíos.'
                });
                return;
            }

            await guardarCambiosPerfil();
            return;
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            // Descartar: recargar para limpiar cambios visuales
            location.reload();
            return;
        } else {
            // Si solo cerró el SweetAlert sin elegir, no cerramos el modal
            return;
        }
    }

    modal.classList.remove('active');
    document.body.style.overflow = '';
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

function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profilePreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

async function guardarCambiosPerfil() {
    const form = document.getElementById('profileForm');
    const formData = new FormData(form);
    
    // Agregar archivo de imagen si se seleccionó
    const avatarFile = document.getElementById('avatarInput').files[0];
    if (avatarFile) {
        formData.append('fotoPerfil', avatarFile);
    }

    const email = formData.get('correo');
    const telefono = formData.get('telefono');

    // Validación de Correo
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        Swal.fire({ icon: 'error', title: 'Email Inválido', text: 'Por favor ingresa un correo con @ y una extensión válida (ej. .com)' });
        return;
    }

    // Validación de Teléfono (Numérico)
    if (telefono && !/^\d+$/.test(telefono)) {
        Swal.fire({ icon: 'error', title: 'Teléfono Inválido', text: 'El teléfono debe contener solo números.' });
        return;
    }

    const btn = document.querySelector('.btn-modal-primary');
    const originalText = btn.innerText;
    btn.innerText = 'Guardando...';
    btn.disabled = true;

    try {
        // Forzar que SweetAlert esté siempre al frente con un z-index superior
        const style = document.createElement('style');
        style.innerHTML = '.swal2-container { z-index: 999999 !important; }';
        document.head.appendChild(style);

        const response = await fetch('<?php echo $base_path; ?>apis/auth/actualizar_perfil.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.ok) {
            Swal.fire({
                icon: 'success',
                title: '¡Actualizado!',
                text: data.mensaje,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.mensaje
            });
            btn.innerText = originalText;
            btn.disabled = false;
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de red',
            text: 'No se pudo conectar con el servidor.'
        });
        btn.innerText = originalText;
        btn.disabled = false;
    }
}
</script>
