<?php
require_once __DIR__.'/../../../Controller/ProduitController.php';
require_once __DIR__.'/../../../Controller/NotificationController.php';
require_once __DIR__.'/../../../service/MonitoringService.php';
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Produit']);

$produitController = new ProduitController();
$notifController = new NotificationController();

// Trigger automatic monitoring
MonitoringService::checkAll();

$unreadCount = $notifController->getUnreadCount();

// Handle delete
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $produitController->deleteProduit($_GET['id']);
    header('Location: listProduit.php');
    exit;
}

$produits = $produitController->getProduits();

// Calculate Stats
$totalProduits = count($produits);
$lowStockCount = count(array_filter($produits, function($p) { return $p['quantite_stock'] <= $p['seuil_alerte']; }));
$promoCount = count(array_filter($produits, function($p) { return ($p['prix_original'] ?? $p['prix']) > $p['prix']; }));
$expiringCount = count(array_filter($produits, function($p) { 
    $d = new DateTime($p['date_expiration']); 
    $now = new DateTime(); 
    return $now->diff($d)->days <= 5 && !$now->diff($d)->invert; 
}));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriVerse - Produits locaux (Admin)</title>
  <link rel="stylesheet" href="../assets/back.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <script src="https://unpkg.com/feather-icons"></script>
  <style>
    /* Custom enhancements for this page */
    .dashboard-content { padding: 30px; }
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-bottom: 34px; }
    .stat-card { background: white; padding: 24px; border-radius: 24px; box-shadow: var(--shadow); display: flex; align-items: center; justify-content: space-between; transition: 0.3s ease; }
    .stat-card:hover { transform: translateY(-5px); }
    .stat-icon { width: 54px; height: 54px; border-radius: 16px; display: grid; place-items: center; }
    .stat-icon.green { background: #eaf8ef; color: #21b66f; }
    .stat-icon.orange { background: #fff4e6; color: #ff922b; }
    .stat-icon.blue { background: #e7f5ff; color: #339af0; }
    .stat-icon.red { background: #fff5f5; color: #ff6b6b; }
    
    .table-card { background: white; border-radius: 28px; box-shadow: var(--shadow); overflow: hidden; margin-top: 24px; }
    .card-header { padding: 24px 34px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
    
    .status-pill { padding: 6px 12px; border-radius: 99px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
    .status-pill.active { background: #eaf8ef; color: #21b66f; }
    .status-pill.warning { background: #fff4e6; color: #ff922b; }
    .status-pill.danger { background: #fff5f5; color: #ff6b6b; }
    
    .action-btns { display: flex; gap: 10px; }
    .btn-icon { width: 36px; height: 36px; border-radius: 10px; display: grid; place-items: center; color: white; transition: 0.2s; text-decoration: none; border: none; cursor: pointer; }
    .btn-edit { background: #339af0; }
    .btn-edit:hover { background: #1c7ed6; }
    .btn-delete { background: #ff6b6b; }
    .btn-delete:hover { background: #fa5252; }
    
    .price-original { text-decoration: line-through; color: #adb5bd; font-size: 0.85rem; margin-right: 4px; }
    .price-current { font-weight: 700; color: #212529; }
    .price-promo { color: #fa5252; font-weight: 700; }
    
    .fade-up { animation: fadeUp 0.6s ease forwards; opacity: 0; transform: translateY(20px); }
    @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }
    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
  </style>
</head>
<body>

  <?php include $_SERVER['DOCUMENT_ROOT'] . '/integ/view/BackOffice/sidebar.php'; ?>

  <div class="main-content">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/integ/view/BackOffice/topbar.php'; ?>

    <main class="dashboard-content">
      <section class="page-header fade-up">
        <div>
          <span class="section-badge">Catalogue local</span>
          <h1>Gestion des Produits</h1>
          <p>Gérez vos produits, surveillez les stocks et les dates d'expiration.</p>
        </div>
        <a class="export-btn" href="addProduit.php" style="background: var(--green); color: white; text-decoration: none;">
          <i data-feather="plus"></i>
          Ajouter un produit
        </a>
      </section>

      <!-- Stats Grid -->
      <section class="stats-grid fade-up delay-1">
        <div class="stat-card">
          <div>
            <p style="color: var(--muted); font-size: 0.9rem; margin-bottom: 4px;">Total Produits</p>
            <h2 style="font-size: 1.8rem;"><?= $totalProduits ?></h2>
          </div>
          <div class="stat-icon blue"><i data-feather="package"></i></div>
        </div>
        <div class="stat-card">
          <div>
            <p style="color: var(--muted); font-size: 0.9rem; margin-bottom: 4px;">Alertes Stock</p>
            <h2 style="font-size: 1.8rem;"><?= $lowStockCount ?></h2>
          </div>
          <div class="stat-icon orange"><i data-feather="alert-circle"></i></div>
        </div>
        <div class="stat-card">
          <div>
            <p style="color: var(--muted); font-size: 0.9rem; margin-bottom: 4px;">Promotions Actives</p>
            <h2 style="font-size: 1.8rem;"><?= $promoCount ?></h2>
          </div>
          <div class="stat-icon green"><i data-feather="trending-down"></i></div>
        </div>
        <div class="stat-card">
          <div>
            <p style="color: var(--muted); font-size: 0.9rem; margin-bottom: 4px;">Expirations (5j)</p>
            <h2 style="font-size: 1.8rem;"><?= $expiringCount ?></h2>
          </div>
          <div class="stat-icon red"><i data-feather="clock"></i></div>
        </div>
      </section>

      <!-- Table Section -->
      <section class="table-card fade-up delay-2">
        <div class="card-header">
          <h3>Liste du Catalogue</h3>
          <div style="color: var(--muted); font-size: 0.85rem;">Mise à jour en temps réel</div>
        </div>
        <div class="table-wrapper">
          <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f8f9fa;">
              <tr>
                <th style="padding: 18px 24px;">ID</th>
                <th>Produit</th>
                <th>Prix</th>
                <th>Expiration</th>
                <th>Stock</th>
                <th>Seuil</th>
                <th>Catégorie</th>
                <th>Statut</th>
                <th style="text-align: right; padding-right: 34px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($produits as $prod): 
                  $isLow = $prod['quantite_stock'] <= $prod['seuil_alerte'];
                  $expDate = new DateTime($prod['date_expiration']);
                  $today = new DateTime();
                  $diff = $today->diff($expDate);
                  $days = $diff->invert ? -$diff->days : $diff->days;
                  $isExpiring = $days <= 5;
                  $isPromo = ($prod['prix_original'] ?? $prod['prix']) > $prod['prix'];
              ?>
              <tr style="border-bottom: 1px solid #f1f3f5; transition: 0.2s;" onmouseover="this.style.background='#fcfdfe'" onmouseout="this.style.background='transparent'">
                <td style="padding: 18px 24px; font-family: monospace; color: var(--muted);">P-<?= $prod['idproduit'] ?></td>
                <td>
                  <div style="font-weight: 600; color: var(--text);"><?= htmlspecialchars($prod['nom']) ?></div>
                </td>
                <td>
                  <?php if($isPromo): ?>
                    <span class="price-original"><?= number_format($prod['prix_original'], 2) ?></span>
                    <span class="price-promo"><?= number_format($prod['prix'], 2) ?> TND</span>
                  <?php else: ?>
                    <span class="price-current"><?= number_format($prod['prix'], 2) ?> TND</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div style="color: <?= $isExpiring ? '#ff6b6b' : 'inherit' ?>; font-weight: 500;">
                    <?= $prod['date_expiration'] ?>
                    <?php if($isExpiring): ?>
                      <div style="font-size: 0.7rem; color: #ff6b6b; font-weight: 700;">PROCHE EXPIRATION</div>
                    <?php endif; ?>
                  </div>
                </td>
                <td>
                  <span style="font-weight: 700; color: <?= $isLow ? '#ff922b' : 'var(--green)' ?>;">
                    <?= $prod['quantite_stock'] ?>
                  </span>
                </td>
                <td style="color: var(--muted);"><?= $prod['seuil_alerte'] ?></td>
                <td>
                  <span style="padding: 4px 10px; background: #f1f3f5; border-radius: 8px; font-size: 0.8rem; font-weight: 500;"><?= htmlspecialchars($prod['categorie']) ?></span>
                </td>
                <td>
                  <?php if($prod['quantite_stock'] == 0): ?>
                    <span class="status-pill danger">Rupture</span>
                  <?php elseif($isLow): ?>
                    <span class="status-pill warning">Bas</span>
                  <?php else: ?>
                    <span class="status-pill active">Actif</span>
                  <?php endif; ?>
                </td>
                <td style="text-align: right; padding-right: 34px;">
                  <div class="action-btns" style="justify-content: flex-end;">
                    <a href="updateProduit.php?id=<?= $prod['idproduit'] ?>" class="btn-icon btn-edit" title="Modifier">
                      <i data-feather="edit-2" style="width: 16px;"></i>
                    </a>
                    <a href="listProduit.php?action=delete&id=<?= $prod['idproduit'] ?>" class="btn-icon btn-delete" title="Supprimer" onclick="return confirm('Supprimer ce produit ?')">
                      <i data-feather="trash-2" style="width: 16px;"></i>
                    </a>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if(empty($produits)): ?>
                <tr><td colspan="9" style="text-align:center; padding: 40px; color: var(--muted);">Aucun produit dans le catalogue.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
        feather.replace();
    });
  </script>
</body>
</html>
