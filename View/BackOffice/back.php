<?php
// Admin guard: checks session + role + sends no-cache headers
require_once __DIR__ . "/../../Controller/auth_check_admin.php";
require_once __DIR__ . "/../../Controller/csrf.php";

// Flash messages
$flashSuccess = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$flashErrors = $_SESSION['back_errors'] ?? [];
unset($_SESSION['back_errors']);


require_once __DIR__ . '/../../controller/RegimeC.php';
require_once __DIR__ . '/../../controller/PlanningC.php';
require_once __DIR__ . '/../../controller/recetteC.php';

$rCtrl = new RegimeC();
$pCtrl = new PlanningC();

$regimes = $rCtrl->listRegimes();
$plannings = $pCtrl->listPlannings();
$recetteC = new recetteC();

// Stats simples
$totalRegimes = count($regimes);
$totalPlannings = count($plannings);
$totalRecettesCount = $recetteC->countRecettes();

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
  if ($p->getStatut() === 'accepte')
    $acceptedCount++;
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
  <link rel="stylesheet" href="assets/css/back.css" />
  <link rel="stylesheet" href="../FrontOffice/assets/css/userbox.css" />

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet" />

  <!-- Avatar animation (same as register.php) -->
  <style>
    input[type="radio"]:checked+img.avatar-option {
      transform: scale(1.15);
    }

    img.avatar-option:hover {
      transform: scale(1.1);
      cursor: pointer;
    }
  </style>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- Feather Icons -->
  <script src="https://unpkg.com/feather-icons"></script>
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

      <button class="close-sidebar" id="closeSidebar">X</button>
    </div>

    <nav class="sidebar-menu">
      <a href="back.php" class="menu-item active" data-section="dashboard">
        <i data-feather="grid"></i>
        <span>Dashboard</span>
      </a>

      <a href="RECETTE/admin.php" class="menu-item">
        <i data-feather="book-open"></i>
        <span>Recettes</span>
      </a>

      <a href="utilisateur/admin_utilisateurs.php" class="menu-item">
        <i data-feather="users"></i>
        <span>Utilisateurs</span>
      </a>

      <a href="#" class="menu-item">
        <i data-feather="package"></i>
        <span>Produits</span>
      </a>

      <a href="#" class="menu-item">
        <i data-feather="shopping-cart"></i>
        <span>Commandes</span>
      </a>

      <a href="#" class="menu-item">
        <i data-feather="activity"></i>
        <span>Suivi Santé</span>
      </a>

      <a href="programme/admin_dashboard.php" class="menu-item">
        <i data-feather="heart"></i>
        <span>Programmes</span>
      </a>

      <a href="#" class="menu-item">
        <i data-feather="settings"></i>
        <span>Paraméres</span>
      </a>
    </nav>

    <div class="sidebar-footer">
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
        <button class="icon-btn notification-btn">
          <i data-feather="bell"></i>
          <span class="notif-dot"></span>
        </button>

        <div class="user-menu admin-box">
          <button class="user-btn" id="userMenuBtn"
            style="background-color: transparent; color: #333; gap: 10px; padding: 5px;">
            <div class="admin-avatar"><?= strtoupper(substr($_SESSION['prenom'] ?? 'A', 0, 1)) ?></div>
            <div style="text-align: left; display: flex; flex-direction: column;">
              <h4 style="margin:0; font-size: 0.95rem;">
                <?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?>
              </h4>
              <p style="margin:0; font-size: 0.8rem; color: #777;">Administrateur</p>
            </div>
            <span>▼</span>
          </button>

          <div class="user-dropdown" id="userDropdown" style="top: 100%; right: 0;">
            <a href="../FrontOffice/utilisateur/logout.php" class="logout"><i data-feather="log-out"
                style="width: 16px;"></i>
              Déonnexion</a>
          </div>
        </div>
      </div>
    </header>

    <!-- DASHBOARD CONTENT -->
    <!-- -------------------------------------------
         SECTION: DASHBOARD (section par déaut)
    -------------------------------------------- -->
    <main class="dashboard-content" id="section-dashboard">

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
           STATS PLACEHOLDER
      ========================== -->
      <section class="stats-grid">

        <div class="stat-card">
          <div class="stat-top">
            <div>
              <p class="stat-title">Régimes</p>
              <h2><?php echo $totalRegimes; ?></h2>
            </div>
            <div class="stat-icon green">
              <i data-feather="heart"></i>
            </div>
          </div>
          <p class="stat-subtitle">Solutions nutritionnelles</p>
        </div>

        <div class="stat-card">
          <div class="stat-top">
            <div>
              <p class="stat-title">Recettes</p>
              <h2><?php echo $totalRecettesCount; ?></h2>
            </div>
            <div class="stat-icon orange">
              <i data-feather="book-open"></i>
            </div>
          </div>
          <p class="stat-subtitle">Recettes saines</p>
        </div>

        <div class="stat-card">
          <div class="stat-top">
            <div>
              <p class="stat-title">Plannings</p>
              <h2><?php echo $totalPlannings; ?></h2>
            </div>
            <div class="stat-icon green">
              <i data-feather="calendar"></i>
            </div>
          </div>
          <p class="stat-subtitle">Programmes actifs</p>
        </div>

        <div class="stat-card">
          <div class="stat-top">
            <div>
              <p class="stat-title">Validation</p>
              <h2><?php echo $validationRate; ?>%</h2>
            </div>
            <div class="stat-icon orange">
              <i data-feather="check-circle"></i>
            </div>
          </div>
          <p class="stat-subtitle">Taux d'approbation</p>
        </div>

      </section>

      <!-- =========================
           CHARTS PLACEHOLDERS
      ========================== -->
      <section class="charts-section">

        <!-- Bloc 1 : RÉGIMES (RESTORED) -->
        <div class="chart-card">
          <div class="chart-header">
            <div>
              <h3>Répartition des Objectifs</h3>
              <p>Analyse des types de programmes gérés</p>
            </div>
          </div>

          <div style="padding: 20px; height: 300px; display: flex; justify-content: center;">
            <canvas id="regimeDistributionChart"></canvas>
          </div>
        </div>

        <!-- Bloc 2 : MACROS -->
        <div class="chart-card">
          <div class="chart-header">
            <div>
              <h3>Équilibre Macronutriments</h3>
              <p>Moyenne des apports (g) sur tous les régimes</p>
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
      <section class="bottom-grid">
        <?php
        // Comptage des recettes par catégorie pour le nouveau bloc
        $recettesAll = $recetteC->listeRecette();
        $catStats = ['Cuisine Durable' => 0, 'Healthy' => 0, 'Vegan' => 0];
        foreach ($recettesAll as $ra) {
          if (isset($catStats[$ra['categorie']])) {
            $catStats[$ra['categorie']]++;
          }
        }
        ?>

        <!-- Bloc RECETTES (REPLACING THE TABLE) -->
        <div class="chart-card">
          <div class="chart-header">
            <div>
              <h3>Répartition des Recettes</h3>
              <p>Catégories principales les plus populaires</p>
            </div>
          </div>

          <div style="padding: 20px; height: 300px; display: flex; justify-content: center;">
            <canvas id="recipeCategoryChart"></canvas>
          </div>
        </div>

        <!-- Dernières Demandes de Programmes -->
        <div class="table-card">
          <div class="card-header">
            <div>
              <h3>Demandes Récentes</h3>
              <p>Plannings en attente</p>
            </div>
            <a href="programme/admin_dashboard.php" class="mini-btn">Gérer</a>
          </div>

          <div class="table-wrapper">
            <table>
              <thead>
                <tr>
                  <th>Titre</th>
                  <th>Statut</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $countP = 0;
                foreach ($plannings as $p) {
                  if ($countP >= 5)
                    break;
                  ?>
                  <tr>
                    <td><span style="font-weight: 500;"><?php echo htmlspecialchars($p->getTitrePlanning()); ?></span>
                    </td>
                    <td>
                      <span class="status <?php echo ($p->getStatut() == 'accepte') ? 'delivered' : 'pending'; ?>">
                        <?php echo ($p->getStatut() == 'accepte') ? 'Accepté' : 'En attente'; ?>
                      </span>
                    </td>
                    <td><a href="programme/admin_dashboard.php" class="view-link">Détails</a></td>
                  </tr>
                  <?php
                  $countP++;
                } ?>
              </tbody>
            </table>
          </div>
        </div>

      </section>


    </main><!-- /#section-dashboard -->

  </div><!-- /.main-content -->

  <!-- JS -->
  <script src="assets/js/back.js"></script>
  <script src="assets/js/back.validate.js"></script>
  <script>
    // Prevent form resubmission on refresh
    if (window.history.replaceState) {
      window.history.replaceState(null, null, window.location.href);
    }

    // Password tools for Back Office
    function togglePassword(inputId, btn) {
      const input = document.getElementById(inputId);
      if (input.type === 'password') {
        input.type = 'text';
        btn.textContent = '🔒';
      } else {
        input.type = 'password';
        btn.textContent = '👁️';
      }
    }

    function generatePassword(inputId) {
      const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+";
      let pass = "";
      for (let i = 0; i < 12; i++) {
        pass += chars.charAt(Math.floor(Math.random() * chars.length));
      }
      const input = document.getElementById(inputId);
      input.value = pass;
      input.type = 'text'; // Show it so they can see it
      // Trigger validation
      if (typeof validateInput === 'function') validateInput(input);
    }
  </script>
  <script src="../FrontOffice/assets/js/userbox.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Animation Feather Icons
      feather.replace();

      // Chart 1 : Répartition des régimes (RESTORED)
      const ctx = document.getElementById('regimeDistributionChart').getContext('2d');
      new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: ['Perte de poids', 'Prise de masse', 'Équilibre'],
          datasets: [{
            data: [
              <?php echo $typesCount['perte_poids']; ?>,
              <?php echo $typesCount['prise_masse']; ?>,
              <?php echo $typesCount['equilibre']; ?>
            ],
            backgroundColor: ['#59b84d', '#ff9f43', '#a8dba0'],
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
    });
  </script>
</body>

</html>