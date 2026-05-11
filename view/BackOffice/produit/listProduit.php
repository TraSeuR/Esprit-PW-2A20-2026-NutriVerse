<?php
require_once __DIR__.'/../../../Controller/ProduitController.php';
require_once __DIR__.'/../../../Controller/NotificationController.php';
require_once __DIR__.'/../../../service/MonitoringService.php';

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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriVerse - Produits locaux (Admin)</title>
  <link rel="stylesheet" href="../assets/back.css" />
  <link rel="stylesheet" href="../../../Produit Locaux/adminproduitlocaux.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>

  <aside class="sidebar" id="sidebar">
    <div class="sidebar-top">
      <div class="brand">
        <img src="../images/logo.png" alt="Logo NutriVerse" class="brand-logo" />
        <div>
          <h2>NutriVerse</h2>
          <p>Back Office</p>
        </div>
      </div>
    </div>
    <nav class="sidebar-menu">
      <a href="../nutri_back.php" class="menu-item">
        <i data-feather="grid"></i>
        <span>Dashboard</span>
      </a>

      <a href="../RECETTE/admin.php" class="menu-item">
        <i data-feather="book-open"></i>
        <span>Recettes</span>
      </a>

      <a href="../../../shop.php?action=admin_users" class="menu-item">
        <i data-feather="users"></i>
        <span>Utilisateurs</span>
      </a>

      <a href="listProduit.php" class="menu-item active">
        <i data-feather="package"></i>
        <span>Produits</span>
      </a>

      <a href="../movement/listMovement.php" class="menu-item">
        <i data-feather="activity"></i>
        <span>Mouvements Stock</span>
      </a>

      <a href="../notifications/listNotifications.php" class="menu-item">
        <i data-feather="bell"></i>
        <span>Notifications</span>
        <?php if(isset($unreadCount) && $unreadCount > 0): ?>
            <span style="background: #e74c3c; color: white; border-radius: 10px; padding: 2px 8px; font-size: 10px; margin-left: auto;"><?= $unreadCount ?></span>
        <?php endif; ?>
      </a>

      <a href="../../../shop.php?action=admin_orders" class="menu-item">
        <i data-feather="shopping-cart"></i>
        <span>Commandes</span>
      </a>

      <a href="../../../shop.php?action=admin_livraisons" class="menu-item">
        <i data-feather="truck"></i>
        <span>Livraisons</span>
      </a>

      <a href="#" class="menu-item">
        <i data-feather="heart-pulse"></i>
        <span>Suivi Santé</span>
      </a>

      <a href="../programme/admin_dashboard.php" class="menu-item">
        <i data-feather="heart"></i>
        <span>Programmes</span>
      </a>
    </nav>
  </aside>

  <div class="main-content">
    <header class="topbar">
      <div class="topbar-left">
        <h2>Gestion des Produits</h2>
      </div>
      <div class="topbar-right" style="display: flex; align-items: center; gap: 20px;">
          <a href="../notifications/listNotifications.php" style="position: relative; color: var(--text-main);">
            <i data-feather="bell"></i>
            <?php if($unreadCount > 0): ?>
                <span style="position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 10px; display: flex; align-items: center; justify-content: center;">
                    <?= $unreadCount ?>
                </span>
            <?php endif; ?>
          </a>
          <div class="user-profile" style="display: flex; align-items: center; gap: 10px;">
              <img src="../images/admin.png" alt="Admin" style="width: 35px; height: 35px; border-radius: 50%;" onerror="this.src='https://ui-avatars.com/api/?name=Admin'"/>
              <span>Admin</span>
          </div>
      </div>
    </header>

    <main class="dashboard-content">
      <section class="page-header">
        <div>
          <span class="section-badge">Catalogue</span>
          <h1>Produits locaux</h1>
        </div>
        <a class="export-btn" href="addProduit.php" style="background: #27ae60;">
          <i data-feather="plus"></i>
          Ajouter un produit
        </a>
      </section>

      <section class="pl-panel">
        <div class="pl-panel-header">
          <h3>Catalogue</h3>
        </div>
        <div class="pl-table-wrap">
          <table class="pl-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prix</th>
                <th>Expiration</th>
                <th>Quantité</th>
                <th>Seuil</th>
                <th>Catégorie</th>
                <th>Statut</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($produits as $prod): ?>
              <tr>
                <td class="pl-mono">P-<?= htmlspecialchars($prod['idproduit']) ?></td>
                <td style="font-weight: 500;"><?= htmlspecialchars($prod['nom']) ?></td>
                <td>
                    <?php if($prod['prix'] < ($prod['prix_original'] ?? $prod['prix'])): ?>
                        <span style="text-decoration: line-through; color: #aaa; margin-right: 5px;"><?= htmlspecialchars($prod['prix_original']) ?></span>
                        <span style="color: #e74c3c; font-weight: 600;"><?= htmlspecialchars($prod['prix']) ?> TND</span>
                    <?php else: ?>
                        <span style="font-weight: 500;"><?= htmlspecialchars($prod['prix']) ?> TND</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php 
                        $expDate = new DateTime($prod['date_expiration']);
                        $today = new DateTime();
                        $diff = $today->diff($expDate);
                        $days = $diff->invert ? -$diff->days : $diff->days;
                        $color = $days <= 5 ? '#e74c3c' : 'inherit';
                    ?>
                    <span style="color: <?= $color ?>; font-weight: 500;"><?= htmlspecialchars($prod['date_expiration']) ?></span>
                    <?php if($days >= 0 && $days <= 10): ?>
                        <br><small style="color: #f39c12;">(Réduction active)</small>
                    <?php endif; ?>
                </td>
                <td style="font-weight: 500;"><?= htmlspecialchars($prod['quantite_stock']) ?></td>
                <td style="font-weight: 500;"><?= htmlspecialchars($prod['seuil_alerte']) ?></td>
                <td><?= htmlspecialchars($prod['categorie']) ?></td>
                <td>
                  <?php if($prod['quantite_stock'] <= $prod['seuil_alerte']): ?>
                      <span style="color: #e67e22; font-weight: 500;">Sous seuil</span>
                  <?php else: ?>
                      <span style="color: #27ae60; font-weight: 500;">actif</span>
                  <?php endif; ?>
                </td>
                <td class="pl-actions-cell">
                  <a href="updateProduit.php?id=<?= $prod['idproduit'] ?>" style="color: #3498db; text-decoration: underline; margin-right: 10px;">Modifier</a>
                  <a href="listProduit.php?action=delete&id=<?= $prod['idproduit'] ?>" style="color: #3498db; text-decoration: underline;" onclick="return confirm('Êtes-vous sûr ?')">Supprimer</a>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if(empty($produits)): ?>
              <tr><td colspan="9" style="text-align:center">Aucun produit trouvé</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>
  <script>feather.replace();</script>
</body>
</html>
