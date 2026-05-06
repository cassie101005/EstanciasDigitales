async function handleLogin(event) {
    event.preventDefault();
    
    const btn = event.target.querySelector('button[type="submit"]');
    const originalText = btn.innerText;
    btn.innerText = 'Autenticando...';
    btn.disabled = true;

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const role = document.getElementById('role_input').value;

    // Limpiar campos después de capturar los valores
    document.getElementById('email').value = '';
    document.getElementById('password').value = '';

    const formData = new FormData();

    formData.append('correo', email);
    formData.append('contrasenia', password);
    formData.append('rol', role);

    try {
        const response = await fetch('./apis/auth/login.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();

        if (data.ok) {
            // El backend responde con el redireccionamiento adecuado
            window.location.href = data.redirect;
        } else {
            alert(data.mensaje || data.error || 'Error de credenciales');
            btn.innerText = originalText;
            btn.disabled = false;
        }
    } catch (error) {
        console.error('Error de red:', error);
        alert('Asegúrate de que el servidor MySQL (XAMPP/WAMP) esté encendido.');
        btn.innerText = originalText;
        btn.disabled = false;
    }
}

function selectRole(role, element) {
    document.querySelectorAll('.role-btn').forEach(btn => btn.classList.remove('active'));
    element.classList.add('active');
    document.getElementById('role_input').value = role;
}

function abrirModalRegistro() {
    document.getElementById('regNombre').value = '';
    document.getElementById('regApellido').value = '';
    document.getElementById('regFechaNac').value = '';
    document.getElementById('regCorreo').value = '';
    document.getElementById('regPassword').value = '';
    const alertBox = document.getElementById('regAlert');
    if (alertBox) {
        alertBox.style.display = 'none';
        alertBox.innerText = '';
    }

    const role = document.getElementById('role_input').value;
    const txtRol = document.getElementById('txtRolSeleccionado');
    if (txtRol) {
        txtRol.innerText = role === 'huesped' ? 'Viajero' : 'Anfitrión';
    }

    const modal = document.getElementById('modalRegistro');
    if (modal) {
        modal.classList.add('active');
        const box = document.getElementById('modalRegistroBox');
        if (box) setTimeout(() => box.classList.add('open'), 10);
    }
}

function cerrarModalRegistro() {
    const box = document.getElementById('modalRegistroBox');
    if (box) {
        box.classList.remove('open');
        setTimeout(() => {
            const modal = document.getElementById('modalRegistro');
            if (modal) modal.classList.remove('active');
        }, 300);
    }
}

async function handleRegistro(event) {
    event.preventDefault();
    const btn = event.target.querySelector('button[type="submit"]');
    const originalText = btn.innerText;
    btn.innerText = 'Registrando...';
    btn.disabled = true;

    const fechaNacVal = document.getElementById('regFechaNac').value;
    if (!fechaNacVal) {
        const el = document.getElementById('regAlert');
        if (el) {
            el.innerText = 'Debe ingresar su fecha de nacimiento.';
            el.style.display = 'block';
        }
        btn.innerText = originalText;
        btn.disabled = false;
        return;
    }

    const birthDate = new Date(fechaNacVal);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    
    if (age < 18) {
        const el = document.getElementById('regAlert');
        if (el) {
            el.innerText = 'Debes ser mayor de 18 años para registrarte.';
            el.style.display = 'block';
        }
        btn.innerText = originalText;
        btn.disabled = false;
        return;
    }

    const rol = document.getElementById('role_input').value;
    const payload = {
        idRol: rol === 'huesped' ? 2 : 3,
        nombre: document.getElementById('regNombre').value,
        apellido: document.getElementById('regApellido').value,
        fechaNacimiento: fechaNacVal,
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
        if (data.success) {
            alert('Cuenta creada exitosamente');
            cerrarModalRegistro();
            window.location.href = "index.php";
        } else {
            const el = document.getElementById('regAlert');
            if (el) {
                el.innerText = data.message || 'Error en el registro';
                el.style.display = 'block';
            }
        }
    } catch (err) {
        const el = document.getElementById('regAlert');
        if (el) {
            el.innerText = 'Error de conexión';
            el.style.display = 'block';
        }
    } finally {
        btn.innerText = originalText;
        btn.disabled = false;
    }
}

function abrirModalReset() {
    document.getElementById('resetCorreo').value = '';
    document.getElementById('resetPassword').value = '';
    const alertBox = document.getElementById('resetAlert');
    if (alertBox) {
        alertBox.style.display = 'none';
        alertBox.innerText = '';
    }

    const modal = document.getElementById('modalReset');
    if (modal) {
        modal.classList.add('active');
        const box = document.getElementById('modalResetBox');
        if (box) setTimeout(() => box.classList.add('open'), 10);
    }
}

function cerrarModalReset() {
    const box = document.getElementById('modalResetBox');
    if (box) {
        box.classList.remove('open');
        setTimeout(() => {
            const modal = document.getElementById('modalReset');
            if (modal) modal.classList.remove('active');
        }, 300);
    }
}

async function handleResetPassword(event) {
    event.preventDefault();
    const btn = event.target.querySelector('button[type="submit"]');
    const originalText = btn.innerText;
    btn.innerText = 'Actualizando...';
    btn.disabled = true;

    const passwordInput = document.getElementById('resetPassword').value;
    document.getElementById('resetPassword').value = ''; // Limpiar inmediatamente por seguridad
    
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d\w\W]{8,}$/;
    if (!regex.test(passwordInput)) {
        const el = document.getElementById('resetAlert');
        if (el) {
            el.innerText = 'La contraseña debe tener mínimo 8 caracteres, incluir mayúsculas, minúsculas y números.';
            el.style.display = 'block';
        }
        btn.innerText = originalText;
        btn.disabled = false;
        return;
    }

    const payload = {
        correo: document.getElementById('resetCorreo').value,
        nuevaContrasenia: passwordInput
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
            if (el) {
                el.innerText = data.mensaje || data.error || 'Error al restablecer';
                el.style.display = 'block';
            }
        }
    } catch (err) {
        const el = document.getElementById('resetAlert');
        if (el) {
            el.innerText = 'Error de conexión';
            el.style.display = 'block';
        }
    } finally {
        btn.innerText = originalText;
        btn.disabled = false;
    }
}

function limpiarCamposLogin() {
    const fields = ['email', 'password', 'resetCorreo', 'resetPassword', 'regNombre', 'regApellido', 'regFechaNac', 'regCorreo', 'regPassword'];
    fields.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
}

// Event Listeners for cleaning fields
window.addEventListener('load', () => {
    limpiarCamposLogin();
    setTimeout(limpiarCamposLogin, 100);
    setTimeout(limpiarCamposLogin, 500);
});
window.addEventListener('pageshow', limpiarCamposLogin);
