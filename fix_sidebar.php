<?php
$baseDir = __DIR__ . '/view/BackOffice';

$sidebarHtml = <<<'HTML'
<?php
$base_back = "/integ/view/BackOffice";
$base_front = "/integ/view/FrontOffice";
$base_root = "/integ";
$current_url = $_SERVER['REQUEST_URI'];
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-top">
        <div class="brand">
            <img src="<?= $base_back ?>/images/logo.png" class="brand-logo" onerror="this.style.display='none'">
            <div>
                <h2>NutriVerse</h2>
                <p>Back Office</p>
            </div>
        </div>
        <button class="close-sidebar" id="closeSidebar">✕</button>
    </div>

    <nav class="sidebar-menu">
        <a href="<?= $base_back ?>/nutri_back.php" class="menu-item <?= strpos($current_url, 'nutri_back.php') !== false ? 'active' : '' ?>">
            <i data-feather="grid"></i>
            <span>Dashboard</span>
        </a>

        <a href="<?= $base_back ?>/RECETTE/admin.php" class="menu-item <?= strpos($current_url, 'RECETTE/admin.php') !== false ? 'active' : '' ?>">
            <i data-feather="book-open"></i>
            <span>Recettes</span>
        </a>

        <a href="<?= $base_root ?>/shop.php?action=admin_users" class="menu-item <?= strpos($current_url, 'admin_users') !== false ? 'active' : '' ?>">
            <i data-feather="users"></i>
            <span>Utilisateurs</span>
        </a>

        <a href="<?= $base_back ?>/produit/listProduit.php" class="menu-item <?= strpos($current_url, 'produit/listProduit.php') !== false ? 'active' : '' ?>">
            <i data-feather="package"></i>
            <span>Produits</span>
        </a>

        <a href="<?= $base_back ?>/movement/listMovement.php" class="menu-item <?= strpos($current_url, 'movement/listMovement.php') !== false ? 'active' : '' ?>">
            <i data-feather="activity"></i>
            <span>Mouvements Stock</span>
        </a>

        <a href="<?= $base_back ?>/notifications/listNotifications.php" class="menu-item <?= strpos($current_url, 'listNotifications.php') !== false ? 'active' : '' ?>">
            <i data-feather="bell"></i>
            <span>Notifications</span>
        </a>

        <a href="<?= $base_root ?>/shop.php?action=admin_orders" class="menu-item <?= strpos($current_url, 'admin_orders') !== false ? 'active' : '' ?>">
            <i data-feather="shopping-cart"></i>
            <span>Commandes</span>
        </a>

        <a href="<?= $base_root ?>/shop.php?action=admin_livraisons" class="menu-item <?= strpos($current_url, 'admin_livraisons') !== false ? 'active' : '' ?>">
            <i data-feather="truck"></i>
            <span>Livraisons</span>
        </a>

        <a href="<?= $base_back ?>/programme/admin_dashboard.php" class="menu-item <?= strpos($current_url, 'programme/admin_dashboard.php') !== false ? 'active' : '' ?>">
            <i data-feather="heart"></i>
            <span>Programmes</span>
        </a>

        <a href="<?= $base_root ?>/Controller/OffreC.php?action=admin_list" class="menu-item <?= (strpos($current_url, 'OffreC.php') !== false || strpos($current_url, 'offre/') !== false) ? 'active' : '' ?>">
            <i data-feather="refresh-cw"></i>
            <span>Offre & ingrediant</span>
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
HTML;

file_put_contents($baseDir . '/sidebar.php', $sidebarHtml);

function replaceSidebarInFile($filePath) {
    $content = file_get_contents($filePath);
    
    // Pattern to match <aside class="sidebar"> ... </aside>
    $pattern = '/<aside class="sidebar"[^>]*>.*?<\/aside>/is';
    
    // Check if the file contains the sidebar
    if (preg_match($pattern, $content)) {
        // Find how deep we are relative to BackOffice to require the sidebar.php
        // Or just use document root
        $requireSt = '<?php include $_SERVER[\'DOCUMENT_ROOT\'] . \'/integ/view/BackOffice/sidebar.php\'; ?>';
        
        $newContent = preg_replace($pattern, $requireSt, $content);
        
        if ($newContent !== null && $newContent !== $content) {
            file_put_contents($filePath, $newContent);
            echo "Replaced in: $filePath\n";
        }
    }
}

// Function to recursively find PHP files
function scanAndReplace($dir) {
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            scanAndReplace($path);
        } else if (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            if (basename($path) !== 'sidebar.php') {
                replaceSidebarInFile($path);
            }
        }
    }
}

scanAndReplace($baseDir);
echo "Done.\n";
