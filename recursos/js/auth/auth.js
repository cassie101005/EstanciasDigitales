function handleLogin(event) {
    event.preventDefault();
    const role = document.getElementById('role_input').value;
    const btn = event.target.querySelector('.btn-primary-modern');
    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Cargando...';
    btn.disabled = true;

    // Navegación desde el raíz a la carpeta presentacion
    setTimeout(() => {
        if (role === 'huesped') window.location.href = './presentacion/huesped/home.php';
        else if (role === 'anfitrion') window.location.href = './presentacion/anfitrion/dashboard.php';
        else if (role === 'admin') window.location.href = './presentacion/admin/dashboard.php';
    }, 800);
}
