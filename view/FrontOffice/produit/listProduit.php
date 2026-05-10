<?php
if(session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__.'/../../../Controller/ProduitController.php';
require_once __DIR__.'/../../../Controller/NotificationController.php';
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
  <link rel="stylesheet" href="../assets/front.css" />
  <link rel="stylesheet" href="../assets/utilisateurproduitlocaux.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <style>

    .plu-product-visual { padding: 0 !important; overflow: hidden; display:flex; align-items:center; justify-content:center; background:#f4f4f4;}
    .plu-product-visual img { width: 100%; height: 150px; object-fit: cover; }
  </style>
</head>
<body>
  <?php 
  $rel = "../";

  include '../header.php'; 
  ?>

  <!-- LINK GLOBAL NUTRIVERSE STYLE -->
  <link rel="stylesheet" href="../assets/style.css">

  <!-- LUXURY DECORATIVE ELEMENTS -->
  <div class="luxury-bg-blob blob-1"></div>
  <div class="luxury-bg-blob blob-2" style="background: var(--primary);"></div>

  <!-- HERO SECTION -->
  <section class="recipe-header fade-up" style="margin-top: 0;">
      <div class="icons">
          <span>🥗</span><span>🍎</span><span>🥑</span><span>🍉</span><span>🥦</span><span>🍓</span>
          <span>🥕</span><span>🍋</span><span>🍇</span><span>🥝</span><span>🍍</span><span>🥬</span>
      </div>
      <div class="header-content">
          <h1 style="margin-bottom: 0;">NutriVerse</h1>
          <h2 style="font-size: 2rem; opacity: 0.9; font-weight: 700; margin: 10px 0; color: white;">Produits Locaux</h2>
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
        $imgGlob = glob(__DIR__ . '/../../BackOffice/images/produit_' . $prod['idproduit'] . '.*');
        if($imgGlob) {
            $imgPath = '../../BackOffice/images/' . basename($imgGlob[0]);
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
                <span class="plu-tag-promo">🏷 PROMO</span>
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
                <span class="plu-tag-waste">À écouler</span>
                <?php endif; ?>
            <?php endif; ?>
          </div>
          
          <?php if($prod['date_expiration']): ?>
          <p class="plu-expire-line">À consommer avant le <strong><?= date("d/m/Y", strtotime($prod['date_expiration'])) ?></strong></p>
          <?php endif; ?>

            <?php if($prod['quantite_stock'] > 0): ?>
                <span class="btn-primary" style="background: #ccc; cursor: default;">En stock</span>
            <?php else: ?>
                <span class="btn-primary plu-btn-muted" aria-disabled="true">Indisponible</span>
            <?php endif; ?>
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
        <a href="../index.php#hero">Accueil</a>
        <a href="../index.php#recipes">Recettes</a>
        <a href="#catalogue">Produits locaux</a>
        <a href="../index.php#programs">Programmes</a>
        <a href="../index.php#suivi">Suivi</a>
      </div>
    </div>
  </footer>



  <script>
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
  </script>
</body>
</html>
