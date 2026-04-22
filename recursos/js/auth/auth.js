async function handleLogin(event) {
    event.preventDefault();
    
    const btn = event.target.querySelector('button[type="submit"]');
    const originalText = btn.innerText;
    btn.innerText = 'Autenticando...';
    btn.disabled = true;

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const role = document.getElementById('role_input').value;

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
