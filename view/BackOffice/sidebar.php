<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$base_back = "/integ/view/BackOffice";
$base_front = "/integ/view/FrontOffice";
$base_ctrl = "/integ/Controller";
$current_url = $_SERVER['REQUEST_URI'];

// Role Based Access Control
$user_role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? 'guest';

function has_access($module) {
    $user_role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? 'guest';
    if ($user_role === 'admin' || $user_role === 'Administrateur') return true;
    
    $permissions = [
        'dashboard' => ['admin', 'Administrateur', 'Responsable recette', 'Responsable Produit', 'Responsable commande', 'Livreur', 'Responsable Programmes'],
        'recette'   => ['Responsable recette'],
        'utilisateur' => [], // Only admin
        'produit'   => ['Responsable Produit'],
        'movement'  => ['Responsable Produit'],
        'notifications' => ['Responsable Produit', 'admin', 'Administrateur'],

        'programme' => ['Responsable Programmes'],

    ];

    if (!isset($permissions[$module])) return false;
    return in_array($user_role, $permissions[$module]);
}

function sb_link_attr($module, $url) {
    if (has_access($module)) {
        return 'href="' . $url . '"';
    } else {
        return 'href="javascript:void(0)" class="menu-item locked"';
    }
}

// Load unread notifications count if not already loaded
if (!isset($unreadCount)) {
    $sidebar_notif_path = __DIR__ . '/../../Controller/NotificationController.php';
    if (file_exists($sidebar_notif_path)) {
        require_once $sidebar_notif_path;
        $sidebarNotifCtrl = new NotificationController();
        $unreadCount = $sidebarNotifCtrl->getUnreadCount();
    } else {
        $unreadCount = 0;
    }
}

function sb_active($pattern) {
    $current_url = $_SERVER['REQUEST_URI'];
    return strpos($current_url, $pattern) !== false ? 'active' : '';
}
?>
<style>
/* Balanced compact sidebar — overrides all page-specific CSS */
aside.sidebar#sidebar {
    width: 255px !important;
    padding: 18px 14px !important;
    overflow-y: auto !important;
}
aside.sidebar#sidebar .sidebar-top {
    margin-bottom: 22px !important;
}
aside.sidebar#sidebar .brand {
    gap: 10px !important;
}
aside.sidebar#sidebar .brand-logo {
    width: 46px !important;
    height: 46px !important;
    padding: 6px !important;
    border-radius: 12px !important;
    flex-shrink: 0 !important;
}
aside.sidebar#sidebar .brand h2 {
    font-size: 1.35rem !important;
    line-height: 1.2 !important;
}
aside.sidebar#sidebar .brand p {
    font-size: 0.78rem !important;
}
aside.sidebar#sidebar .sidebar-menu {
    gap: 3px !important;
}
aside.sidebar#sidebar .menu-item {
    padding: 9px 12px !important;
    gap: 10px !important;
    font-size: 0.85rem !important;
    border-radius: 14px !important;
    display: flex !important;
    align-items: center !important;
    white-space: nowrap !important;
    text-decoration: none !important;
    color: var(--text) !important;
}

aside.sidebar#sidebar .menu-item.active {
    background: var(--green-soft) !important;
    color: var(--green-dark) !important;
    font-weight: 600 !important;
}

/* RBAC Locked State */
aside.sidebar#sidebar .menu-item.locked {
    filter: blur(1.8px);
    pointer-events: none;
    opacity: 0.45;
    cursor: not-allowed !important;
    user-select: none;
}

/* Make feather icons visible at correct size */
aside.sidebar#sidebar .menu-item svg {
    width: 18px !important;
    height: 18px !important;
    min-width: 18px !important;
    flex-shrink: 0 !important;
    display: block !important;
    stroke: currentColor !important;
}
aside.sidebar#sidebar .sidebar-footer {
    padding-top: 12px !important;
    font-size: 0.78rem !important;
}
/* Adjust main content margin */
.main-content {
    margin-left: 255px !important;
    width: calc(100% - 255px) !important;
}
</style>


<aside class="sidebar" id="sidebar">
    <div class="sidebar-top">
        <div class="brand">
            <img src="<?= $base_back ?>/images/logo.png" class="brand-logo" onerror="this.style.display='none'" alt="Logo">
            <div>
                <h2>NutriVerse</h2>
                <p>Back Office</p>
            </div>
        </div>
        <button class="close-sidebar" id="closeSidebar">✕</button>
    </div>

    <nav class="sidebar-menu">
        <a <?= sb_link_attr('dashboard', $base_back . '/nutri_back.php') ?> class="menu-item <?= sb_active('nutri_back.php') ?>">
            <i data-feather="grid"></i>
            <span>Dashboard</span>
        </a>

        <a <?= sb_link_attr('recette', $base_back . '/RECETTE/admin.php') ?> class="menu-item <?= sb_active('RECETTE/admin.php') ?>">
            <i data-feather="book-open"></i>
            <span>Recettes</span>
        </a>

        <a <?= sb_link_attr('utilisateur', $base_back . '/utilisateur/admin_utilisateurs.php') ?> class="menu-item <?= sb_active('utilisateur/admin_utilisateurs') ?>">
            <i data-feather="users"></i>
            <span>Utilisateurs</span>
        </a>

        <a <?= sb_link_attr('produit', $base_back . '/produit/listProduit.php') ?> class="menu-item <?= sb_active('produit/listProduit') ?>">
            <i data-feather="package"></i>
            <span>Produits</span>
        </a>

        <a <?= sb_link_attr('movement', $base_back . '/movement/listMovement.php') ?> class="menu-item <?= sb_active('movement/listMovement') ?>">
            <i data-feather="activity"></i>
            <span>Mouvements Stock</span>
        </a>

        <a <?= sb_link_attr('notifications', $base_back . '/notifications/listNotifications.php') ?> class="menu-item <?= sb_active('listNotifications') ?>" style="position:relative;">
            <i data-feather="bell"></i>
            <span>Notifications</span>
            <?php if(isset($unreadCount) && $unreadCount > 0): ?>
                <span style="background:#e74c3c;color:white;border-radius:10px;padding:2px 7px;font-size:10px;margin-left:auto;"><?= $unreadCount ?></span>
            <?php endif; ?>
        </a>



        <a <?= sb_link_attr('programme', $base_back . '/programme/admin_dashboard.php') ?> class="menu-item <?= sb_active('programme/admin_dashboard') ?>">
            <i data-feather="heart"></i>
            <span>Programmes</span>
        </a>


    </nav>

    <div class="sidebar-footer">
        <a href="<?= $base_front ?>/index.php" class="menu-item" style="padding: 10px 0; font-size: 0.85rem; opacity: 0.7;">
            <i data-feather="log-out" style="width: 16px;"></i>
            <span>Quitter l'admin</span>
        </a>
        <p style="margin-top: 10px;">© 2026 NutriVerse</p>
    </div>
</aside>