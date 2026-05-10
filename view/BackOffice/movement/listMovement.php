<?php
require_once __DIR__.'/../../../Controller/MovementController.php';
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Produit']);
$movementController = new MovementController();

// Handle delete
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $movementController->deleteMovement($_GET['id']);
    header('Location: listMovement.php');
    exit;
}

$movements = $movementController->getMovements();

// Calculate Stats
$totalMouvements = count($movements);
$entreesCount = count(array_filter($movements, function($m) { return in_array($m['type_mouvement'], ['achat', 'ajout_stock']); }));
$sortiesCount = count(array_filter($movements, function($m) { return !in_array($m['type_mouvement'], ['achat', 'ajout_stock']); }));
$totalVolume = array_sum(array_column($movements, 'quantite'));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriVerse - Mouvements Stock</title>
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
    .stat-icon.purple { background: #f3f0ff; color: #7950f2; }
    
    .table-card { background: white; border-radius: 28px; box-shadow: var(--shadow); overflow: hidden; margin-top: 24px; }
    .card-header { padding: 24px 34px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
    
    .mov-pill { padding: 6px 12px; border-radius: 99px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
    .mov-pill.entry { background: #eaf8ef; color: #21b66f; }
    .mov-pill.exit { background: #fff5f5; color: #ff6b6b; }
    
    .impact-pos { color: #21b66f; font-weight: 700; font-size: 1.1rem; }
    .impact-neg { color: #ff6b6b; font-weight: 700; font-size: 1.1rem; }
    
    .btn-icon { width: 36px; height: 36px; border-radius: 10px; display: grid; place-items: center; color: white; transition: 0.2s; text-decoration: none; border: none; cursor: pointer; }
    .btn-delete { background: #ff6b6b; }
    .btn-delete:hover { background: #fa5252; }
    
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
          <span class="section-badge">Stock local</span>
          <h1>Historique des Mouvements</h1>
          <p>Tracez chaque entrée et sortie de stock pour une gestion précise.</p>
        </div>
        <a class="export-btn" href="addMovement.php" style="background: var(--green); color: white; text-decoration: none;">
          <i data-feather="plus"></i>
          Nouveau Mouvement
        </a>
      </section>

      <!-- Stats Grid -->
      <section class="stats-grid fade-up delay-1">
        <div class="stat-card">
          <div>
            <p style="color: var(--muted); font-size: 0.9rem; margin-bottom: 4px;">Total Mouvements</p>
            <h2 style="font-size: 1.8rem;"><?= $totalMouvements ?></h2>
          </div>
          <div class="stat-icon blue"><i data-feather="list"></i></div>
        </div>
        <div class="stat-card">
          <div>
            <p style="color: var(--muted); font-size: 0.9rem; margin-bottom: 4px;">Entrées (Achat)</p>
            <h2 style="font-size: 1.8rem;"><?= $entreesCount ?></h2>
          </div>
          <div class="stat-icon green"><i data-feather="arrow-down-left"></i></div>
        </div>
        <div class="stat-card">
          <div>
            <p style="color: var(--muted); font-size: 0.9rem; margin-bottom: 4px;">Sorties (Vente)</p>
            <h2 style="font-size: 1.8rem;"><?= $sortiesCount ?></h2>
          </div>
          <div class="stat-icon red"><i data-feather="arrow-up-right"></i></div>
        </div>
        <div class="stat-card">
          <div>
            <p style="color: var(--muted); font-size: 0.9rem; margin-bottom: 4px;">Volume Total</p>
            <h2 style="font-size: 1.8rem;"><?= $totalVolume ?></h2>
          </div>
          <div class="stat-icon purple"><i data-feather="activity"></i></div>
        </div>
      </section>

      <!-- Table Section -->
      <section class="table-card fade-up delay-2">
        <div class="card-header">
          <h3>Flux de Stock</h3>
          <div style="color: var(--muted); font-size: 0.85rem;">Journal complet des activités</div>
        </div>
        <div class="table-wrapper">
          <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f8f9fa;">
              <tr>
                <th style="padding: 18px 24px;">ID</th>
                <th>Produit</th>
                <th>Titre / Motif</th>
                <th>Type</th>
                <th>Impact</th>
                <th>Date du mouvement</th>
                <th style="text-align: right; padding-right: 34px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($movements as $mov): 
                  $isEntry = in_array($mov['type_mouvement'], ['achat', 'ajout_stock']);
              ?>
              <tr style="border-bottom: 1px solid #f1f3f5; transition: 0.2s;" onmouseover="this.style.background='#fcfdfe'" onmouseout="this.style.background='transparent'">
                <td style="padding: 18px 24px; font-family: monospace; color: var(--muted);">#<?= $mov['id'] ?></td>
                <td>
                  <div style="font-weight: 600; color: var(--text);"><?= htmlspecialchars($mov['nom_produit'] ?? 'N/A') ?></div>
                </td>
                <td>
                  <div style="font-weight: 500;"><?= htmlspecialchars($mov['titre']) ?></div>
                  <div style="font-size: 0.75rem; color: var(--muted);"><?= htmlspecialchars($mov['description']) ?></div>
                </td>
                <td>
                  <span class="mov-pill <?= $isEntry ? 'entry' : 'exit' ?>">
                    <?= htmlspecialchars($mov['type_mouvement']) ?>
                  </span>
                </td>
                <td>
                  <span class="<?= $isEntry ? 'impact-pos' : 'impact-neg' ?>">
                    <?= $isEntry ? '+' : '-' ?><?= $mov['quantite'] ?>
                  </span>
                </td>
                <td style="color: var(--muted); font-weight: 500;">
                  <?= date('d/m/Y H:i', strtotime($mov['date_mouvement'])) ?>
                </td>
                <td style="text-align: right; padding-right: 34px;">
                  <div class="action-btns" style="justify-content: flex-end;">
                    <a href="listMovement.php?action=delete&id=<?= $mov['id'] ?>" class="btn-icon btn-delete" title="Supprimer de l'historique" onclick="return confirm('Supprimer ce mouvement de l\'historique ?')">
                      <i data-feather="trash-2" style="width: 16px;"></i>
                    </a>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if(empty($movements)): ?>
                <tr><td colspan="7" style="text-align:center; padding: 40px; color: var(--muted);">Aucun mouvement enregistré.</td></tr>
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
