<?php
session_start();
if (isset($_SESSION['idUsuario']) && isset($_SESSION['rol'])) {
    $rol = strtolower($_SESSION['rol']);
    if ($rol === 'admin') {
        header("Location: presentacion/admin/dashboard.php");
    } elseif ($rol === 'anfitrion') {
        header("Location: presentacion/anfitrion/dashboard.php");
    } else {
        header("Location: presentacion/huesped/home.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Login | Estancias Digitales</title>
    <link rel="icon" type="image/png" href="recursos/img/logo_final.png?v=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="recursos/css/login.css">
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>

<body>
    <!-- Auth Container -->
    <main class="auth-main">

        <!-- Left Side: Form -->
        <section class="auth-left">
            <div class="auth-inner">
                <!-- Brand -->
                <div class="auth-brand">
                    <i class="fa-solid fa-house-laptop"></i>
                    <span>Estancias Digitales</span>
                </div>

                <!-- Tab Toggle -->
                <div class="auth-tabs">
                    <button class="auth-tab active">Iniciar sesión</button>
                    <button type="button" onclick="abrirModalRegistro()" class="auth-tab">Crear cuenta</button>
                </div>

                <!-- Headline -->
                <div class="auth-headline">
                    <h1>Bienvenido de nuevo</h1>
                    <p>Accede a las propiedades más exclusivas de la red digital.</p>
                </div>

                <!-- Login Form -->
                <form class="auth-form" onsubmit="handleLogin(event)" autocomplete="off">
                    <div class="form-group">
                        <label class="form-label">Correo electrónico</label>
                        <div class="input-wrapper">
                            <input id="email" class="form-input" required type="email" autocomplete="off" />
                            <span class="material-symbols-outlined input-icon">alternate_email</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-label-row">
                            <label class="form-label">Contraseña</label>
                            <a class="link-forgot" href="javascript:void(0)" onclick="abrirModalReset()">¿Olvidaste tu contraseña?</a>
                        </div>
                        <div class="input-wrapper">
                            <input id="password" class="form-input" required type="password" autocomplete="new-password" />
                            <span class="material-symbols-outlined input-icon">lock</span>
                        </div>
                    </div>

                    <!-- Role Selection -->
                    <div class="form-group">
                        <p class="role-label">Tipo de acceso</p>
                        <div class="role-grid">
                            <button type="button" onclick="selectRole('huesped', this)" class="role-btn active">
                                <span class="material-symbols-outlined role-icon">person</span>
                                <span class="role-text">Viajero</span>
                            </button>
                            <button type="button" onclick="selectRole('anfitrion', this)" class="role-btn">
                                <span class="material-symbols-outlined role-icon">key</span>
                                <span class="role-text">Anfitrión</span>
                            </button>
                        </div>
                    </div>

                    <input type="hidden" id="role_input" value="huesped">

                    <button class="btn-submit" type="submit">Entrar al Portal</button>
                </form>

                <footer class="auth-footer">
                    <p>
                        © 2026 Estancias Digitales.<br />
                        <span>Excelencia en gestión de propiedades.</span>
                    </p>
                </footer>
            </div>
        </section>

        <!-- Right Side: Property Image -->
        <section class="auth-right">
            <div class="auth-right-bg"
                style="background-image: url('https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&q=80&w=2070')">
                <div class="auth-right-overlay"></div>
            </div>
        </section>
    </main>

    <!-- Modal de Registro -->
    <div id="modalRegistro" class="modal-overlay">
        <div class="modal-box" id="modalRegistroBox">
            <button type="button" onclick="cerrarModalRegistro()" class="modal-close">
                <span class="material-symbols-outlined">close</span>
            </button>
            <h2 class="modal-title">Crear nueva cuenta</h2>
            <p class="modal-subtitle">Regístrate como <span id="txtRolSeleccionado" class="role-highlight"></span> para continuar.</p>

            <form onsubmit="handleRegistro(event)" class="auth-form" autocomplete="off">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label-sm">Nombre</label>
                        <input id="regNombre" required class="form-input-sm" autocomplete="off" />
                    </div>
                    <div class="form-group">
                        <label class="form-label-sm">Apellido</label>
                        <input id="regApellido" required class="form-input-sm" autocomplete="off" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label-sm">Correo electrónico</label>
                    <input id="regCorreo" type="email" required class="form-input-sm" autocomplete="off" />
                </div>

                <div class="form-group">
                    <label class="form-label-sm">Contraseña</label>
                    <input id="regPassword" type="password" required class="form-input-sm" autocomplete="new-password" />
                </div>

                <div id="regAlert" class="form-alert" style="display:none;"></div>

                <button type="submit" class="btn-submit-modal">Registrarme</button>
            </form>
        </div>
    </div>

    <!-- Modal de Restablecer Contraseña -->
    <div id="modalReset" class="modal-overlay">
        <div class="modal-box" id="modalResetBox">
            <button type="button" onclick="cerrarModalReset()" class="modal-close">
                <span class="material-symbols-outlined">close</span>
            </button>
            <h2 class="modal-title">Restablecer contraseña</h2>
            <p class="modal-subtitle">Ingresa tu correo y tu nueva contraseña para actualizarla.</p>

            <form onsubmit="handleResetPassword(event)" class="auth-form" autocomplete="off">
                <div class="form-group">
                    <label class="form-label-sm">Correo electrónico</label>
                    <input id="resetCorreo" type="email" required class="form-input-sm" autocomplete="off" />
                </div>

                <div class="form-group">
                    <label class="form-label-sm">Nueva contraseña</label>
                    <input id="resetPassword" type="password" required class="form-input-sm" autocomplete="new-password" />
                </div>

                <div id="resetAlert" class="form-alert" style="display:none;"></div>

                <button type="submit" class="btn-submit-modal">Actualizar Contraseña</button>
            </form>
        </div>
    </div>

    <!-- Background Decorative Blobs -->
    <div class="bg-blobs">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
    </div>

    <script>
        function selectRole(role, element) {
            document.querySelectorAll('.role-btn').forEach(btn => btn.classList.remove('active'));
            element.classList.add('active');
            document.getElementById('role_input').value = role;
        }

        function abrirModalRegistro() {
            document.getElementById('regNombre').value = '';
            document.getElementById('regApellido').value = '';
            document.getElementById('regCorreo').value = '';
            document.getElementById('regPassword').value = '';
            const alert = document.getElementById('regAlert');
            alert.style.display = 'none';
            alert.innerText = '';

            const role = document.getElementById('role_input').value;
            document.getElementById('txtRolSeleccionado').innerText = role === 'huesped' ? 'Viajero' : 'Anfitrión';

            const modal = document.getElementById('modalRegistro');
            modal.classList.add('active');
            setTimeout(() => document.getElementById('modalRegistroBox').classList.add('open'), 10);
        }

        function cerrarModalRegistro() {
            const box = document.getElementById('modalRegistroBox');
            box.classList.remove('open');
            setTimeout(() => document.getElementById('modalRegistro').classList.remove('active'), 300);
        }

        async function handleRegistro(event) {
            event.preventDefault();
            const btn = event.target.querySelector('button[type="submit"]');
            const originalText = btn.innerText;
            btn.innerText = 'Registrando...';
            btn.disabled = true;

            const rol = document.getElementById('role_input').value;
            const payload = {
                idRol: rol === 'huesped' ? 2 : 3,
                nombre: document.getElementById('regNombre').value,
                apellido: document.getElementById('regApellido').value,
                correo: document.getElementById('regCorreo').value,
                contrasenia: document.getElementById('regPassword').value
            };

            try {
                const res = await fetch('./apis/auth/registro.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.ok) {
                    cerrarModalRegistro();
                    alert('Registro exitoso. Ahora puedes iniciar sesión.');
                    event.target.reset();
                } else {
                    const el = document.getElementById('regAlert');
                    el.innerText = data.mensaje || data.error || 'Error en el registro';
                    el.style.display = 'block';
                }
            } catch (err) {
                const el = document.getElementById('regAlert');
                el.innerText = 'Error de conexión';
                el.style.display = 'block';
            } finally {
                btn.innerText = originalText;
                btn.disabled = false;
            }
        }

        function abrirModalReset() {
            document.getElementById('resetCorreo').value = '';
            document.getElementById('resetPassword').value = '';
            const alert = document.getElementById('resetAlert');
            alert.style.display = 'none';
            alert.innerText = '';

            const modal = document.getElementById('modalReset');
            modal.classList.add('active');
            setTimeout(() => document.getElementById('modalResetBox').classList.add('open'), 10);
        }

        function cerrarModalReset() {
            const box = document.getElementById('modalResetBox');
            box.classList.remove('open');
            setTimeout(() => document.getElementById('modalReset').classList.remove('active'), 300);
        }

        async function handleResetPassword(event) {
            event.preventDefault();
            const btn = event.target.querySelector('button[type="submit"]');
            const originalText = btn.innerText;
            btn.innerText = 'Actualizando...';
            btn.disabled = true;

            const payload = {
                correo: document.getElementById('resetCorreo').value,
                nuevaContrasenia: document.getElementById('resetPassword').value
            };

            try {
                const res = await fetch('./apis/auth/reset_password.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.ok) {
                    cerrarModalReset();
                    alert('Contraseña restablecida con éxito. Ya puedes iniciar sesión.');
                    event.target.reset();
                } else {
                    const el = document.getElementById('resetAlert');
                    el.innerText = data.mensaje || data.error || 'Error al restablecer';
                    el.style.display = 'block';
                }
            } catch (err) {
                const el = document.getElementById('resetAlert');
                el.innerText = 'Error de conexión';
                el.style.display = 'block';
            } finally {
                btn.innerText = originalText;
                btn.disabled = false;
            }
        }

        function limpiarCamposLogin() {
            ['email', 'password', 'resetCorreo', 'resetPassword', 'regNombre', 'regApellido', 'regCorreo', 'regPassword']
                .forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
            setTimeout(() => {
                ['email', 'password', 'resetCorreo', 'resetPassword', 'regNombre', 'regApellido', 'regCorreo', 'regPassword']
                    .forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
            }, 100);
        }

        window.addEventListener('load', limpiarCamposLogin);
        window.addEventListener('pageshow', limpiarCamposLogin);
    </script>

    <script src="./recursos/js/auth/auth.js"></script>
</body>

</html>