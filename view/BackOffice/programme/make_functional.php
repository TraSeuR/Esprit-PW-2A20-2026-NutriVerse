<?php
$file = 'c:/xampp/htdocs/integ/view/BackOffice/programme/admin_dashboard.php';
$content = file_get_contents($file);

// 1. Add CSS
$css = '<style>
    .user-menu-container { position: relative; }
    .user-dropdown {
      position: absolute; top: 110%; right: 0; width: 220px;
      background: #fff; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.15);
      z-index: 10001; display: none; border: 1px solid #eee; overflow: hidden;
    }
    .user-dropdown.show { display: block; animation: slideDownUser 0.2s ease; }
    .user-dropdown a {
      display: flex; align-items: center; gap: 10px; padding: 12px 20px;
      color: #333; text-decoration: none; font-size: 14px; transition: 0.2s;
      text-align: left;
    }
    .user-dropdown a:hover { background: #f9f9f9; color: #27ae60; }
    .user-dropdown a.logout { color: #e74c3c; border-top: 1px solid #eee; }
    .user-dropdown a.logout:hover { background: #fff5f5; }
    .admin-box { cursor: pointer; transition: 0.2s; }
    .admin-box:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    @keyframes slideDownUser { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
</style>';

if (strpos($content, '</head>') !== false) {
    $content = str_replace('</head>', $css . "\n</head>", $content);
}

// 2. Update HTML
$oldBoxPart = 'strtoupper(substr($_SESSION[\'prenom\'] ?? \'A\', 0, 1))'; // unique enough to find the box
if (strpos($content, $oldBoxPart) !== false) {
    // Find the whole admin-box container
    $newBox = '<div class="user-menu-container">
                <div class="admin-box" id="adminBoxBtn" style="display: flex; align-items: center; gap: 12px; background: white; padding: 6px 16px 6px 6px; border-radius: 20px; border: 1px solid var(--border);">
                    <div class="admin-avatar" style="width: 40px; height: 40px; border-radius: 50%; background: #27ae60; color: white; display: grid; place-items: center; font-weight: 700;"><?= strtoupper(substr($_SESSION[\'prenom\'] ?? \'A\', 0, 1)) ?></div>
                    <div>
                        <h4 style="margin:0; font-size: 0.9rem;"><?= htmlspecialchars(($_SESSION[\'prenom\'] ?? \'\') . \' \' . ($_SESSION[\'nom\'] ?? \'\')) ?></h4>
                        <p style="margin:0; font-size: 0.75rem; color: var(--muted);"><?= htmlspecialchars($_SESSION[\'role\'] ?? \'Staff\') ?></p>
                    </div>
                    <i data-feather="chevron-down" style="width: 16px; color: #888;"></i>
                </div>

                <div class="user-dropdown" id="adminDropdown">
                    <a href="../utilisateur/admin_utilisateurs.php"><i data-feather="user"></i> Mon Profil</a>
                    <a href="../nutri_back.php"><i data-feather="settings"></i> Paramètres</a>
                    <a href="../../FrontOffice/utilisateur/logout.php" class="logout"><i data-feather="log-out"></i> Déconnexion</a>
                </div>
            </div>';
            
    // Replace the existing admin-box structure
    $pattern = '/<div class="admin-box"[^>]*>.*?<\/div>\s*<\/div>/is';
    $content = preg_replace($pattern, $newBox, $content, 1);
}

// 3. Add JS
$js = '<script>
document.addEventListener("DOMContentLoaded", function() {
    const btn = document.getElementById("adminBoxBtn");
    const dropdown = document.getElementById("adminDropdown");
    if(btn && dropdown) {
        btn.addEventListener("click", function(e) {
            e.stopPropagation();
            dropdown.classList.toggle("show");
        });
        document.addEventListener("click", function(e) {
            if (!e.target.closest(".user-menu-container")) {
                dropdown.classList.remove("show");
            }
        });
    }
});
</script>';

if (strpos($content, '</body>') !== false) {
    $content = str_replace('</body>', $js . "\n</body>", $content);
}

file_put_contents($file, $content);
echo "Admin box made functional in Programmes dashboard.";
?>
