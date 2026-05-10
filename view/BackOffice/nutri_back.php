<?php
require_once __DIR__ . '/../../controller/RegimeC.php';
require_once __DIR__ . '/../../controller/PlanningC.php';
require_once __DIR__ . '/../../controller/recetteC.php';
require_once __DIR__ . '/../../Controller/ProduitController.php';
require_once __DIR__ . '/../../Controller/MovementController.php';
require_once __DIR__ . '/../../Controller/NotificationController.php';
require_once __DIR__ . '/../../service/MonitoringService.php';

$rCtrl = new RegimeC();
$pCtrl = new PlanningC();
$recetteC = new recetteC();
$produitController = new ProduitController();
$movementController = new MovementController();
$notifController = new NotificationController();

// Trigger monitoring
MonitoringService::checkAll();

$unreadCount = $notifController->getUnreadCount();
$prodStats = $produitController->getDashboardStats();

// Fetch products under alert threshold
$lowStockProduits = [];
$allProduits = $produitController->getProduits();
foreach($allProduits as $p) {
    if($p['quantite_stock'] <= $p['seuil_alerte']) {
        $lowStockProduits[] = $p;
    }
}

$regimes = $rCtrl->listRegimes();
$plannings = $pCtrl->listPlannings();
$totalRegimes = count($regimes);
$totalPlannings = count($plannings);
$totalRecettesCount = $recetteC->countRecettes();

// --- BUSINESS STATS ---
$db = config::getConnexion();
$totalOrders = 0;
$totalRevenue = 0;
$totalUsers = $db->query("SELECT COUNT(*) FROM user")->fetchColumn();

// Recent Activity
$recentOrders = [];
$recentUsers = $db->query("SELECT * FROM user ORDER BY id_user DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Chart Data (Monthly Growth)
$chartJson = json_encode(array_fill(0, 12, 0));

// Macros Data
$avgMacros = ['prot' => 0, 'gluc' => 0, 'lip' => 0];
if ($totalRegimes > 0) {
    foreach ($regimes as $r) {
        $avgMacros['prot'] += $r->getProteine();
        $avgMacros['gluc'] += $r->getGlucide();
        $avgMacros['lip'] += $r->getLipides();
    }
    $avgMacros['prot'] /= $totalRegimes;
    $avgMacros['gluc'] /= $totalRegimes;
    $avgMacros['lip'] /= $totalRegimes;
}

// Validation Rate
$acceptedCount = 0;
foreach ($plannings as $p) {
    if ($p->getStatut() === 'accepte') $acceptedCount++;
}
$validationRate = $totalPlannings > 0 ? round(($acceptedCount / $totalPlannings) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriVerse - Dashboard de Gestion</title>
  <link rel="stylesheet" href="assets/back.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://unpkg.com/feather-icons"></script>
  <style>
    :root { --p-green: #21b66f; --p-orange: #ff922b; --p-blue: #339af0; --p-red: #ff6b6b; --p-purple: #7950f2; }
    .dashboard-content { padding: 30px; }
    .main-stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-bottom: 34px; }
    
    .glass-card { background: rgba(255, 255, 255, 0.95); border-radius: 28px; box-shadow: var(--shadow); padding: 24px; transition: 0.3s ease; border: 1px solid rgba(255, 255, 255, 0.2); }
    .glass-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); }
    
    .stat-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
    .stat-val { font-size: 2rem; font-weight: 800; color: var(--text); margin: 0; }
    .stat-label { color: var(--muted); font-size: 0.9rem; font-weight: 500; }
    .stat-icon-wrap { width: 48px; height: 48px; border-radius: 14px; display: grid; place-items: center; }
    
    .icon-green { background: #eaf8ef; color: var(--p-green); }
    .icon-blue { background: #e7f5ff; color: var(--p-blue); }
    .icon-orange { background: #fff4e6; color: var(--p-orange); }
    .icon-purple { background: #f3f0ff; color: var(--p-purple); }
    .icon-red { background: #fff5f5; color: var(--p-red); }

    .charts-row { display: grid; grid-template-columns: 1fr; gap: 24px; margin-bottom: 34px; }
    .chart-box { padding: 24px; }
    .chart-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 6px; }
    .chart-sub { color: var(--muted); font-size: 0.85rem; margin-bottom: 20px; }

    .secondary-stats { display: flex; flex-direction: column; gap: 24px; }
    
    .activity-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; }
    .table-mini th { text-align: left; font-size: 0.75rem; color: var(--muted); text-transform: uppercase; padding: 12px; }
    .table-mini td { padding: 12px; font-size: 0.9rem; border-top: 1px solid #f1f3f5; }
    
    .badge-pill { padding: 4px 10px; border-radius: 99px; font-size: 0.7rem; font-weight: 700; }
    .badge-success { background: #eaf8ef; color: #21b66f; }
    .badge-warning { background: #fff4e6; color: #ff922b; }

    .fade-up { animation: fadeUp 0.6s ease forwards; opacity: 0; transform: translateY(20px); }
    @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }
    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }
  </style>
</head>
<body>

  <?php include $_SERVER['DOCUMENT_ROOT'] . '/integ/view/BackOffice/sidebar.php'; ?>

  <div class="main-content">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/integ/view/BackOffice/topbar.php'; ?>

    <main class="dashboard-content">
      <section class="page-header fade-up">
        <div>
          <span class="section-badge">Vue d'ensemble</span>
          <h1>Console NutriVerse</h1>
          <p>Bienvenue dans votre centre de pilotage intelligent.</p>
        </div>
        <button class="export-btn" style="background: var(--green); color: white; border: none; border-radius: 12px; padding: 10px 20px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;">
          <i data-feather="download"></i> Exporter Rapport
        </button>
      </section>

      <!-- Row 1: Primary Business Stats -->
      <section class="main-stats-grid fade-up delay-1">
        <div class="glass-card">
          <div class="stat-header">
            <span class="stat-label">Utilisateurs</span>
            <div class="stat-icon-wrap icon-blue"><i data-feather="users"></i></div>
          </div>
          <h2 class="stat-val"><?= number_format($totalUsers) ?></h2>
          <p style="font-size: 0.75rem; color: var(--p-green); margin-top: 8px; font-weight: 600;">↑ 12% ce mois</p>
        </div>

        <div class="glass-card">
          <div class="stat-header">
            <span class="stat-label">Recettes</span>
            <div class="stat-icon-wrap icon-orange"><i data-feather="book-open"></i></div>
          </div>
          <h2 class="stat-val"><?= $totalRecettesCount ?></h2>
          <p style="font-size: 0.75rem; color: var(--muted); margin-top: 8px;">Contenu nutritionnel</p>
        </div>
      </section>

      <!-- Row 2: Charts and Secondary Alerts -->
      <section class="charts-row fade-up delay-2">


        <div class="secondary-stats">
          <div class="glass-card" style="display: flex; align-items: center; gap: 20px;">
            <div class="stat-icon-wrap icon-red"><i data-feather="alert-triangle"></i></div>
            <div>
              <h4 style="margin:0; font-size: 1.2rem;"><?= count($lowStockProduits) ?></h4>
              <p style="margin:0; color: var(--muted); font-size: 0.8rem;">Produits en rupture/bas</p>
            </div>
            <a href="produit/listProduit.php" style="margin-left: auto; color: var(--p-blue);"><i data-feather="chevron-right"></i></a>
          </div>
          <div class="glass-card" style="display: flex; align-items: center; gap: 20px;">
            <div class="stat-icon-wrap icon-green"><i data-feather="check-circle"></i></div>
            <div>
              <h4 style="margin:0; font-size: 1.2rem;"><?= $validationRate ?>%</h4>
              <p style="margin:0; color: var(--muted); font-size: 0.8rem;">Taux de validation plannings</p>
            </div>
          </div>
          <div class="glass-card chart-box" style="flex: 1;">
            <h3 class="chart-title" style="font-size: 0.9rem;">Apports Moyens (Macros)</h3>
            <div style="height: 120px;">
              <canvas id="miniMacrosChart"></canvas>
            </div>
          </div>
        </div>
      </section>

      <!-- Row 3: Lists -->
      <section class="activity-grid fade-up delay-3">
        <!-- Recent Orders -->


        <!-- Recent Plannings -->
        <div class="glass-card">
          <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 class="chart-title">📅 Demandes Plannings</h3>
            <a href="programme/admin_dashboard.php" style="font-size: 0.8rem; color: var(--p-blue); font-weight: 600; text-decoration: none;">Gérer</a>
          </div>
          <table class="table-mini" style="width: 100%;">
            <thead><tr><th>Titre</th><th>Statut</th></tr></thead>
            <tbody>
              <?php foreach(array_slice($plannings, 0, 5) as $p): ?>
              <tr>
                <td><?= htmlspecialchars($p->getTitrePlanning()); ?></td>
                <td><span class="badge-pill badge-warning">En attente</span></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- New Members -->
        <div class="glass-card">
          <h3 class="chart-title" style="margin-bottom: 20px;">👥 Nouveaux Membres</h3>
          <div style="display: flex; flex-direction: column; gap: 16px;">
            <?php foreach($recentUsers as $user): ?>
            <div style="display: flex; align-items: center; gap: 14px;">
              <div style="width: 40px; height: 40px; border-radius: 12px; background: #f1f3f5; display: grid; place-items: center; font-weight: 800; color: var(--p-blue);">
                <?= strtoupper(substr($user['nom'] ?? 'U', 0, 1)) ?>
              </div>
              <div>
                <div style="font-size: 0.9rem; font-weight: 600;"><?= htmlspecialchars(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?></div>
                <div style="font-size: 0.75rem; color: var(--muted);"><?= htmlspecialchars($user['email']) ?></div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    </main>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      feather.replace();



      // Mini Macros Chart
      const ctxMacros = document.getElementById('miniMacrosChart').getContext('2d');
      new Chart(ctxMacros, {
        type: 'bar',
        data: {
          labels: ['Prot', 'Gluc', 'Lip'],
          datasets: [{
            data: [<?= round($avgMacros['prot'], 1) ?>, <?= round($avgMacros['gluc'], 1) ?>, <?= round($avgMacros['lip'], 1) ?>],
            backgroundColor: ['#ff922b', '#21b66f', '#339af0'],
            borderRadius: 6,
            barThickness: 20
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: {
            y: { display: false },
            x: { grid: { display: false } }
          }
        }
      });
    });
  </script>
</body>
</html>
