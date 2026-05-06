<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriVerse - Dashboard Back Office</title>

  <!-- CSS -->
  <link rel="stylesheet" href="view/back/assets/comb.css" />

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet" />

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- Feather Icons -->
  <script src="https://unpkg.com/feather-icons"></script>
</head>

<body>

  <div class="dashboard">

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-top">
        <div class="brand">
          <img src="images/logo.png" alt="Logo NutriVerse" class="brand-logo" onerror="this.style.display='none'">
          <div>
            <h2>NutriVerse</h2>
            <p>Back Office</p>
          </div>
        </div>
      </div>

      <nav class="sidebar-menu">
        <a href="index.php?action=admin_dashboard" class="active"><i data-feather="grid"></i> Dashboard</a>
        <a href="#"><i data-feather="book-open"></i> Recettes</a>
        <a href="#"><i data-feather="users"></i> Utilisateurs</a>
        <a href="#"><i data-feather="shopping-bag"></i> Produits</a>
        <a href="index.php?action=admin_orders"><i data-feather="shopping-cart"></i> Commandes</a>
        <a href="index.php?action=admin_livraisons"><i data-feather="truck"></i> Livraisons</a>
        <a href="#"><i data-feather="activity"></i> Suivi Santé</a>
        <a href="#"><i data-feather="heart"></i> Programmes</a>
        <a href="#"><i data-feather="settings"></i> Paramètres</a>
      </nav>

      <div class="sidebar-footer">
        <p>© 2026 NutriVerse</p>
      </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">

      <!-- TOPBAR -->
      <header class="topbar">
        <div class="topbar-left">
          <button class="menu-btn" id="menuBtn">
            <i data-feather="menu"></i>
          </button>
        </div>

        <div class="topbar-right">
          <div class="search-box">
            <i data-feather="search" style="color: var(--muted);"></i>
            <input type="text" placeholder="Rechercher une commande..." />
          </div>
          <button class="notif-btn">
            <i data-feather="bell"></i>
            <span class="notif-dot"></span>
          </button>
          <div class="admin-box">
            <div class="admin-avatar">A</div>
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
           STATS PLACEHOLDER
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
          <p class="stat-subtitle">Total des inscrits</p>
        </div>

        <div class="stat-card">
          <div class="stat-top">
            <div>
              <p class="stat-title">Revenu Total</p>
              <h2 style="font-size: 1.5rem;"><?= number_format($totalRevenue, 2) ?> DT</h2>
            </div>
            <div class="stat-icon orange">
              <i data-feather="dollar-sign"></i>
            </div>
          </div>
          <p class="stat-subtitle">Chiffre d'affaires global</p>
        </div>

        <div class="stat-card">
          <div class="stat-top">
            <div>
              <p class="stat-title">Produits</p>
              <h2><?= $totalProducts ?></h2>
            </div>
            <div class="stat-icon blue">
              <i data-feather="package"></i>
            </div>
          </div>
          <p class="stat-subtitle">Produits en catalogue</p>
        </div>

        <div class="stat-card">
          <div class="stat-top">
            <div>
              <p class="stat-title">Commandes</p>
              <h2><?= $totalOrders ?></h2>
            </div>
            <div class="stat-icon purple">
              <i data-feather="shopping-cart"></i>
            </div>
          </div>
          <p class="stat-subtitle">Total des ventes</p>
        </div>

      </section>

      <!-- =========================
           CHARTS PLACEHOLDERS
      ========================== -->
      <section class="charts-section">

        <!-- Bloc 1 -->
        <div class="chart-card placeholder-card">
          <div class="chart-header">
            <div>
              <h3>Croissance Mensuelle</h3>
              <p>Évolution de l’activité de la plateforme</p>
            </div>
            <button class="chart-badge">Mensuel</button>
          </div>

          <div class="chart-content" style="height: 250px; padding: 20px;">
            <canvas id="ordersChart"></canvas>
          </div>
          <script>
            document.addEventListener('DOMContentLoaded', function() {
              const ctx = document.getElementById('ordersChart').getContext('2d');
              new Chart(ctx, {
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
            });
          </script>
        </div>

        <!-- Bloc 2 -->
        <div class="chart-card placeholder-card">
          <div class="chart-header">
            <div>
              <h3>Répartition des Recettes</h3>
              <p>Catégories principales les plus populaires</p>
            </div>
            <button class="chart-badge">2026</button>
          </div>

          <div class="chart-placeholder">
            <div class="placeholder-icon"></div>
            <h4>Le graphique apparaîtra ici</h4>
            <p>
              Cette section affichera plus tard la répartition
              des catégories selon les recettes enregistrées.
            </p>
          </div>
        </div>

      </section>

      <!-- =========================
           TABLES / LISTES PLACEHOLDER
      ========================== -->
      <section class="bottom-section">

        <!-- Commandes Récentes -->
        <div class="table-card" style="flex: 2;">
          <div class="card-header" style="padding: 25px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border);">
            <div>
              <h3 style="margin: 0; color: var(--text);">📦 Commandes Récentes</h3>
              <p style="margin: 5px 0 0; color: var(--muted); font-size: 0.85rem;">Les 5 dernières transactions</p>
            </div>
            <a href="index.php?action=admin_orders" class="view-all" style="color: var(--green); font-weight: 600; font-size: 0.9rem;">Voir tout →</a>
          </div>

          <div class="table-wrapper" style="padding: 0 20px 20px;">
            <table style="width: 100%; border-collapse: collapse;">
              <thead>
                <tr style="text-align: left; border-bottom: 2px solid #f0f0f0; color: #6f7680;">
                  <th style="padding: 12px 0;">ID</th>
                  <th>Client</th>
                  <th>Total</th>
                  <th>Statut</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($recentOrders as $order): ?>
                <tr style="border-bottom: 1px solid #f9f9f9;">
                  <td style="padding: 12px 0; font-weight: 600;">#<?= $order['id_commande'] ?></td>
                  <td><?= htmlspecialchars($order['nom_client']) ?></td>
                  <td style="font-weight: 600; color: #59b84d;"><?= number_format($order['montant_total'], 2) ?> DT</td>
                  <td>
                    <span style="font-size: 11px; padding: 4px 10px; border-radius: 20px; background: #edf7ec; color: #3f9636; font-weight: 600;">
                      <?= $order['statut_commande'] ?>
                    </span>
                  </td>
                  <td>
                    <a href="index.php?action=admin_order_view&id=<?= $order['id_commande'] ?>" style="color: #6f7680;"><i data-feather="eye" style="width: 16px;"></i></a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Nouveaux Utilisateurs -->
        <div class="users-card" style="flex: 1; background: var(--white); border-radius: var(--radius); box-shadow: var(--shadow);">
          <div class="card-header" style="padding: 25px; border-bottom: 1px solid var(--border);">
            <h3 style="margin: 0; color: var(--text);">👥 Nouveaux Membres</h3>
            <p style="margin: 5px 0 0; color: var(--muted); font-size: 0.85rem;">Dernières inscriptions</p>
          </div>

          <div class="users-list" style="padding: 25px;">
            <?php foreach($recentUsers as $user): ?>
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--border);">
              <div style="width: 44px; height: 44px; border-radius: 12px; background: var(--green-gradient); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px; box-shadow: 0 4px 10px rgba(46, 204, 113, 0.2);">
                <?= strtoupper(substr($user['nom'] ?? 'U', 0, 1)) ?>
              </div>
              <div style="flex: 1;">
                <h5 style="margin: 0; color: var(--text); font-size: 0.95rem;"><?= htmlspecialchars($user['nom'] ?? 'Anonyme') ?></h5>
                <p style="margin: 3px 0 0; font-size: 0.8rem; color: var(--muted);"><?= htmlspecialchars($user['email'] ?? '') ?></p>
              </div>
              <div style="font-size: 0.75rem; color: var(--muted);">Nouveau</div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

      </section>

    </main>
  </div>

  <!-- JS -->
  <script src="view/back/comb.js"></script>
  <script>
    feather.replace();
  </script>
</body>

</html>