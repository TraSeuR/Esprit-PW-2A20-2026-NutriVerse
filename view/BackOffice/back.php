<?php
require_once __DIR__ . '/../../Controller/ProduitController.php';
require_once __DIR__ . '/../../Controller/NotificationController.php';

$produitController = new ProduitController();
$notifController = new NotificationController();

// --- BUSINESS STATS ---
$db = config::getConnexion();
$totalOrders = $db->query("SELECT COUNT(*) FROM commande")->fetchColumn();
$totalRevenue = $db->query("SELECT SUM(montant_total) FROM commande")->fetchColumn() ?: 0;
$totalProducts = $db->query("SELECT COUNT(*) FROM produit")->fetchColumn();
$totalUsers = $db->query("SELECT COUNT(*) FROM user")->fetchColumn();

// Recent Activity
$recentOrders = $db->query("SELECT * FROM commande ORDER BY date_commande DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
$recentUsers = $db->query("SELECT * FROM user ORDER BY id_user DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Chart Data (Monthly Growth)
$stmtChart = $db->query("SELECT MONTH(date_commande) as mois, COUNT(*) as nb FROM commande WHERE YEAR(date_commande) = YEAR(CURDATE()) GROUP BY MONTH(date_commande)");
$chartData = array_fill(1, 12, 0);
while ($row = $stmtChart->fetch(PDO::FETCH_ASSOC)) {
    $chartData[(int)$row['mois']] = (int)$row['nb'];
}
$chartJson = json_encode(array_values($chartData));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriVerse - Dashboard Administration</title>
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

    .charts-row { display: grid; grid-template-columns: 1.6fr 1fr; gap: 24px; margin-bottom: 34px; }
    .chart-box { padding: 24px; }
    .chart-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 20px; }
    
    .activity-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; }
    .table-mini th { text-align: left; font-size: 0.75rem; color: var(--muted); text-transform: uppercase; padding: 12px; }
    .table-mini td { padding: 12px; font-size: 0.9rem; border-top: 1px solid #f1f3f5; }
    .badge-pill { padding: 4px 10px; border-radius: 99px; font-size: 0.7rem; font-weight: 700; }
    .badge-success { background: #eaf8ef; color: #21b66f; }

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
          <span class="section-badge">Administration</span>
          <h1>Dashboard NutriVerse</h1>
          <p>Vue d’ensemble de votre plateforme santé, nutrition et commerce.</p>
        </div>
        <button class="export-btn" style="background: var(--green); color: white; border: none; border-radius: 12px; padding: 10px 20px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;">
          <i data-feather="download"></i> Exporter Rapport
        </button>
      </section>

      <!-- Stats Grid -->
      <section class="main-stats-grid fade-up">
        <div class="glass-card">
          <div class="stat-header">
            <span class="stat-label">Utilisateurs</span>
            <div class="stat-icon-wrap icon-blue"><i data-feather="users"></i></div>
          </div>
          <h2 class="stat-val"><?= $totalUsers ?></h2>
          <p style="font-size: 0.75rem; color: var(--muted); margin-top: 8px;">Total inscrits</p>
        </div>
        <div class="glass-card">
          <div class="stat-header">
            <span class="stat-label">Revenu Total</span>
            <div class="stat-icon-wrap icon-green"><i data-feather="dollar-sign"></i></div>
          </div>
          <h2 class="stat-val"><?= number_format($totalRevenue, 2) ?> <small style="font-size: 0.9rem;">DT</small></h2>
          <p style="font-size: 0.75rem; color: var(--muted); margin-top: 8px;">Ventes shop</p>
        </div>
        <div class="glass-card">
          <div class="stat-header">
            <span class="stat-label">Produits</span>
            <div class="stat-icon-wrap icon-orange"><i data-feather="package"></i></div>
          </div>
          <h2 class="stat-val"><?= $totalProducts ?></h2>
          <p style="font-size: 0.75rem; color: var(--muted); margin-top: 8px;">Articles en catalogue</p>
        </div>
        <div class="glass-card">
          <div class="stat-header">
            <span class="stat-label">Commandes</span>
            <div class="stat-icon-wrap icon-purple"><i data-feather="shopping-bag"></i></div>
          </div>
          <h2 class="stat-val"><?= $totalOrders ?></h2>
          <p style="font-size: 0.75rem; color: var(--muted); margin-top: 8px;">Transactions totales</p>
        </div>
      </section>

      <!-- Charts -->
      <section class="charts-row fade-up">
        <div class="glass-card chart-box">
          <h3 class="chart-title">Croissance des Ventes</h3>
          <div style="height: 300px;"><canvas id="ordersChart"></canvas></div>
        </div>
        <div class="glass-card" style="display:flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;">
          <div class="stat-icon-wrap icon-green" style="width: 80px; height: 80px; margin-bottom: 20px;"><i data-feather="trending-up" style="width: 40px; height: 40px;"></i></div>
          <h3 style="margin-bottom: 10px;">Activité Positive</h3>
          <p style="color: var(--muted); max-width: 200px;">Votre plateforme connaît une croissance stable ce mois-ci.</p>
        </div>
      </section>

      <!-- Bottom Grid -->
      <section class="activity-grid fade-up">
        <div class="glass-card">
          <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 class="chart-title" style="margin:0;">📦 Commandes Récentes</h3>
            <a href="/integ/view/BackOffice/commande/commandes.php" style="font-size: 0.8rem; color: var(--p-blue); font-weight: 600;">Tout voir</a>
          </div>
          <table class="table-mini" style="width: 100%;">
            <thead><tr><th>Client</th><th>Total</th><th>Statut</th></tr></thead>
            <tbody>
              <?php foreach($recentOrders as $order): ?>
              <tr>
                <td style="font-weight: 500;"><?= htmlspecialchars($order['nom_client']) ?></td>
                <td style="font-weight: 700; color: var(--p-green);"><?= number_format($order['montant_total'], 2) ?> DT</td>
                <td><span class="badge-pill badge-success"><?= $order['statut_commande'] ?></span></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

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

      const ctxOrders = document.getElementById('ordersChart').getContext('2d');
      const gradient = ctxOrders.createLinearGradient(0, 0, 0, 300);
      gradient.addColorStop(0, 'rgba(33, 182, 111, 0.2)');
      gradient.addColorStop(1, 'rgba(33, 182, 111, 0)');

      new Chart(ctxOrders, {
        type: 'line',
        data: {
          labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
          datasets: [{
            label: 'Commandes',
            data: <?= $chartJson ?>,
            borderColor: '#21b66f',
            borderWidth: 3,
            backgroundColor: gradient,
            tension: 0.4,
            fill: true,
            pointRadius: 4,
            pointBackgroundColor: '#fff',
            pointBorderWidth: 2
          }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: '#f1f3f5' } }, x: { grid: { display: false } } } }
      });
    });
  </script>
</body>
</html>
