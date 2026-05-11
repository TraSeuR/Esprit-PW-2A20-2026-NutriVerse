<?php
/**
 * topbar.php - Unified Back Office Top Navigation Bar
 */
if (session_status() === PHP_SESSION_NONE) session_start();

// Fetch unread count for notifications
if (!isset($unreadCount)) {
    $topbar_notif_path = __DIR__ . '/../../Controller/NotificationController.php';
    if (file_exists($topbar_notif_path)) {
        require_once $topbar_notif_path;
        $topbarNotifCtrl = new NotificationController();
        $unreadCount = $topbarNotifCtrl->getUnreadCount();
    } else {
        $unreadCount = 0;
    }
}
?>

<header class="topbar" style="height: 86px; background: rgba(255, 255, 255, 0.88); backdrop-filter: blur(12px); border-bottom: 1px solid #e5ebea; display: flex; justify-content: space-between; align-items: center; padding: 0 34px; position: sticky; top: 0; z-index: 500;">
    <div class="topbar-left" style="display: flex; align-items: center; gap: 18px;">
        <label for="ssa-sidebar-toggle" class="menu-btn" style="cursor:pointer; display:none; background: white; border: 1px solid #e5ebea; width: 46px; height: 46px; border-radius: 14px; align-items: center; justify-content: center;">
            <i data-feather="menu"></i>
        </label>
        <div class="search-box" style="width: 520px; max-width: 100%; display: flex; align-items: center; gap: 12px; background: white; border: 1px solid #e5ebea; padding: 14px 18px; border-radius: 18px; box-shadow: 0 12px 30px rgba(0, 0, 0, 0.06);">
            <i data-feather="search" style="width: 18px; color: #6e7782;"></i>
            <input type="search" placeholder="Rechercher..." autocomplete="off" style="width: 100%; border: none; outline: none; background: transparent; font-family: inherit; font-size: 0.98rem;" />
        </div>
    </div>

    <div class="topbar-right" style="display: flex; align-items: center; gap: 24px;">
        <!-- Retour au site -->
        <a href="/integ/view/FrontOffice/index.php" style="color: #0b8f3c; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px; transition: 0.2s;" class="back-to-site-link">
            <i data-feather="arrow-left" style="width: 18px;"></i>
            <span>Retour au site</span>
        </a>

        <!-- Notification Bell -->
        <div style="position: relative;">
            <button class="icon-btn notification-btn" onclick="toggleNotifs()" style="width: 48px; height: 48px; border: none; border-radius: 16px; background: white; cursor: pointer; position: relative; box-shadow: 0 12px 30px rgba(0, 0, 0, 0.06); display: flex; align-items: center; justify-content: center;">
                <i data-feather="bell" style="width: 20px; color: #1a2433;"></i>
                <?php if($unreadCount > 0): ?>
                    <span class="notif-badge-ui" style="position: absolute; top: 12px; right: 12px; background: #ff9800; border-radius: 50%; width: 10px; height: 10px; border: 2px solid #fff;"></span>
                <?php endif; ?>
            </button>
            <div id="notif-dropdown" class="notif-dropdown" style="position: absolute; top: 110%; right: 0; width: 350px; max-height: 450px; background: #fff; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); z-index: 10000; overflow-y: auto; display: none; border: 1px solid #eee;">
                <div style="padding: 15px; border-bottom: 1px solid #eee; font-weight: 600; display:flex; justify-content: space-between; color: #333;">
                    Notifications
                    <span style="font-size: 11px; color: #3498db; cursor: pointer;" onclick="markAllRead()">Tout marquer lu</span>
                </div>
                <div id="notif-content">
                    <p style="padding: 20px; text-align: center; color: #888;">Chargement...</p>
                </div>
                <div class="notif-footer" style="padding: 10px; text-align: center; background: #fafafa; border-top: 1px solid #eee;">
                    <a href="/integ/view/BackOffice/notifications/listNotifications.php" style="font-size: 12px; color: #3498db; font-weight: 600; text-decoration: none;">Voir tout l'historique</a>
                </div>
            </div>
        </div>

        <!-- User Profile Dropdown -->
        <div class="user-menu-container" style="position: relative;">
            <div class="admin-box" id="adminBoxBtn" style="display: flex; align-items: center; gap: 12px; background: white; padding: 6px 16px 6px 6px; border-radius: 20px; border: 1px solid #e5ebea; cursor: pointer; transition: 0.2s; box-shadow: 0 12px 30px rgba(0, 0, 0, 0.06);">
                <div class="admin-avatar" style="width: 44px; height: 44px; border-radius: 50%; background: #27ae60; color: white; display: grid; place-items: center; font-weight: 700; font-size: 1.1rem;"><?= strtoupper(substr($_SESSION['prenom'] ?? 'A', 0, 1)) ?></div>
                <div style="text-align: left;">
                    <h4 style="margin:0; font-size: 0.95rem; color: #1a2433;"><?= htmlspecialchars(($_SESSION['prenom'] ?? '') . ' ' . ($_SESSION['nom'] ?? '')) ?></h4>
                    <p style="margin:0; font-size: 0.8rem; color: #6e7782;"><?= htmlspecialchars($_SESSION['role'] ?? 'Staff') ?></p>
                </div>
                <i data-feather="chevron-down" style="width: 16px; color: #6e7782;"></i>
            </div>

            <div class="user-dropdown" id="adminDropdown" style="position: absolute; top: 115%; right: 0; width: 220px; background: #fff; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); z-index: 10001; display: none; border: 1px solid #eee; overflow: hidden;">
                <a href="/integ/view/FrontOffice/index.php" style="display: flex; align-items: center; gap: 10px; padding: 12px 20px; color: #333; text-decoration: none; font-size: 14px; transition: 0.2s;">
                    <i data-feather="home" style="width: 18px;"></i>
                    Retour au site
                </a>
                <a href="/integ/view/FrontOffice/utilisateur/logout.php" class="logout" style="display: flex; align-items: center; gap: 10px; padding: 12px 20px; color: #e74c3c; text-decoration: none; font-size: 14px; transition: 0.2s; border-top: 1px solid #eee;">
                    <i data-feather="log-out" style="width: 18px;"></i>
                    Déconnexion
                </a>
            </div>
        </div>
    </div>
</header>

<style>
/* Notification Item Styling */
.notif-item { padding: 12px 15px; border-bottom: 1px solid #f0f0f0; font-size: 13px; transition: background 0.2s; text-align: left; cursor: pointer; }
.notif-item:hover { background: #f9f9f9; }
.notif-item.unread { background: #f0f7ff; border-left: 3px solid #3498db; }
.notif-item-header { display: flex; justify-content: space-between; margin-bottom: 4px; color: #888; font-size: 11px; }
.notif-item-msg { color: #333; line-height: 1.4; font-weight: 500; }

/* Custom Scrollbar for Dropdown */
#notif-dropdown::-webkit-scrollbar { width: 6px; }
#notif-dropdown::-webkit-scrollbar-track { background: #f1f1f1; }
#notif-dropdown::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
#notif-dropdown::-webkit-scrollbar-thumb:hover { background: #999; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof feather !== 'undefined') feather.replace();

    const adminBoxBtn = document.getElementById('adminBoxBtn');
    const adminDropdown = document.getElementById('adminDropdown');
    
    if (adminBoxBtn && adminDropdown) {
        adminBoxBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const currentDisplay = window.getComputedStyle(adminDropdown).display;
            adminDropdown.style.display = (currentDisplay === 'block') ? 'none' : 'block';
        });
        
        document.addEventListener('click', function(e) {
            if (!adminBoxBtn.contains(e.target) && !adminDropdown.contains(e.target)) {
                adminDropdown.style.display = 'none';
            }
        });
    }
});

function toggleNotifs() {
    const dropdown = document.getElementById('notif-dropdown');
    if (dropdown) {
        const isShow = dropdown.style.display === 'block';
        dropdown.style.display = isShow ? 'none' : 'block';
        if (!isShow) {
            loadNotifications();
        }
    }
}

function loadNotifications() {
    const content = document.getElementById('notif-content');
    if (!content) return;
    
    fetch('/integ/view/BackOffice/notifications/get_notifications.php')
        .then(response => response.json())
        .then(data => {
            if (!data || data.length === 0) {
                content.innerHTML = '<p style="padding: 20px; text-align: center; color: #888;">Aucune notification</p>';
            } else {
                let html = '';
                data.forEach(n => {
                    const isUnread = n.is_read == 0 ? 'unread' : '';
                    html += `
                        <div class="notif-item ${isUnread}" onclick="markAsRead(${n.id}, '${n.link}')">
                            <div class="notif-item-header">
                                <span>${n.type}</span>
                                <span>${n.created_at}</span>
                            </div>
                            <div class="notif-item-msg">${n.message}</div>
                        </div>
                    `;
                });
                content.innerHTML = html;
            }
        })
        .catch(err => {
            content.innerHTML = '<p style="padding: 20px; text-align: center; color: #e74c3c;">Erreur de chargement</p>';
        });
}

function markAllRead() {
    fetch('/integ/view/BackOffice/notifications/mark_all_read.php', { method: 'POST' })
        .then(() => location.reload());
}

function markAsRead(id, link) {
    fetch('/integ/view/BackOffice/notifications/mark_read.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id
    }).then(() => {
        if (link && link !== '#') window.location.href = link;
        else location.reload();
    });
}
</script>
