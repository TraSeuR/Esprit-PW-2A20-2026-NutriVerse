<?php
require_once __DIR__.'/../../../controller/MovementController.php';
$movementController = new MovementController();

// Handle delete
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $movementController->deleteMovement($_GET['id']);
    header('Location: listMovement.php');
    exit;
}

$movements = $movementController->getMovements();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriVerse - Mouvements Stock</title>
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

      <a href="../produit/listProduit.php" class="menu-item">
        <i data-feather="package"></i>
        <span>Produits</span>
      </a>

      <a href="listMovement.php" class="menu-item active">
        <i data-feather="activity"></i>
        <span>Mouvements Stock</span>
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
        <h2>Gestion des Mouvements</h2>
      </div>
    </header>

    <main class="dashboard-content">
      <section class="page-header">
        <div>
          <span class="section-badge">Stock local</span>
          <h1>Historique des mouvements</h1>
        </div>
        <a class="export-btn" href="addMovement.php">
          <i data-feather="plus"></i>
          Ajouter un mouvement (Achat/Vente)
        </a>
      </section>

      <section class="pl-panel">
        <div class="pl-panel-header">
          <h3>Liste des mouvements</h3>
        </div>
        <div class="pl-table-wrap">
          <table class="pl-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Produit</th>
                <th>Titre</th>
                <th>Description</th>
                <th>Type</th>
                <th>Quantité Impactée</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($movements as $mov): ?>
              <tr>
                <td class="pl-mono">#<?= htmlspecialchars($mov['id']) ?></td>
                <td><?= htmlspecialchars($mov['nom_produit'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($mov['titre']) ?></td>
                <td><?= htmlspecialchars($mov['description']) ?></td>
                <td>
                  <?php if(in_array($mov['type_mouvement'], ['achat', 'ajout_stock'])): ?>
                      <span class="pl-badge pl-badge-ok"><?= htmlspecialchars($mov['type_mouvement']) ?></span>
                  <?php else: ?>
                      <span class="pl-badge pl-badge-low"><?= htmlspecialchars($mov['type_mouvement']) ?></span>
                  <?php endif; ?>
                </td>
                <td style="font-weight: bold; color: <?= in_array($mov['type_mouvement'], ['achat', 'ajout_stock']) ? 'green' : 'red' ?>;">
                  <?= in_array($mov['type_mouvement'], ['achat', 'ajout_stock']) ? '+' : '-' ?><?= htmlspecialchars($mov['quantite']) ?>
                </td>
                <td><?= htmlspecialchars($mov['date_mouvement']) ?></td>
                <td class="pl-actions-cell">
                  <a href="listMovement.php?action=delete&id=<?= $mov['id'] ?>" class="pl-btn-icon pl-btn-danger" onclick="return confirm('Êtes-vous sûr ?')">Supprimer l'historique</a>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if(empty($movements)): ?>
              <tr><td colspan="8" style="text-align:center">Aucun mouvement trouvé</td></tr>
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
