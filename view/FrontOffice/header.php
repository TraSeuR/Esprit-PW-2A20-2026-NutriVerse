<?php
require_once __DIR__ . "/../../Controller/no_cache.php";

// Auto-login from "remember me" cookie
if (!isset($_SESSION['id_user']) && isset($_COOKIE['remember_token'])) {
  require_once __DIR__ . "/../../Controller/userC.php";
  $userC = new userC();
  $user = $userC->getUserByRememberToken($_COOKIE['remember_token']);
  if ($user) {
    $_SESSION['id_user'] = $user['id_user'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['nom'] = $user['nom'];
    $_SESSION['prenom'] = $user['prenom'];
    $_SESSION['avatar'] = $user['avatar'] ?? 'avatar1.png';
  }
}
if (!isset($rel)) {
    $rel = "";
}

// Logic to determine active link
$current_page = basename($_SERVER['PHP_SELF']);
function is_active($page, $current_page) {
    return ($page == $current_page) ? 'active-link' : '';
}

// Ensure the controller paths are correct
require_once __DIR__ . '/../../controller/recetteC.php';
require_once __DIR__ . '/../../Controller/ProduitController.php';
require_once __DIR__ . '/../../Controller/NotificationController.php';
require_once __DIR__ . '/../../service/MonitoringService.php';

$notifController = new NotificationController();
$unreadCount = $notifController->getUnreadCount();
?>

<!-- ============================================= -->
<!-- NUTRIVERSE STANDARDIZED HEADER - SELF-CONTAINED -->
<!-- All styles use #nv-header ID for max specificity -->
<!-- ============================================= -->
<style>
    /* HEADER - fully self-contained, immune to external CSS */
    #nv-header {
        position: sticky !important;
        top: 0 !important;
        z-index: 10000 !important; /* Extremely high z-index */
        background: rgba(248, 248, 246, 0.92) !important;
        backdrop-filter: blur(10px) !important;
        -webkit-backdrop-filter: blur(10px) !important;
        border-bottom: 1px solid #e5ebe4 !important;
        width: 100% !important;
        font-family: 'Poppins', sans-serif !important;
        height: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        display: block !important;
        transition: all 0.3s ease !important;
    }

    #nv-header * {
        box-sizing: border-box !important;
    }

    #nv-header .nv-container {
        width: min(1200px, 92%) !important;
        max-width: 1200px !important;
        margin: auto !important;
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        padding: 18px 0 !important;
        height: auto !important;
        border: none !important;
        background: transparent !important;
    }

    #nv-header .nv-logo {
        display: flex !important;
        align-items: center !important;
        flex-shrink: 0 !important;
        height: 60px !important;
    }

    #nv-header .nv-logo img {
        height: 60px !important;
        width: auto !important;
        object-fit: contain !important;
        display: block !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        box-shadow: none !important;
    }

    #nv-header .nv-menu-toggle {
        display: none !important;
        font-size: 1.8rem !important;
        cursor: pointer !important;
        color: #1c2733 !important;
    }

    #nv-header .nv-nav {
        display: flex !important;
        align-items: center !important;
        gap: 24px !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    #nv-header .nv-nav a {
        font-weight: 500 !important;
        font-size: 16px !important;
        color: #1c2733 !important;
        text-decoration: none !important;
        transition: all 0.3s ease !important;
        position: relative !important;
        white-space: nowrap !important;
        background: transparent !important;
        border: none !important;
        display: inline-block !important;
        padding: 0 !important;
    }

    #nv-header .nv-nav a:hover {
        color: #3f9636 !important;
    }

    #nv-header .nv-nav a.active-link {
        color: #59b84d !important;
        font-weight: 700 !important;
        font-size: 16px !important;
    }

    #nv-header .nv-icons {
        position: relative !important;
        margin-left: 10px !important;
        display: flex !important;
        align-items: center !important;
        gap: 15px !important;
    }

    #nv-header .nv-icon-link {
        font-size: 1.3rem !important;
        cursor: pointer !important;
        text-decoration: none !important;
        padding: 8px 12px !important;
        border-radius: 50% !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: transparent !important;
        border: none !important;
    }

    #nv-header .nv-icon-link:hover {
        background: rgba(89, 184, 77, 0.08) !important;
        transform: scale(1.1) !important;
    }

    #nv-header .nv-auth {
        display: flex !important;
        gap: 10px !important;
        margin-left: 10px !important;
    }

    #nv-header .nv-btn-outline, 
    #nv-header .nv-nav a.nv-btn-outline {
        padding: 13px 22px !important;
        border: 1.5px solid #59b84d !important;
        border-radius: 999px !important;
        color: #3f9636 !important;
        font-weight: 600 !important;
        font-size: 16px !important;
        background: transparent !important;
        text-decoration: none !important;
        transition: all 0.3s ease !important;
        white-space: nowrap !important;
        display: flex !important;
        align-items: center !important;
    }

    #nv-header .nv-btn-outline:hover,
    #nv-header .nv-nav a.nv-btn-outline:hover {
        background: rgba(89, 184, 77, 0.08) !important;
    }

    #nv-header .nv-btn-primary,
    #nv-header .nv-nav a.nv-btn-primary {
        padding: 13px 22px !important;
        background: #59b84d !important;
        border: 1.5px solid #59b84d !important;
        border-radius: 999px !important;
        color: white !important;
        font-weight: 600 !important;
        font-size: 16px !important;
        text-decoration: none !important;
        box-shadow: 0 4px 12px rgba(89, 184, 77, 0.3) !important;
        transition: all 0.3s ease !important;
        white-space: nowrap !important;
        display: flex !important;
        align-items: center !important;
    }

    #nv-header .nv-btn-primary:hover,
    #nv-header .nv-nav a.nv-btn-primary:hover {
        background: #3f9636 !important;
        border-color: #3f9636 !important;
        transform: translateY(-2px) !important;
        color: white !important;
    }

    /* Notification styles */
    #nv-header .nv-notif-badge {
        position: absolute !important; top: -5px !important; right: -8px !important; background: #e74c3c !important; color: white !important;
        border-radius: 50% !important; padding: 2px 5px !important; font-size: 10px !important; font-weight: 700 !important;
        min-width: 16px !important; text-align: center !important; border: 2px solid white !important;
    }
    #nv-header .nv-notif-dropdown {
        position: absolute !important; top: 100% !important; right: 0 !important; width: 320px !important; background: white !important;
        border-radius: 12px !important; box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important; display: none !important;
        z-index: 1000 !important; margin-top: 15px !important; overflow: hidden !important; border: 1px solid #eee !important;
        text-align: left !important;
    }
    #nv-header .nv-notif-dropdown.show { display: block !important; animation: nvSlideDown 0.3s ease !important; }
    @keyframes nvSlideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    #nv-header .nv-notif-item { display: none !important; }
    #nv-header .nv-notif-footer { padding: 12px !important; text-align: center; background: #fff; font-size: 13px; border-top: 1px solid #f0f0f0; }
    #nv-header .nv-notif-footer a { color: #59b84d !important; font-weight: 600; text-decoration: none; }
    #nv-header .nv-notif-footer a:hover { text-decoration: underline; }

    /* User Dropdown */
    #nv-header .user-dropdown a:hover { background: #f8f9fa !important; color: #59b84d !important; }
    #nv-header .user-dropdown a.logout:hover { background: #fff5f5 !important; color: #c82333 !important; }

    /* Mobile responsive */
    @media (max-width: 1050px) {
        #nv-header .nv-menu-toggle { display: block !important; }
        #nv-header .nv-nav {
            display: none !important;
            position: absolute !important;
            top: 100% !important;
            right: 4% !important;
            background: white !important;
            border-radius: 20px !important;
            box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
            padding: 20px !important;
            flex-direction: column !important;
            align-items: flex-start !important;
            width: 260px !important;
            gap: 15px !important;
            z-index: 10001 !important;
        }
        #nv-header .nv-nav.active {
            display: flex !important;
        }
        #nv-header .nv-auth {
            margin-left: 0 !important;
            width: 100% !important;
            flex-direction: column !important;
        }
        #nv-header .nv-btn-outline, #nv-header .nv-btn-primary {
            width: 100% !important;
        }
    }
</style>

<header id="nv-header">
    <div class="nv-container">
        <div class="nv-logo">
            <a href="<?= $rel ?>index.php">
                <img src="<?= $rel ?>images/logo.png" alt="Logo NutriVerse">
            </a>
        </div>

        <div class="nv-menu-toggle" id="nv-toggle-btn">☰</div>
        
        <nav class="nv-nav" id="nv-nav-menu">
            <a href="<?= $rel ?>index.php" class="<?= is_active('index.php', $current_page) ?>">Accueil</a>
            <a href="<?= $rel ?>RECETTE/recettes.php" class="<?= is_active('recettes.php', $current_page) ?>">Recettes</a>
            <a href="<?= $rel ?>programme/mode_selection.php" class="<?= is_active('mode_selection.php', $current_page) ?>">Programmes</a>
            <a href="<?= $rel ?>produit/listProduit.php" class="<?= is_active('listProduit.php', $current_page) ?>">Produits Locaux</a>
            
            <div class="nv-icons">
                <!-- Notification Bell -->
                <div style="position: relative;">
                    <a href="javascript:void(0)" onclick="toggleNvNotifs()" class="nv-icon-link nv-notif-trigger">
                        🔔
                        <?php if($unreadCount > 0): ?>
                            <span class="nv-notif-badge"><?= $unreadCount ?></span>
                        <?php endif; ?>
                    </a>
                    <div id="nv-notif-dropdown" class="nv-notif-dropdown">
                        <div style="padding: 15px; border-bottom: 1px solid #eee; font-weight: 600; display:flex; justify-content: space-between; color: #333; align-items: center;">
                            <span style="font-size: 14px;">Notifications</span>
                            <div style="display: flex; gap: 10px;">
                                <span style="font-size: 11px; color: #3498db; cursor: pointer; font-weight: 500;" onclick="markNvAllRead()">Tout lu</span>
                                <span style="font-size: 11px; color: #e74c3c; cursor: pointer; font-weight: 500;" onclick="deleteNvAllNotifs()">Vider</span>
                            </div>
                        </div>
                        <div id="nv-notif-content">
                            <p style="padding: 20px; text-align: center; color: #888;">Chargement...</p>
                        </div>
                        <div class="nv-notif-footer" id="nv-notif-footer">
                            <a href="javascript:void(0)" onclick="loadNvNotifications(true)">Voir tout l'historique</a>
                        </div>
                    </div>
                </div>


            </div>
            <div class="nv-auth">
                <?php if (isset($_SESSION['id_user'])): ?>
                    <div class="user-menu" style="position: relative;">
                        <button class="user-btn transparent-btn" id="userMenuBtn" style="border: none; background: white; cursor: pointer; display: flex; align-items: center; gap: 10px; padding: 8px 14px; border-radius: 999px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); border: 1px solid #eee; transition: all 0.2s ease;">
                            <img src="<?= $rel ?>images/<?= htmlspecialchars($_SESSION['avatar'] ?? 'avatar1.png') ?>" alt="Avatar"
                                style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 2px solid #59b84d; flex-shrink: 0;">
                            <div class="user-info-text" style="text-align: left;">
                                <h4 style="margin: 0; font-size: 0.88rem; color: #1c2733; font-weight: 700; line-height: 1.2;"><?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?></h4>
                                <p style="margin: 0; font-size: 0.72rem; color: #64748b; line-height: 1.2;">Utilisateur</p>
                            </div>
                            <span style="font-size: 0.7rem; color: #999; margin-left: 2px;">▼</span>
                        </button>

                        <div class="user-dropdown" id="userDropdown" style="
                            position: absolute; top: calc(100% + 10px); right: 0;
                            background: white; border-radius: 16px;
                            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
                            display: none; z-index: 9999; min-width: 200px;
                            overflow: hidden; border: 1px solid #eef0ee;
                            padding: 8px;
                        ">
                            <a href="<?= $rel ?>utilisateur/edit_profile.php" style="
                                display: flex; align-items: center; gap: 10px;
                                padding: 11px 14px; color: #1c2733; text-decoration: none;
                                border-radius: 10px; font-size: 0.9rem; font-weight: 500;
                                transition: background 0.2s;
                            " onmouseover="this.style.background='#f4faf3'" onmouseout="this.style.background='transparent'">
                                <span style="font-size: 1rem;">👤</span> Éditer Profil
                            </a>
                            <div style="height: 1px; background: #f0f0f0; margin: 4px 0;"></div>
                            <a href="<?= $rel ?>utilisateur/logout.php" class="logout" style="
                                display: flex; align-items: center; gap: 10px;
                                padding: 11px 14px; color: #e74c3c; text-decoration: none;
                                border-radius: 10px; font-size: 0.9rem; font-weight: 600;
                                transition: background 0.2s;
                            " onmouseover="this.style.background='#fff5f5'" onmouseout="this.style.background='transparent'">
                                <span style="font-size: 1rem;">🚪</span> Déconnexion
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= $rel ?>utilisateur/login.php" class="nv-btn-outline">Se connecter</a>
                    <a href="<?= $rel ?>utilisateur/register.php" class="nv-btn-primary">S'inscrire</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>

<script>
    document.getElementById('nv-toggle-btn').addEventListener('click', function() {
        document.getElementById('nv-nav-menu').classList.toggle('active');
    });

    function toggleNvNotifs() {
        const dropdown = document.getElementById('nv-notif-dropdown');
        const isOpen = dropdown.classList.contains('show');
        dropdown.classList.toggle('show');
        if (!isOpen) { loadNvNotifications(); }
    }

    function loadNvNotifications(showAll = false) {
        let url = '<?= $rel ?>produit/ajax_get_notifications.php';
        if (showAll) url += '?show=all';
        fetch(url)
        .then(r => r.text())
        .then(html => {
            document.getElementById('nv-notif-content').innerHTML = html;
        });
    }

    function markNvAllRead() {
        fetch('<?= $rel ?>produit/ajax_get_notifications.php?action=read_all')
        .then(() => {
            loadNvNotifications();
            const badge = document.querySelector('.nv-notif-badge');
            if (badge) badge.remove();
        });
    }

    function deleteNvNotif(id) {
        fetch('<?= $rel ?>produit/ajax_get_notifications.php?action=delete&id=' + id)
        .then(() => {
            loadNvNotifications();
        });
    }

    function deleteNvAllNotifs() {
        fetch('<?= $rel ?>produit/ajax_get_notifications.php?action=delete_all')
        .then(() => {
            loadNvNotifications();
            const badge = document.querySelector('.nv-notif-badge');
            if (badge) badge.remove();
        });
    }

    const userBtn = document.getElementById("userMenuBtn");
    const userDropdown = document.getElementById("userDropdown");
    if (userBtn && userDropdown) {
        userBtn.addEventListener("click", function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (userDropdown.style.display === "block") {
                userDropdown.style.display = "none";
            } else {
                userDropdown.style.display = "block";
            }
        });
    }

    window.addEventListener('click', function(e) {
        if (!e.target.closest('.nv-notif-trigger') && !e.target.closest('.nv-notif-dropdown')) {
            const dropdown = document.getElementById('nv-notif-dropdown');
            if(dropdown) dropdown.classList.remove('show');
        }
        if (!e.target.closest('#nv-nav-menu') && !e.target.closest('#nv-toggle-btn')) {
            document.getElementById('nv-nav-menu').classList.remove('active');
        }
        if (userBtn && userDropdown && !userBtn.contains(e.target) && !userDropdown.contains(e.target)) {
            userDropdown.style.display = "none";
        }
    });
</script>
