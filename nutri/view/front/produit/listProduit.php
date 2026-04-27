<?php
require_once __DIR__.'/../../../controller/ProduitController.php';
require_once __DIR__.'/../../../controller/NotificationController.php';
require_once __DIR__.'/../../../service/MonitoringService.php';

$produitController = new ProduitController();
$notifController = new NotificationController();

// Trigger automatic monitoring (runs daily)
MonitoringService::checkAll();

$unreadCount = $notifController->getUnreadCount();

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? 'asc';

// Sur le front-office, on n'affiche que les produits actifs avec la recherche et la catégorie
$produits = $produitController->getProduitsActifs($search, $category, $sort);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriVerse - Produits locaux</title>
  <link rel="stylesheet" href="../../front/assets/front.css" />
  <link rel="stylesheet" href="../../../Produit Locaux/utilisateurproduitlocaux.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <style>
    /* Cart Sidebar Styles */
    .cart-sidebar {
      position: fixed; top: 0; right: -400px; width: 350px; height: 100vh;
      background: #fff; box-shadow: -2px 0 10px rgba(0,0,0,0.1);
      z-index: 9999; transition: right 0.3s ease; display: flex; flex-direction: column;
    }
    .cart-sidebar.open { right: 0; }
    .cart-header { padding: 20px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; }
    .cart-header button { border: none; background: none; font-size: 20px; cursor: pointer; }
    .cart-items { flex: 1; overflow-y: auto; padding: 20px; }
    .cart-item { display: flex; justify-content: space-between; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;}
    .cart-footer { padding: 20px; border-top: 1px solid #ddd; text-align: center;}
    .cart-footer button { width: 100%; margin-top: 10px; }
    .plu-product-visual { padding: 0 !important; overflow: hidden; display:flex; align-items:center; justify-content:center; background:#f4f4f4;}
    .plu-product-visual img { width: 100%; height: 150px; object-fit: cover; }

    /* Notification Dropdown Styles */
    .notif-dropdown {
      position: absolute; top: 100%; right: 0; width: 350px; max-height: 450px;
      background: #fff; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.15);
      z-index: 10000; overflow-y: auto; display: none; border: 1px solid #eee;
    }
    .notif-dropdown.show { display: block; animation: slideDown 0.3s ease; }
    @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    .notif-item { padding: 12px 15px; border-bottom: 1px solid #f0f0f0; font-size: 13px; transition: background 0.2s; }
    .notif-item:hover { background: #f9f9f9; }
    .notif-item.unread { background: #f0f7ff; border-left: 3px solid #3498db; }
    .notif-item-header { display: flex; justify-content: space-between; margin-bottom: 4px; color: #888; font-size: 11px; }
    .notif-item-msg { color: #333; line-height: 1.4; font-weight: 500; }
    .notif-footer { padding: 10px; text-align: center; background: #fafafa; }
    .notif-footer a { font-size: 12px; color: #3498db; font-weight: 600; text-decoration: none; }
    .notif-badge-ui { 
        position: absolute; top: -8px; right: -8px; background: #e74c3c; color: white; 
        border-radius: 50%; width: 20px; height: 20px; font-size: 11px; font-weight: bold;
        display: flex; align-items: center; justify-content: center; border: 2px solid #fff;
    }
  </style>
</head>
<body>

  <header class="header">
    <div class="container nav">
      <div class="logo">
        <a href="../../front/front.html">
          <img src="../../front/images/logo.png" alt="Logo NutriVerse" class="logo-img" />
        </a>
      </div>

      <input type="checkbox" id="nav-toggle" hidden aria-hidden="true" />
      <label for="nav-toggle" class="menu-toggle" aria-label="Ouvrir le menu">☰</label>
      <nav class="navbar">
        <a href="../../front/front.html#hero">Accueil</a>
        <a href="../../front/front.html#categories">Marketplace</a>
        <a href="../../front/front.html#recipes">Recettes</a>
        <a href="../../front/front.html#programs">Programmes</a>
        <a href="../../front/front.html#suivi">Suivi</a>
        <a href="listProduit.php" class="active">Produits locaux</a>
        <a href="#" onclick="toggleCart(); return false;" class="cart-icon" title="Commandes" style="position: relative;">
            🛒
        </a>
        <div style="position: relative; margin-left: 15px;">
            <a href="javascript:void(0)" onclick="toggleNotifs()" class="notif-icon-front" style="font-size: 1.2rem; cursor: pointer;">
                🔔
                <?php if($unreadCount > 0): ?>
                    <span class="notif-badge-ui"><?= $unreadCount ?></span>
                <?php endif; ?>
            </a>
            <div id="notif-dropdown" class="notif-dropdown">
                <div style="padding: 15px; border-bottom: 1px solid #eee; font-weight: 600; display:flex; justify-content: space-between;">
                    Notifications
                    <span style="font-size: 11px; color: #3498db; cursor: pointer;" onclick="markAllRead()">Tout marquer lu</span>
                </div>
                <div id="notif-content">
                    <!-- Loaded via AJAX -->
                    <p style="padding: 20px; text-align: center; color: #888;">Chargement...</p>
                </div>
                <div class="notif-footer" id="notif-footer">
                    <a href="javascript:void(0)" onclick="loadNotifications(true)">Voir tout l'historique</a>
                </div>
            </div>
        </div>
      </nav>
    </div>
  </header>

  <section class="plu-intro section">
    <div class="container">
      <div class="plu-intro-inner">
        <div>
          <span class="plu-badge">Disponibilité en temps réel • Anti-gaspillage</span>
          <h1>Produits locaux, frais et responsables</h1>
          <p>
            Consultez les produits proposés par nos partenaires locaux, voyez la disponibilité
            mise à jour avec le stock, et repérez les dates courtes pour privilégier les achats
            utiles à la planète et à votre panier.
          </p>
        </div>
      </div>
    </div>
  </section>

  <section class="container" id="catalogue" aria-labelledby="catalogue-title">
    <div class="plu-banner-waste">
      <span aria-hidden="true">🌱</span>
      <div>
        <strong>Anti-gaspillage</strong>
        <p>
          Découvrez nos produits locaux ! Les stocks sont mis à jour en direct.
        </p>
      </div>
    </div>

    <form id="filterForm" method="GET" action="listProduit.php" class="plu-toolbar" style="display:flex; gap: 10px; align-items:center;">
      <div class="plu-search-wrap" role="search" style="flex: 1;">
        <span aria-hidden="true">🔍</span>
        <label class="visually-hidden" for="search-products">Rechercher un produit</label>
        <input id="search-products" name="search" type="search" placeholder="Rechercher (tomate, miel, yaourt…)" value="<?= htmlspecialchars($search) ?>" onkeyup="fetchProducts()" />
      </div>

      <div class="plu-category-wrap">
        <select id="category-filter" name="category" onchange="fetchProducts()" style="padding: 12px; border-radius: 8px; border: 1px solid #ddd; background: white; font-family: inherit; cursor: pointer;">
            <option value="">Toutes les catégories</option>
            <option value="Fruits & légumes" <?= $category == 'Fruits & légumes' ? 'selected' : '' ?>>🥦 Fruits & légumes</option>
            <option value="Boulangerie" <?= $category == 'Boulangerie' ? 'selected' : '' ?>>🥖 Boulangerie</option>
            <option value="Produits laitiers" <?= $category == 'Produits laitiers' ? 'selected' : '' ?>>🥛 Produits laitiers</option>
            <option value="Viandes & poissons" <?= $category == 'Viandes & poissons' ? 'selected' : '' ?>>🍗 Viandes & poissons</option>
            <option value="Boissons" <?= $category == 'Boissons' ? 'selected' : '' ?>>🥤 Boissons</option>
        </select>
      </div>

      <div class="plu-sort-wrap">
        <select id="sort-filter" name="sort" onchange="fetchProducts()" style="padding: 12px; border-radius: 8px; border: 1px solid #ddd; background: white; font-family: inherit; cursor: pointer;">
            <option value="asc" <?= $sort == 'asc' ? 'selected' : '' ?>>Prix croissant</option>
            <option value="desc" <?= $sort == 'desc' ? 'selected' : '' ?>>Prix décroissant</option>
        </select>
      </div>
      <noscript><button type="submit" class="btn-primary" style="padding: 10px;">Trier/Rechercher</button></noscript>
    </form>

    <div class="plu-section-title">
      <h2 id="catalogue-title">Catalogue produits locaux</h2>
    </div>

    <div class="plu-product-grid" id="product-grid">
      <?php foreach($produits as $prod): ?>
      <article class="plu-product-card <?= ($prod['quantite_stock'] == 0) ? 'plu-product-card-out' : '' ?>">
        <?php 
        $imgGlob = glob(__DIR__ . '/../../back/images/produit_' . $prod['idproduit'] . '.*');
        if($imgGlob) {
            $imgPath = '../../back/images/' . basename($imgGlob[0]);
            echo '<div class="plu-product-visual"><img src="'.htmlspecialchars($imgPath).'" alt="'.htmlspecialchars($prod['nom']).'" /></div>';
        } else {
            echo '<div class="plu-product-visual" aria-hidden="true" style="font-size: 40px; color:#aaa;">🛒</div>';
        }
        ?>
        <div class="plu-product-body">
          <h3><?= htmlspecialchars($prod['nom']) ?></h3>
          <p class="plu-price">
            <?php if($prod['prix'] < $prod['prix_original']): ?>
                <span style="text-decoration: line-through; color: #888; font-size: 0.8em;"><?= htmlspecialchars($prod['prix_original']) ?> TND</span>
                <span style="color: #e74c3c; font-weight: bold;"><?= htmlspecialchars($prod['prix']) ?> TND</span>
                <span class="plu-tag plu-tag-waste" style="background: #e74c3c; color: white; display: inline-block; margin-left: 5px;">🔥 PROMO</span>
            <?php else: ?>
                <?= htmlspecialchars($prod['prix']) ?> TND
            <?php endif; ?>
          </p>
          <div class="plu-meta-row">
            <?php if($prod['quantite_stock'] == 0): ?>
                <span class="plu-tag plu-tag-stock-out">Rupture de stock</span>
            <?php elseif($prod['quantite_stock'] <= $prod['seuil_alerte']): ?>
                <span class="plu-tag plu-tag-stock-low"><?= htmlspecialchars($prod['quantite_stock']) ?> restants</span>
            <?php else: ?>
                <span class="plu-tag plu-tag-stock-ok"><?= htmlspecialchars($prod['quantite_stock']) ?> en stock</span>
            <?php endif; ?>
            
            <?php if($prod['date_expiration']): ?>
                <?php 
                $days = (strtotime($prod['date_expiration']) - time()) / (60 * 60 * 24);
                if($days > 0 && $days <= 5): 
                ?>
                <span class="plu-tag plu-tag-waste">À écouler</span>
                <?php endif; ?>
            <?php endif; ?>
          </div>
          
          <?php if($prod['date_expiration']): ?>
          <p class="plu-expire-line">À consommer avant le <strong><?= date("d/m/Y", strtotime($prod['date_expiration'])) ?></strong></p>
          <?php endif; ?>

          <div class="plu-card-actions">
            <?php if($prod['quantite_stock'] > 0): ?>
                <a href="#" onclick="addToCart(<?= $prod['idproduit'] ?>, '<?= htmlspecialchars($prod['nom'], ENT_QUOTES) ?>', <?= $prod['prix'] ?>); return false;" class="btn-primary">Ajouter au panier</a>
            <?php else: ?>
                <span class="btn-primary plu-btn-muted" aria-disabled="true">Indisponible</span>
            <?php endif; ?>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
      <?php if(empty($produits)): ?>
         <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">Aucun produit disponible pour le moment.</div>
      <?php endif; ?>
    </div>
  </section>

  <footer class="footer">
    <div class="container footer-content">
      <div>
        <h3>NutriVerse</h3>
        <p>Nutrition intelligente pour une vie plus saine.</p>
      </div>

      <div class="footer-links">
        <a href="../../front/front.html#hero">Accueil</a>
        <a href="../../front/front.html#recipes">Recettes</a>
        <a href="#catalogue">Produits locaux</a>
        <a href="../../front/front.html#programs">Programmes</a>
        <a href="../../front/front.html#suivi">Suivi</a>
      </div>
    </div>
  </footer>

  <!-- Cart Sidebar Overlay -->
  <div id="cart-sidebar" class="cart-sidebar">
      <div class="cart-header">
          <h2>Votre Panier</h2>
          <button onclick="toggleCart()">✕</button>
      </div>
      <div id="cart-items" class="cart-items">
          <!-- Inject JavaScript Items -->
      </div>
      <div class="cart-footer">
          <p>Total: <span id="cart-total">0</span> TND</p>
          <button class="btn-primary" onclick="alert('Commande validée!')">Commander</button>
      </div>
  </div>

  <script>
    function toggleCart() {
        document.getElementById('cart-sidebar').classList.toggle('open');
        if (document.getElementById('cart-sidebar').classList.contains('open')) {
            updateCartUI();
        }
    }
    function addToCart(id, nom, prix) {
        let fd = new FormData();
        fd.append('action', 'add');
        fd.append('id', id);
        fd.append('nom', nom);
        fd.append('prix', prix);
        fetch('cartHandler.php', {method: 'POST', body: fd})
        .then(r => r.json()).then(res => {
            updateCartUI();
            document.getElementById('cart-sidebar').classList.add('open');
        });
    }
    function removeFromCart(id) {
        let fd = new FormData();
        fd.append('action', 'remove');
        fd.append('id', id);
        fetch('cartHandler.php', {method: 'POST', body: fd})
        .then(r => r.json()).then(res => updateCartUI());
    }
    function updateCartUI() {
        fetch('cartHandler.php?action=get')
        .then(r => r.json()).then(data => {
            let html = '';
            let total = 0;
            data.forEach(item => {
                html += `<div class="cart-item">
                            <div>
                                <h4>${item.nom}</h4>
                                <p>${item.prix} TND x ${item.qte}</p>
                            </div>
                            <button onclick="removeFromCart(${item.id})" style="background:none;border:none;cursor:pointer;color:red;">🗑</button>
                         </div>`;
                total += item.prix * item.qte;
            });
            document.getElementById('cart-items').innerHTML = html || '<p>Votre panier est vide.</p>';
            document.getElementById('cart-total').innerText = total.toFixed(2);
        });
    }

    // AJAX Filtering Logic
    function fetchProducts() {
        const search = document.getElementById('search-products').value;
        const category = document.getElementById('category-filter').value;
        const sort = document.getElementById('sort-filter').value;
        const grid = document.getElementById('product-grid');

        // Update URL without refresh
        const params = new URLSearchParams({
            search: search,
            category: category,
            sort: sort
        });
        window.history.pushState({}, '', 'listProduit.php?' + params.toString());

        // Visual feedback (fade out)
        grid.style.opacity = '0.5';

        fetch('ajax_filter_produits.php?' + params.toString())
        .then(response => response.text())
        .then(html => {
            grid.innerHTML = html;
            grid.style.opacity = '1';
        })
        .catch(err => {
            console.error('Erreur AJAX:', err);
            grid.style.opacity = '1';
        });
    }

    // Notification Logic
    function toggleNotifs() {
        const dropdown = document.getElementById('notif-dropdown');
        const isOpen = dropdown.classList.contains('show');
        
        // Close other dropdowns if any
        dropdown.classList.toggle('show');
        
        if (!isOpen) {
            loadNotifications();
        }
    }

    function loadNotifications(showAll = false) {
        let url = 'ajax_get_notifications.php';
        if (showAll) {
            url += '?show=all';
            document.getElementById('notif-footer').style.display = 'none';
        }
        
        fetch(url)
        .then(r => r.text())
        .then(html => {
            document.getElementById('notif-content').innerHTML = html;
        });
    }

    function markAllRead() {
        fetch('ajax_get_notifications.php?action=read_all')
        .then(() => {
            loadNotifications();
            // Update badge (simple removal for UI feedback)
            const badge = document.querySelector('.notif-badge-ui');
            if (badge) badge.remove();
        });
    }

    // Close on outside click
    window.addEventListener('click', function(e) {
        if (!e.target.closest('.notif-icon-front') && !e.target.closest('.notif-dropdown')) {
            document.getElementById('notif-dropdown').classList.remove('show');
        }
    });
  </script>
</body>
</html>
