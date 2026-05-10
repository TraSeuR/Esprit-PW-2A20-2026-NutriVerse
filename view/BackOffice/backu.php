<?php
require_once __DIR__ . "/../../Controller/auth_check_admin.php";
require_once __DIR__ . "/../../Controller/csrf.php";

require_once __DIR__ . '/../../controller/RegimeC.php';
require_once __DIR__ . '/../../controller/PlanningC.php';
require_once __DIR__ . '/../../controller/recetteC.php';

$rCtrl = new RegimeC();
$pCtrl = new PlanningC();
$recetteC = new recetteC();

$regimes = $rCtrl->listRegimes();
$plannings = $pCtrl->listPlannings();
$totalRegimes = count($regimes);
$totalPlannings = count($plannings);
$totalRecettesCount = $recetteC->countRecettes();

// Type counts
$typesCount = ['perte_poids' => 0, 'prise_masse' => 0, 'equilibre' => 0];
foreach ($regimes as $r) {
    if (isset($typesCount[$r->getType()])) $typesCount[$r->getType()]++;
}

// Macros
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

// Validation rate
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
  <title>NutriVerse - Dashboard Nutrition</title>
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
    .stat-val { font-size: 2.2rem; font-weight: 800; color: var(--text); margin: 0; }
    .stat-label { color: var(--muted); font-size: 0.9rem; font-weight: 500; }
    .stat-icon-wrap { width: 48px; height: 48px; border-radius: 14px; display: grid; place-items: center; }
    .icon-green { background: #eaf8ef; color: var(--p-green); }
    .icon-blue { background: #e7f5ff; color: var(--p-blue); }
    .icon-orange { background: #fff4e6; color: var(--p-orange); }
    .icon-purple { background: #f3f0ff; color: var(--p-purple); }

    .charts-row { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 34px; }
    .chart-box { padding: 24px; }
    .chart-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 20px; }

    .activity-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 24px; }
    .table-mini th { text-align: left; font-size: 0.75rem; color: var(--muted); text-transform: uppercase; padding: 12px; }
    .table-mini td { padding: 12px; font-size: 0.9rem; border-top: 1px solid #f1f3f5; }
    .badge-pill { padding: 4px 10px; border-radius: 99px; font-size: 0.7rem; font-weight: 700; }
    .badge-success { background: #eaf8ef; color: #21b66f; }
    .badge-warning { background: #fff4e6; color: #ff922b; }

    .fade-up { animation: fadeUp 0.6s ease forwards; opacity: 0; transform: translateY(20px); }
    @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }
  </style>
</head>
<body>

  <?php include $_SERVER['DOCUMENT_ROOT'] . '/integ/view/BackOffice/sidebar.php'; ?>

  <div class="main-content">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/integ/view/BackOffice/topbar.php'; ?>

    <main class="dashboard-content">
      <section class="page-header fade-up">
        <div>
          <span class="section-badge">Nutrition & Programmes</span>
          <h1>Dashboard Nutrition</h1>
          <p>Suivez les régimes, les plannings et l'équilibre nutritionnel global.</p>
        </div>
        <button class="export-btn" style="background: var(--green); color: white; border: none; border-radius: 12px; padding: 10px 20px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;">
          <i data-feather="download"></i> Exporter Rapport
        </button>
      </section>

      <!-- Main Stats -->
      <section class="main-stats-grid fade-up">
        <div class="glass-card">
          <div class="stat-header">
            <span class="stat-label">Régimes</span>
            <div class="stat-icon-wrap icon-green"><i data-feather="heart"></i></div>
          </div>
          <h2 class="stat-val"><?= $totalRegimes ?></h2>
          <p style="font-size: 0.75rem; color: var(--muted); margin-top: 8px;">Programmes créés</p>
        </div>
        <div class="glass-card">
          <div class="stat-header">
            <span class="stat-label">Plannings</span>
            <div class="stat-icon-wrap icon-blue"><i data-feather="calendar"></i></div>
          </div>
          <h2 class="stat-val"><?= $totalPlannings ?></h2>
          <p style="font-size: 0.75rem; color: var(--muted); margin-top: 8px;">Demandes totales</p>
        </div>
        <div class="glass-card">
          <div class="stat-header">
            <span class="stat-label">Validation</span>
            <div class="stat-icon-wrap icon-orange"><i data-feather="check-circle"></i></div>
          </div>
          <h2 class="stat-val"><?= $validationRate ?>%</h2>
          <p style="font-size: 0.75rem; color: var(--muted); margin-top: 8px;">Taux d'approbation</p>
        </div>
        <div class="glass-card">
          <div class="stat-header">
            <span class="stat-label">Recettes</span>
            <div class="stat-icon-wrap icon-purple"><i data-feather="book-open"></i></div>
          </div>
          <h2 class="stat-val"><?= $totalRecettesCount ?></h2>
          <p style="font-size: 0.75rem; color: var(--muted); margin-top: 8px;">Bibliothèque saine</p>
        </div>
      </section>

      <!-- Charts -->
      <section class="charts-row fade-up">
        <div class="glass-card chart-box">
          <h3 class="chart-title">Répartition des Objectifs</h3>
          <div style="height: 300px;"><canvas id="regimeDistributionChart"></canvas></div>
        </div>
        <div class="glass-card chart-box">
          <h3 class="chart-title">Équilibre Macronutriments (Moy.)</h3>
          <div style="height: 300px;"><canvas id="macroNutrientChart"></canvas></div>
        </div>
      </section>

      <!-- Lists -->
      <section class="activity-grid fade-up">
        <div class="glass-card">
          <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 class="chart-title" style="margin:0;">📅 Demandes Récentes</h3>
            <a href="programme/admin_dashboard.php" style="font-size: 0.8rem; color: var(--p-blue); font-weight: 600;">Gérer</a>
          </div>
          <table class="table-mini" style="width: 100%;">
            <thead><tr><th>Titre</th><th>Statut</th><th>Action</th></tr></thead>
            <tbody>
              <?php foreach(array_slice($plannings, 0, 6) as $p): ?>
              <tr>
                <td style="font-weight: 500;"><?= htmlspecialchars($p->getTitrePlanning()); ?></td>
                <td><span class="badge-pill <?= $p->getStatut() == 'accepte' ? 'badge-success' : 'badge-warning' ?>"><?= $p->getStatut() == 'accepte' ? 'Accepté' : 'En attente' ?></span></td>
                <td><a href="programme/admin_dashboard.php" style="color: var(--p-blue);"><i data-feather="eye" style="width: 16px;"></i></a></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="glass-card">
          <h3 class="chart-title">Aperçu Macro</h3>
          <div style="display: flex; flex-direction: column; gap: 20px; padding-top: 10px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
              <span>Protéines</span>
              <span style="font-weight: 700; color: var(--p-orange);"><?= round($avgMacros['prot'], 1) ?>g</span>
            </div>
            <div style="width: 100%; height: 8px; background: #eee; border-radius: 4px; overflow: hidden;"><div style="width: <?= min(100, $avgMacros['prot']) ?>%; height: 100%; background: var(--p-orange);"></div></div>
            
            <div style="display: flex; justify-content: space-between; align-items: center;">
              <span>Glucides</span>
              <span style="font-weight: 700; color: var(--p-green);"><?= round($avgMacros['gluc'], 1) ?>g</span>
            </div>
            <div style="width: 100%; height: 8px; background: #eee; border-radius: 4px; overflow: hidden;"><div style="width: <?= min(100, $avgMacros['gluc']) ?>%; height: 100%; background: var(--p-green);"></div></div>

            <div style="display: flex; justify-content: space-between; align-items: center;">
              <span>Lipides</span>
              <span style="font-weight: 700; color: var(--p-blue);"><?= round($avgMacros['lip'], 1) ?>g</span>
            </div>
            <div style="width: 100%; height: 8px; background: #eee; border-radius: 4px; overflow: hidden;"><div style="width: <?= min(100, $avgMacros['lip']) ?>%; height: 100%; background: var(--p-blue);"></div></div>
          </div>
        </div>
      </section>
    </main>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      feather.replace();

      // Chart 1: Doughnut
      new Chart(document.getElementById('regimeDistributionChart'), {
        type: 'doughnut',
        data: {
          labels: ['Perte', 'Prise', 'Équilibre'],
          datasets: [{
            data: [<?= $typesCount['perte_poids'] ?>, <?= $typesCount['prise_masse'] ?>, <?= $typesCount['equilibre'] ?>],
            backgroundColor: ['#21b66f', '#ff922b', '#339af0'],
            borderWidth: 0, hoverOffset: 10
          }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { padding: 20, font: { family: 'Poppins' } } } }, cutout: '75%' }
      });

      // Chart 2: Bar
      new Chart(document.getElementById('macroNutrientChart'), {
        type: 'bar',
        data: {
          labels: ['Prot', 'Gluc', 'Lip'],
          datasets: [{
            label: 'Grammes',
            data: [<?= round($avgMacros['prot'], 1) ?>, <?= round($avgMacros['gluc'], 1) ?>, <?= round($avgMacros['lip'], 1) ?>],
            backgroundColor: ['#ff922b', '#21b66f', '#339af0'],
            borderRadius: 8
          }]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, grid: { display: false } }, x: { grid: { display: false } } }, plugins: { legend: { display: false } } }
      });
    });
  </script>
</body>
</html>