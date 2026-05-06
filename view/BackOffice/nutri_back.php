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
$movementCount = $movementController->getMovementCount();

// Fetch products under alert threshold
$lowStockProduits = [];
$allProduits = $produitController->getProduits();
foreach($allProduits as $p) {
    if($p['quantite_stock'] <= $p['seuil_alerte']) {
        $lowStockProduits[] = $p;
    }
}

// Fetch recent movements
$recentMovements = array_slice(array_reverse($movementController->getMovements()), 0, 5);

$regimes = $rCtrl->listRegimes();
$plannings = $pCtrl->listPlannings();
$totalRegimes = count($regimes);
$totalPlannings = count($plannings);
$totalRecettesCount = $recetteC->countRecettes();

// --- NEW GLOBAL STATS (From back.php/AdminController) ---
$db = config::getConnexion();
$totalOrders = $db->query("SELECT COUNT(*) FROM commande")->fetchColumn();
$totalRevenue = $db->query("SELECT SUM(montant_total) FROM commande")->fetchColumn() ?: 0;
$totalProducts = $db->query("SELECT COUNT(*) FROM produit")->fetchColumn();
$totalUsers = $db->query("SELECT COUNT(*) FROM user")->fetchColumn();

// Recent Orders
$stmtRecent = $db->query("SELECT * FROM commande ORDER BY date_commande DESC LIMIT 5");
$recentOrders = $stmtRecent->fetchAll(PDO::FETCH_ASSOC);

// Nouveaux Membres
$stmtUsers = $db->query("SELECT * FROM user ORDER BY id_user DESC LIMIT 5");
$recentUsers = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

// Données pour le Graphique Mensuel
$stmtChart = $db->query("SELECT MONTH(date_commande) as mois, COUNT(*) as nb FROM commande WHERE YEAR(date_commande) = YEAR(CURDATE()) GROUP BY MONTH(date_commande)");
$chartData = array_fill(1, 12, 0);
while ($row = $stmtChart->fetch(PDO::FETCH_ASSOC)) {
    $chartData[(int)$row['mois']] = (int)$row['nb'];
}
$chartJson = json_encode(array_values($chartData));
// ---------------------------------------------------------

// Comptage par type de régime
$typesCount = ['perte_poids' => 0, 'prise_masse' => 0, 'equilibre' => 0];
foreach ($regimes as $r) {
    if (isset($typesCount[$r->getType()])) {
        $typesCount[$r->getType()]++;
    }
}

// Comptage des plannings en attente
$pendingCount = 0;
foreach ($plannings as $p) {
    if ($p->getStatut() === 'en_attente') {
        $pendingCount++;
    }
}

// Stats Macronutriments moyennes
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

// Taux de validation
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
  <title>NutriVerse - Dashboard Back Office</title>

  <!-- CSS -->
  <link rel="stylesheet" href="assets/back.css" />

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet"
  />

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- Feather Icons -->
  <script src="https://unpkg.com/feather-icons"></script>

  <style>
    /* Notification Dropdown Styles (Integrated) */
    .notif-dropdown {
      position: absolute; top: 100%; right: 0; width: 350px; max-height: 450px;
      background: #fff; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.15);
      z-index: 10000; overflow-y: auto; display: none; border: 1px solid #eee;
    }
    .notif-dropdown.show { display: block; animation: slideDown 0.3s ease; }
    @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    .notif-item { padding: 12px 15px; border-bottom: 1px solid #f0f0f0; font-size: 13px; transition: background 0.2s; text-align: left;}
    .notif-item:hover { background: #f9f9f9; }
    .notif-item.unread { background: #f0f7ff; border-left: 3px solid #3498db; }
    .notif-item-header { display: flex; justify-content: space-between; margin-bottom: 4px; color: #888; font-size: 11px; }
    .notif-item-msg { color: #333; line-height: 1.4; font-weight: 500; }
    .notif-footer { padding: 10px; text-align: center; background: #fafafa; }
    .notif-footer a { font-size: 12px; color: #3498db; font-weight: 600; text-decoration: none; }
    .notif-badge-ui { 
        position: absolute; top: -5px; right: -5px; background: #e74c3c; color: white; 
        border-radius: 50%; width: 18px; height: 18px; font-size: 10px; font-weight: bold;
        display: flex; align-items: center; justify-content: center; border: 2px solid #fff;
    }
  </style>
</head>
<body>

  <!-- =========================
       SIDEBAR
  ========================== -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-top">
      <div class="brand">
        <img src="images/logo.png" alt="Logo NutriVerse" class="brand-logo" />
        <div>
          <h2>NutriVerse</h2>
          <p>Back Office</p>
        </div>
      </div>

      <button class="close-sidebar" id="closeSidebar">✕</button>
    </div>

    <nav class="sidebar-menu">
      <a href="nutri_back.php" class="menu-item active">
        <i data-feather="grid"></i>
        <span>Dashboard</span>
      </a>

      <a href="RECETTE/admin.php" class="menu-item">
        <i data-feather="book-open"></i>
        <span>Recettes</span>
      </a>

      <a href="../../shop.php?action=admin_users" class="menu-item">
        <i data-feather="users"></i>
        <span>Utilisateurs</span>
      </a>

      <a href="produit/listProduit.php" class="menu-item">
        <i data-feather="package"></i>
        <span>Produits</span>
      </a>

      <a href="movement/listMovement.php" class="menu-item">
        <i data-feather="activity"></i>
        <span>Mouvements Stock</span>
      </a>

      <a href="notifications/listNotifications.php" class="menu-item">
        <i data-feather="bell"></i>
        <span>Notifications</span>
        <?php if(isset($unreadCount) && $unreadCount > 0): ?>
            <span style="background: #e74c3c; color: white; border-radius: 10px; padding: 2px 8px; font-size: 10px; margin-left: auto;"><?= $unreadCount ?></span>
        <?php endif; ?>
      </a>

      <a href="../../shop.php?action=admin_orders" class="menu-item">
        <i data-feather="shopping-cart"></i>
        <span>Commandes</span>
      </a>

      <a href="../../shop.php?action=admin_livraisons" class="menu-item">
        <i data-feather="truck"></i>
        <span>Livraisons</span>
      </a>

      <a href="#" class="menu-item">
        <i data-feather="heart-pulse"></i>
        <span>Suivi Santé</span>
      </a>

      <a href="programme/admin_dashboard.php" class="menu-item">
        <i data-feather="heart"></i>
        <span>Programmes</span>
      </a>
    </nav>

    <div class="sidebar-footer">
      <a href="../FrontOffice/nutri_front.php" class="menu-item" style="padding: 10px 0; font-size: 0.85rem; opacity: 0.7;">
        <i data-feather="log-out" style="width: 16px;"></i>
        <span>Quitter l'admin</span>
      </a>
      <p style="margin-top: 10px;">© 2026 NutriVerse</p>
    </div>
  </aside>

  <!-- =========================
       MAIN CONTENT
  ========================== -->
  <div class="main-content">

    <!-- TOPBAR -->
    <header class="topbar">
      <div class="topbar-left">
        <button class="menu-btn" id="menuBtn">
          <i data-feather="menu"></i>
        </button>

        <div class="search-box">
          <i data-feather="search"></i>
          <input type="text" placeholder="Rechercher..." />
        </div>
      </div>

      <div class="topbar-right">
        <div style="position: relative;">
            <button class="icon-btn notification-btn" onclick="toggleNotifs()">
              <i data-feather="bell"></i>
              <?php if($unreadCount > 0): ?>
                  <span class="notif-badge-ui"><?= $unreadCount ?></span>
              <?php endif; ?>
            </button>
            <div id="notif-dropdown" class="notif-dropdown">
                <div style="padding: 15px; border-bottom: 1px solid #eee; font-weight: 600; display:flex; justify-content: space-between; color: #333;">
                    Notifications
                    <span style="font-size: 11px; color: #3498db; cursor: pointer;" onclick="markAllRead()">Tout marquer lu</span>
                </div>
                <div id="notif-content">
                    <p style="padding: 20px; text-align: center; color: #888;">Chargement...</p>
                </div>
                <div class="notif-footer" id="notif-footer">
                    <a href="notifications/listNotifications.php">Voir tout l'historique</a>
                </div>
            </div>
        </div>

        <div class="admin-box">
          <div class="admin-avatar">A</div>
          <div>
            <h4>Admin</h4>
            <p>Administrateur</p>
          </div>
        </div>
      </div>
    </header>

    <!-- DASHBOARD CONTENT -->
    <main class="dashboard-content">

      <!-- PAGE HEADER -->
      <section class="page-header fade-up">
        <div>
          <span class="section-badge">Vue globale</span>
          <h1>Dashboard NutriVerse</h1>
          <p>
            Vue d’ensemble intelligente de votre plateforme santé, nutrition et commandes.
          </p>
        </div>

        <button class="export-btn">
          <i data-feather="download"></i>
          Exporter le rapport
        </button>
      </section>

      <!-- =========================
           STATS
      ========================== -->
      <section class="stats-grid">
        <div class="stat-card">
          <div class="stat-top">
            <div>
              <p class="stat-title">Utilisateurs</p>
              <h2><?= $totalUsers ?></h2>
            </div>
            <div class="stat-icon green">
                <i data-feather="users"></i>
            </div>
          </div>
          <p class="stat-subtitle">Total inscrits</p>
        </div>

        <div class="stat-card">
          <div class="stat-top">
            <div>
              <p class="stat-title">Revenu Total</p>
              <h2 style="font-size: 1.4rem;"><?= number_format($totalRevenue, 2) ?> DT</h2>
            </div>
            <div class="stat-icon orange">
                <i data-feather="dollar-sign"></i>
            </div>
          </div>
          <p class="stat-subtitle">Chiffre d'affaires</p>
        </div>

        <div class="stat-card">
          <div class="stat-top">
            <div>
              <p class="stat-title">Commandes</p>
              <h2><?= $totalOrders ?></h2>
            </div>
            <div class="stat-icon blue">
                <i data-feather="shopping-cart"></i>
            </div>
          </div>
          <p class="stat-subtitle">Ventes globales</p>
        </div>

        <div class="stat-card">
          <div class="stat-top">
            <div>
              <p class="stat-title">Recettes</p>
              <h2><?= $totalRecettesCount ?></h2>
            </div>
            <div class="stat-icon orange">
                <i data-feather="book-open"></i>
            </div>
          </div>
          <p class="stat-subtitle">Contenu nutritionnel</p>
        </div>

        <div class="stat-card">
          <div class="stat-top">
            <div>
              <p class="stat-title">Stock Alertes</p>
              <h2><?= count($lowStockProduits) ?></h2>
            </div>
            <div class="stat-icon red">
                <i data-feather="alert-triangle"></i>
            </div>
          </div>
          <p class="stat-subtitle">Produits sous seuil</p>
        </div>

        <div class="stat-card">
          <div class="stat-top">
            <div>
              <p class="stat-title">Validation</p>
              <h2><?= $validationRate ?>%</h2>
            </div>
            <div class="stat-icon green">
                <i data-feather="check-circle"></i>
            </div>
          </div>
          <p class="stat-subtitle">Taux d'approbation</p>
        </div>

      <!-- =========================
           CHARTS PLACEHOLDERS
      ========================== -->
      <section class="charts-section">

        <!-- Croissance Mensuelle (Restored from back.php) -->
        <div class="chart-card">
          <div class="chart-header">
            <div>
              <h3>Croissance Mensuelle</h3>
              <p>Évolution de l’activité (Commandes)</p>
            </div>
            <button class="mini-btn">Annuel</button>
          </div>
          <div style="padding: 20px; height: 300px;">
            <canvas id="ordersChart"></canvas>
          </div>
        </div>

        <!-- Équilibre Macronutriments -->
        <div class="chart-card">
          <div class="chart-header">
            <div>
              <h3>Équilibre Macronutriments</h3>
              <p>Moyenne des apports (g) sur les régimes</p>
            </div>
          </div>
          <div style="padding: 20px; height: 300px;">
            <canvas id="macroNutrientChart"></canvas>
          </div>
        </div>

      </section>

      <!-- =========================
           TABLES / LISTES 
      ========================== -->
      <!-- =========================
           TABLES & LISTS (UNIFIED)
      ========================== -->
      <section class="bottom-grid">
        
        <!-- Commandes Récentes (Restored) -->
        <div class="table-card">
          <div class="card-header">
            <div>
              <h3>📦 Commandes Récentes</h3>
              <p>Dernières transactions shop</p>
            </div>
            <a href="../../shop.php?action=admin_orders" class="mini-btn">Voir tout</a>
          </div>
          <div class="table-wrapper">
            <table>
              <thead>
                <tr>
                  <th>Client</th>
                  <th>Total</th>
                  <th>Statut</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($recentOrders as $order): ?>
                <tr>
                  <td><?= htmlspecialchars($order['nom_client']) ?></td>
                  <td style="font-weight: 600; color: #59b84d;"><?= number_format($order['montant_total'], 2) ?> DT</td>
                  <td><span class="status delivered"><?= $order['statut_commande'] ?></span></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Nouveaux Membres (Restored) -->
        <div class="table-card">
          <div class="card-header">
            <div>
              <h3>👥 Nouveaux Membres</h3>
              <p>Dernières inscriptions</p>
            </div>
          </div>
          <div class="table-wrapper" style="padding: 10px 20px;">
            <?php foreach($recentUsers as $user): ?>
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #f9f9f9;">
              <div style="width: 36px; height: 36px; border-radius: 8px; background: #59b84d; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">
                <?= strtoupper(substr($user['nom'] ?? 'U', 0, 1)) ?>
              </div>
              <div style="flex: 1;">
                <h5 style="margin: 0; font-size: 0.9rem;"><?= htmlspecialchars($user['nom'] ?? 'Anonyme') ?></h5>
                <p style="margin: 0; font-size: 0.75rem; color: #888;"><?= htmlspecialchars($user['email'] ?? '') ?></p>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Demandes de Programmes -->
        <div class="table-card">
          <div class="card-header">
            <div>
              <h3>📅 Plannings Récents</h3>
              <p>Demandes en attente</p>
            </div>
            <a href="programme/admin_dashboard.php" class="mini-btn">Gérer</a>
          </div>
          <div class="table-wrapper">
            <table>
              <thead>
                <tr>
                  <th>Titre</th>
                  <th>Statut</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                $countP = 0;
                foreach($plannings as $p) {
                  if($countP >= 5) break;
                  ?>
                  <tr>
                    <td><?= htmlspecialchars($p->getTitrePlanning()); ?></td>
                    <td><span class="status pending">En attente</span></td>
                  </tr>
                  <?php $countP++; } ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Produits Sous Stock -->
        <div class="table-card">
          <div class="card-header">
            <div>
              <h3>⚠️ Alertes Stock</h3>
              <p>Réapprovisionnement</p>
            </div>
            <a href="produit/listProduit.php" class="mini-btn">Voir</a>
          </div>
          <div class="table-wrapper">
            <table>
                <tbody>
                    <?php foreach(array_slice($lowStockProduits, 0, 5) as $lp): ?>
                    <tr>
                        <td><?= htmlspecialchars($lp['nom']) ?></td>
                        <td><span style="color: #e74c3c; font-weight: bold;"><?= $lp['quantite_stock'] ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
          </div>
        </div>

      </section>


    </main>
  </div>

  <!-- JS -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Animation Feather Icons
      feather.replace();

      // Chart 1 : Croissance Mensuelle (Restored)
      const ctxOrders = document.getElementById('ordersChart').getContext('2d');
      new Chart(ctxOrders, {
        type: 'line',
        data: {
          labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
          datasets: [{
            label: 'Commandes',
            data: <?= $chartJson ?>,
            borderColor: '#59b84d',
            backgroundColor: 'rgba(89, 184, 77, 0.1)',
            tension: 0.4,
            fill: true
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: {
            y: { beginAtZero: true, grid: { display: false } },
            x: { grid: { display: false } }
          }
        }
      });

      // Chart 2 : Recettes par Catégorie (Optional but kept for completeness)
      // (Removing the old Chart 1 logic since we replaced it with Orders Chart)


      // Chart 2 : Recettes par Catégorie (NEW)
      const ctxRecipe = document.getElementById('recipeCategoryChart').getContext('2d');
      new Chart(ctxRecipe, {
        type: 'doughnut',
        data: {
          labels: ['Cuisine Durable', 'Healthy', 'Vegan'],
          datasets: [{
            data: [
              <?php echo $catStats['Cuisine Durable']; ?>,
              <?php echo $catStats['Healthy']; ?>,
              <?php echo $catStats['Vegan']; ?>
            ],
            backgroundColor: ['#59b84d', '#ff9f43', '#2196f3'],
            borderWidth: 0,
            hoverOffset: 10
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                padding: 20,
                font: { family: 'Poppins', size: 12 }
              }
            }
          },
          cutout: '70%'
        }
      });

      // Chart 3 : Macronutriments
      const ctx2 = document.getElementById('macroNutrientChart').getContext('2d');
      new Chart(ctx2, {
        type: 'bar',
        data: {
          labels: ['Protéines', 'Glucides', 'Lipides'],
          datasets: [{
            label: 'Grammes (moyenne)',
            data: [
              <?php echo round($avgMacros['prot'], 1); ?>,
              <?php echo round($avgMacros['gluc'], 1); ?>,
              <?php echo round($avgMacros['lip'], 1); ?>
            ],
            backgroundColor: ['#ff9f43', '#59b84d', '#a8dba0'],
            borderRadius: 8
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: { beginAtZero: true, grid: { display: false } },
            x: { grid: { display: false } }
          },
          plugins: {
            legend: { display: false }
          }
        }
      });

      // Notification Logic
      window.toggleNotifs = function() {
          const dropdown = document.getElementById('notif-dropdown');
          const isOpen = dropdown.classList.contains('show');
          dropdown.classList.toggle('show');
          if (!isOpen) { loadNotifications(); }
      }

      function loadNotifications() {
          fetch('produit/ajax_get_notifications.php').then(r => r.text()).then(html => {
              document.getElementById('notif-content').innerHTML = html;
          });
      }

      window.markAllRead = function() {
          fetch('produit/ajax_get_notifications.php?action=read_all').then(() => {
              loadNotifications();
              const badge = document.querySelector('.notif-badge-ui');
              if (badge) badge.remove();
          });
      }

      window.addEventListener('click', function(e) {
          if (!e.target.closest('.notification-btn') && !e.target.closest('.notif-dropdown')) {
              const dropdown = document.getElementById('notif-dropdown');
              if(dropdown) dropdown.classList.remove('show');
          }
      });
    });
  </script>
</body>
</html>
