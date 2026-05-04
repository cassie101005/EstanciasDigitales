<aside class="sidebar-host">
    <div style="display: flex; flex-direction: column; gap: 1rem;">
        <div class="host-logo-box">
            <h2 style="font-size: 1.3rem; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-house-laptop"></i>
                Estancias Digitales
            </h2>
            <p>Modo Anfitrión</p>
        </div>
        
        <nav class="side-nav-host">
            <li class="side-nav-item <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>" onclick="window.location.href='dashboard.php'"><i class="fa-solid fa-house"></i> Inicio</li>
            <li class="side-nav-item <?= in_array(basename($_SERVER['PHP_SELF']), ['propiedades.php','nueva-propiedad.php','editar-propiedad.php']) ? 'active' : '' ?>" onclick="window.location.href='propiedades.php'"><i class="fa-solid fa-building"></i> Propiedades</li>
            <li class="side-nav-item <?= basename($_SERVER['PHP_SELF']) == 'calendario.php' ? 'active' : '' ?>" onclick="window.location.href='calendario.php'"><i class="fa-solid fa-calendar-days"></i> Calendario</li>
            <li class="side-nav-item <?= basename($_SERVER['PHP_SELF']) == 'reservas.php' ? 'active' : '' ?>" onclick="window.location.href='reservas.php'"><i class="fa-solid fa-receipt"></i> Reservas</li>
        </nav>
    </div>
</aside>
