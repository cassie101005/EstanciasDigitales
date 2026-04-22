<?php
// Shared Navbar Component - Dynamic Layout
$is_root = $is_root ?? false;
$base_path = $is_root ? "" : "../../";
$current_page = basename($_SERVER['PHP_SELF']);
$is_reservas = ($current_page == 'reservas.php');
$is_host = strpos($_SERVER['REQUEST_URI'], '/anfitrion/') !== false;
$is_admin = strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;
$hide_search = $hide_search ?? false;

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
                <i class="fa-solid fa-house-laptop"></i> Estancias Digitales
            </div>
        <?php endif; ?>
        <?php if (($is_host || $is_admin) && !$hide_search): ?>
            <!-- Search bar for Host/Admin -->
            <div class="nav-search-pill host-search-desktop" style="width: 400px;">
                <i class="fa-solid fa-magnifying-glass" style="opacity: 0.4;"></i>
                <input type="text" placeholder="<?php echo $host_placeholder; ?>">
            </div>
        <?php elseif (!$is_host && !$is_admin): ?>
            <?php if ($is_reservas): ?>
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
        </div>
        <div class="nav-profile-avatar" onclick="openProfileModal()" style="cursor:pointer;">
            <img src="https://i.pravatar.cc/100?u=<?php echo ($is_host ?? false) ? 'host' : 'huesped'; ?>" alt="Perfil">
        </div>
    </div>
</nav>

<script>
function toggleHostSidebar() {
    const sidebar = document.querySelector('.sidebar-host');
    if (sidebar) {
        sidebar.classList.toggle('mobile-active');
    }
}
</script>

<?php include __DIR__ . '/user_profile_modal.php'; ?>
