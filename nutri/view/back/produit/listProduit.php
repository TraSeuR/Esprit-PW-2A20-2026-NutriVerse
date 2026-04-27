<?php
require_once __DIR__.'/../../../controller/ProduitController.php';
require_once __DIR__.'/../../../controller/NotificationController.php';
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
  <link rel="stylesheet" href="../../back/assets/back.css" />
  <link rel="stylesheet" href="../../../Produit Locaux/adminproduitlocaux.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>

  <aside class="sidebar" id="sidebar">
    <div class="sidebar-top">
      <div class="brand">
        <img src="../../back/images/logo.png" alt="Logo NutriVerse" class="brand-logo" />
        <div>
          <h2>NutriVerse</h2>
          <p>Back Office</p>
        </div>
      </div>
    </div>
    <nav class="sidebar-menu">
      <a href="../../back/back.html" class="menu-item">
        <i data-feather="grid"></i>
        <span>Dashboard</span>
      </a>

      <a href="#" class="menu-item">
        <i data-feather="book-open"></i>
        <span>Recettes</span>
      </a>

      <a href="#" class="menu-item">
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
        <?php if($unreadCount > 0): ?>
            <span class="notif-badge"><?= $unreadCount ?></span>
        <?php endif; ?>
      </a>

      <a href="#" class="menu-item">
        <i data-feather="shopping-cart"></i>
        <span>Commandes</span>
      </a>

      <a href="#" class="menu-item">
        <i data-feather="activity"></i>
        <span>Suivi Santé</span>
      </a>

      <a href="#" class="menu-item">
        <i data-feather="heart"></i>
        <span>Programmes</span>
      </a>

      <a href="#" class="menu-item">
        <i data-feather="settings"></i>
        <span>Paramètres</span>
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
              <img src="../../back/images/admin.png" alt="Admin" style="width: 35px; height: 35px; border-radius: 50%;" onerror="this.src='https://ui-avatars.com/api/?name=Admin'"/>
              <span>Admin</span>
          </div>
      </div>
    </header>

    <main class="dashboard-content">
      <section class="page-header">
        <div>
          <span class="section-badge">Marketplace locale</span>
          <h1>Produits locaux</h1>
        </div>
        <a class="export-btn" href="addProduit.php">
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
                <td><?= htmlspecialchars($prod['nom']) ?></td>
                <td>
                    <?php if($prod['prix'] < $prod['prix_original']): ?>
                        <span style="text-decoration: line-through; color: #888; font-size: 0.9em;"><?= htmlspecialchars($prod['prix_original']) ?></span>
                        <span style="color: #e74c3c; font-weight: bold;"><?= htmlspecialchars($prod['prix']) ?> TND</span>
                    <?php else: ?>
                        <?= htmlspecialchars($prod['prix']) ?> TND
                    <?php endif; ?>
                </td>
                <td>
                    <?php 
                        $expDate = new DateTime($prod['date_expiration']);
                        $today = new DateTime();
                        $diff = $today->diff($expDate);
                        $days = $diff->invert ? -$diff->days : $diff->days;
                        $color = $days <= 3 ? 'red' : ($days <= 7 ? 'orange' : 'inherit');
                    ?>
                    <span style="color: <?= $color ?>"><?= htmlspecialchars($prod['date_expiration']) ?></span>
                    <?php if($days >= 0 && $days <= 14): ?>
                        <br><small style="color: #e67e22;">(Réduction active)</small>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($prod['quantite_stock']) ?></td>
                <td><?= htmlspecialchars($prod['seuil_alerte']) ?></td>
                <td><?= htmlspecialchars($prod['categorie']) ?></td>
                <td>
                  <?php if($prod['quantite_stock'] <= $prod['seuil_alerte']): ?>
                      <span class="pl-badge pl-badge-low">Sous seuil</span>
                  <?php else: ?>
                      <span class="pl-badge pl-badge-ok"><?= htmlspecialchars($prod['statut']) ?></span>
                  <?php endif; ?>
                </td>
                <td class="pl-actions-cell">
                  <a href="updateProduit.php?id=<?= $prod['idproduit'] ?>" class="pl-btn-icon pl-btn-edit">Modifier</a>
                  <a href="listProduit.php?action=delete&id=<?= $prod['idproduit'] ?>" class="pl-btn-icon pl-btn-danger" onclick="return confirm('Êtes-vous sûr ?')">Supprimer</a>
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
