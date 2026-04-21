<?php
// Shared Navbar Component - Dynamic Layout
$is_root = $is_root ?? false;
$base_path = $is_root ? "" : "../../";
$current_page = basename($_SERVER['PHP_SELF']);
$is_reservas = ($current_page == 'reservas.php');
$is_host = strpos($_SERVER['REQUEST_URI'], '/anfitrion/') !== false;
$is_admin = strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;

// Dynamic Search Placeholder (Host & Admin Mode)
$host_placeholder = "Buscar...";
if ($is_host || $is_admin) {
    if ($current_page == "dashboard.php") {
        $host_placeholder = $is_admin ? "Buscar reservas, huéspedes o anfitriones..." : "Buscar reservas";
    } elseif ($current_page == "propiedades.php") {
        $host_placeholder = "Buscar propiedades";
    } elseif ($current_page == "calendario.php") {
        $host_placeholder = "Buscar reservas o propiedades";
    } elseif ($current_page == "reservas.php") {
        $host_placeholder = "Buscar reservas";
    }
}
?>
<nav class="nav-huesped <?php echo ($is_host || $is_admin) ? 'nav-is-host' : ''; ?>">
    <!-- Left Section -->
    <div class="nav-left-group">
        <?php if ($is_host || $is_admin): ?>
            <div class="host-mobile-toggle" onclick="toggleHostSidebar()">
                <i class="fa-solid fa-bars"></i>
            </div>
        <?php else: ?>
            <div class="logo-branded" onclick="window.location.href='<?php echo $base_path; ?>index.php'">
                <i class="fa-solid fa-house-laptop"></i> EstanciasDigitales
            </div>
        <?php endif; ?>
        
        <?php if ($is_host || $is_admin): ?>
            <!-- Search bar for Host/Admin -->
            <div class="nav-search-pill host-search-desktop" style="width: 400px; background: #f8fafc; border: 1px solid #e2e8f0;">
                <i class="fa-solid fa-magnifying-glass" style="opacity: 0.4;"></i>
                <input type="text" placeholder="<?php echo $host_placeholder; ?>">
            </div>
        <?php elseif ($is_reservas): ?>
            <!-- Search bar exclusive for Reservas Huésped -->
            <div class="nav-search-pill">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Buscar estancias..." readonly>
            </div>
        <?php else: ?>
            <!-- Menu links next to logo for non-reservas guest pages -->
            <div class="nav-links-left">
                <a href="<?php echo ($is_root ? "presentacion/huesped/" : ""); ?>home.php" class="nav-link <?php echo $current_page == 'home.php' ? 'active' : ''; ?>">Marketplace</a>
                <a href="<?php echo ($is_root ? "presentacion/huesped/" : ""); ?>reservas.php" class="nav-link <?php echo $current_page == 'reservas.php' ? 'active' : ''; ?>">Reservaciones</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Section -->
    <div class="nav-right-group">
        <?php if ($is_host || $is_admin): ?>
            <span class="host-mode-link" onclick="window.location.href='<?php echo $base_path; ?>presentacion/huesped/home.php'">Modo Huésped</span>
        <?php else: ?>
            <span class="host-mode-link" onclick="window.location.href='<?php echo ($is_root ? "presentacion/anfitrion/" : "../anfitrion/"); ?>dashboard.php'">Modo Anfitrión</span>
        <?php endif; ?>
        
        <div class="nav-icons-box">
            <i class="fa-regular fa-bell"></i>
            <i class="fa-regular fa-message"></i>
        </div>
        <div class="nav-profile-avatar">
            <img src="https://i.pravatar.cc/100?u=<?php echo $is_host ? 'host' : 'huesped'; ?>" alt="Profile">
        </div>
    </div>
</nav>
<style>
/* Global Navbar Component Styles */
.nav-huesped {
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: white;
    padding: 0 4rem;
    position: sticky;
    top: 0;
    z-index: 999; /* Below sidebar but above content */
    border-bottom: 1px solid #f1f3f5;
}

.nav-left-group {
    display: flex;
    align-items: center;
    gap: 3rem;
}

.logo-branded {
    color: #1e40af;
    font-size: 1.15rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    white-space: nowrap;
}

.nav-links-left {
    display: flex;
    gap: 2rem;
}

.nav-link {
    font-size: 14px;
    font-weight: 700;
    color: #8c92a5;
    text-decoration: none;
    transition: color 0.2s;
}
.nav-link.active {
    color: #1e40af;
}

/* Search Pill */
.nav-search-pill {
    background: #f1f3f9;
    padding: 0.5rem 1.5rem;
    border-radius: 999px;
    display: flex;
    align-items: center;
    gap: 1rem;
    width: 320px;
}
.nav-search-pill input {
    background: transparent;
    border: none;
    outline: none;
    font-size: 14px;
    font-weight: 500;
}

.nav-right-group {
    display: flex;
    align-items: center;
    gap: 2rem;
}

.host-mode-link {
    font-size: 14px;
    font-weight: 800;
    color: #1e40af;
    cursor: pointer;
}

.nav-icons-box {
    display: flex;
    gap: 1.5rem;
    font-size: 1.25rem;
    color: #495057;
    opacity: 0.7;
}

.host-mobile-toggle {
    display: none;
    font-size: 1.5rem;
    color: #1e40af;
    cursor: pointer;
}

.nav-profile-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    overflow: hidden;
    background: #eee;
}
.nav-profile-avatar img { width: 100%; height: 100%; object-fit: cover; }

@media (max-width: 1024px) {
    .nav-huesped { 
        padding: 0 1.5rem; 
    }
    .host-mobile-toggle {
        display: block;
    }
    .host-search-desktop {
        display: none !important;
    }
}

@media (max-width: 1000px) {
    .nav-left-group { gap: 1rem; }
    .nav-search-pill { display: none; }
}
</style>

<script>
function toggleHostSidebar() {
    const sidebar = document.querySelector('.sidebar-host');
    if (sidebar) {
        sidebar.classList.toggle('mobile-active');
    }
}
</script>
