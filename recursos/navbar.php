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
$foto_navbar = 'https://i.pravatar.cc/100?u=' . ($_SESSION['idUsuario'] ?? 'default');

if (isset($_SESSION['idUsuario'])) {
    if (!isset($conexion)) {
        require_once __DIR__ . '/../datos/conexion.php';
    }
    if (isset($conexion)) {
        $stmtNavUser = $conexion->prepare("SELECT vFoto FROM tbl_usuarios WHERE idUsuario = ?");
        if ($stmtNavUser) {
            $stmtNavUser->bind_param("i", $_SESSION['idUsuario']);
            $stmtNavUser->execute();
            $resNavUser = $stmtNavUser->get_result();
            if ($rowNavUser = $resNavUser->fetch_assoc()) {
                if (!empty($rowNavUser['vFoto'])) {
                    $foto_navbar = $base_path . $rowNavUser['vFoto'];
                }
            }
        }
    }

    // ── Contador de Notificaciones No Leídas ──
    $unread_count = 0;
    if (isset($_SESSION['idUsuario'])) {
        $stmtCount = $conexion->prepare("SELECT COUNT(*) as total FROM tbl_notificaciones WHERE idUsuario = ? AND leida = 0");
        if ($stmtCount) {
            $stmtCount->bind_param("i", $_SESSION['idUsuario']);
            $stmtCount->execute();
            $resCount = $stmtCount->get_result();
            if ($rowCount = $resCount->fetch_assoc()) {
                $unread_count = $rowCount['total'];
            }
        }
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
            <div class="logo-branded" onclick="window.location.href='<?php echo $base_path; ?>/../presentacion/huesped/home.php'" style="display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-house-laptop text-2xl text-primary"></i>
                <span class="text-xl font-black text-primary uppercase tracking-widest">Estancias Digitales</span>
            </div>
        <?php endif; ?>
        <?php if (($is_host || $is_admin) && !$hide_search): ?>
            <!-- Search bar for Host/Admin removed per request -->
        <?php elseif (!$is_host && !$is_admin): ?>
            <?php if ($is_reservas): ?>
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
        
        
        <div class="nav-icons-box" onclick="toggleNotificationsModal()" style="cursor: pointer; position: relative;">
            <i class="fa-regular fa-bell"></i>
            <?php if ($unread_count > 0): ?>
                <span class="nav-notif-badge"><?php echo $unread_count > 9 ? '9+' : $unread_count; ?></span>
            <?php endif; ?>
        </div>
        <?php if (isset($_SESSION['idUsuario'])): ?>
            <div class="nav-profile-avatar" onclick="openProfileModal()" style="cursor:pointer;">
                <img src="<?php echo $foto_navbar; ?>" alt="Perfil">
            </div>
        <?php else: ?>
            <div class="nav-profile-avatar" onclick="window.location.href='<?php echo rtrim($base_path ?? '../../', '/'); ?>/negocio/auth/login.php'" style="cursor:pointer; display: flex; align-items: center; justify-content: center; background: var(--primary); color: white;">
                <i class="fa-solid fa-user"></i>
            </div>
        <?php endif; ?>
    </div>
</nav>

<?php if (!$is_host && !$is_admin): ?>
<!-- Bottom Navigation Móvil para Huésped -->
<div class="mobile-bottom-nav">
    <a href="<?php echo ($is_root ? "presentacion/huesped/" : ""); ?>home.php" class="bottom-nav-item <?php echo $current_page == 'home.php' ? 'active' : ''; ?>">
        <i class="fa-solid fa-house"></i>
        <span>Inicio</span>
    </a>
    <a href="<?php echo ($is_root ? "presentacion/huesped/" : ""); ?>reservas.php" class="bottom-nav-item <?php echo $current_page == 'reservas.php' ? 'active' : ''; ?>">
        <i class="fa-solid fa-calendar-check"></i>
        <span>Reservas</span>
    </a>
    <div class="bottom-nav-item" onclick="openProfileModal()">
        <i class="fa-solid fa-user-circle"></i>
        <span>Perfil</span>
    </div>
</div>
<?php endif; ?>

<!-- Overlay para cerrar sidebar en móvil -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeHostSidebar()"></div>

<script>
function toggleHostSidebar() {
    const sidebar = document.querySelector('.sidebar-host');
    const overlay = document.getElementById('sidebarOverlay');
    if (!sidebar) return;

    const isOpen = sidebar.classList.contains('mobile-active');
    if (isOpen) {
        sidebar.classList.remove('mobile-active');
        overlay.classList.remove('active');
    } else {
        sidebar.classList.add('mobile-active');
        overlay.classList.add('active');
    }
}

function closeHostSidebar() {
    const sidebar = document.querySelector('.sidebar-host');
    const overlay = document.getElementById('sidebarOverlay');
    if (!sidebar) return;
    sidebar.classList.remove('mobile-active');
    if (overlay) overlay.classList.remove('active');
}

// Cerrar con clic fuera del sidebar (sin necesidad de overlay)
document.addEventListener('click', function (e) {
    const sidebar = document.querySelector('.sidebar-host');
    const toggleBtn = document.querySelector('.host-mobile-toggle');
    const overlay = document.getElementById('sidebarOverlay');
    if (!sidebar || !sidebar.classList.contains('mobile-active')) return;

    const isClickInsideSidebar = sidebar.contains(e.target);
    const isClickToggle = toggleBtn && toggleBtn.contains(e.target);

    if (!isClickInsideSidebar && !isClickToggle) {
        sidebar.classList.remove('mobile-active');
        if (overlay) overlay.classList.remove('active');
    }
});
</script>

<?php include __DIR__ . '/user_profile_modal.php'; ?>
<?php include __DIR__ . '/notificaciones_modal.php'; ?>
