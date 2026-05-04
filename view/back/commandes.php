<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriVerse - Back Office Commandes</title>

  <link rel="stylesheet" href="view/back/assets/comb.css" />

  <!-- Google Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Icons -->
  <script src="https://unpkg.com/feather-icons"></script>
  <!-- PDF Export -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
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
        <a href="index.php?action=admin_dashboard"><i data-feather="grid"></i> Dashboard</a>
        <a href="#"><i data-feather="book-open"></i> Recettes</a>
        <a href="#"><i data-feather="users"></i> Utilisateurs</a>
        <a href="#"><i data-feather="shopping-bag"></i> Produits</a>
        <a href="index.php?action=admin_orders" class="active"><i data-feather="shopping-cart"></i> Commandes</a>
        <a href="index.php?action=admin_livraisons"><i data-feather="truck"></i> Livraisons</a>
        <a href="#"><i data-feather="activity"></i> Suivi Santé</a>
        <a href="#"><i data-feather="heart"></i> Programmes</a>
        <a href="#"><i data-feather="settings"></i> Paramètres</a>
      </nav>

      <div class="sidebar-footer">
        <p>© 2026 NutriVerse</p>
      </div>
    </aside>

    <!-- MAIN -->
    <main class="main-content">

      <!-- TOPBAR -->
      <header class="topbar">
        <div class="topbar-left">
          <button class="menu-btn" id="menuBtn">
            <i data-feather="menu"></i>
          </button>

          <button id="exportBtn" style="background: var(--orange, #ff8a00); color: white; border: none; padding: 10px 20px; border-radius: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(255, 138, 0, 0.2);">
            <i data-feather="file-text"></i> Exporter
          </button>
        </div>

        <div class="topbar-right">
          <a href="index.php?action=front" style="margin-right:20px; color:#59b84d; text-decoration:none; font-weight:600;">← Retour au site</a>
          <button class="notif-btn">
            <i data-feather="bell"></i>
            <span class="notif-dot"></span>
          </button>

          <div class="admin-box">
            <div class="admin-avatar">A</div>
            <div>
              <h4>Admin</h4>
              <p>Administrateur</p>
            </div>
          </div>
        </div>
      </header>

      <!-- PAGE HEADER -->
      <section class="page-header fade-up">
        <h1>Gestion des Commandes</h1>
        <p>Suivez et gérez les commandes de vos clients</p>
      </section>

      <?php
      $totalCommandes = count($orders);
      $enAttente = 0;
      $revenuTotal = 0;
      $livrees = 0;

      foreach ($orders as $o) {
          $revenuTotal += $o['montant_total'];
          if (strtolower($o['statut_commande']) == 'en attente') $enAttente++;
          if (strtolower($o['statut_commande']) == 'livrée') $livrees++;
      }
      ?>

      <!-- STATS -->
      <section class="stats-grid fade-up delay-1">
        <div class="stat-card">
          <div class="stat-icon green">
            <i data-feather="package"></i>
          </div>
          <div>
            <h3><?= $totalCommandes ?></h3>
            <p>Total commandes</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon orange">
            <i data-feather="clock"></i>
          </div>
          <div>
            <h3><?= $enAttente ?></h3>
            <p>En attente</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon blue">
            <i data-feather="check-circle"></i>
          </div>
          <div>
            <h3><?= $livrees ?></h3>
            <p>Livrées</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon purple">
            <i data-feather="trending-up"></i>
          </div>
          <div>
            <h3><?= number_format($revenuTotal, 2) ?> DT</h3>
            <p>Revenu total</p>
          </div>
        </div>
      </section>

      <!-- FILTER BAR -->
      <section class="filter-bar fade-up delay-1">
        <div class="table-search">
          <i data-feather="search"></i>
          <input type="text" id="orderSearch" placeholder="Rechercher par ID ou nom...">
        </div>
        
        <select id="statusFilter">
          <option value="all">Tous les statuts</option>
          <option value="en attente">En attente</option>
          <option value="confirmée">Confirmée</option>
          <option value="expédiée">Expédiée</option>
          <option value="livrée">Livrée</option>
          <option value="annulée">Annulée</option>
        </select>

        <button id="sortBtn" style="background: var(--orange, #ff8a00); color: white; border: none; padding: 10px 20px; border-radius: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; margin-left: 10px; box-shadow: 0 4px 12px rgba(255, 138, 0, 0.15);">
          <i data-feather="list"></i> Trier
        </button>
      </section>

      <!-- TABLE -->
      <section class="table-card fade-up">
        <div class="table-wrapper">
          <table style="width: 100%; border-collapse: collapse;">
            <thead>
              <tr style="text-align: left; border-bottom: 2px solid #edf7ec; color: #6f7680;">
                <th style="padding: 15px;">ID</th>
                <th style="padding: 15px;">Date</th>
                <th style="padding: 15px;">Client</th>
                <th style="padding: 15px;">Total</th>
                <th style="padding: 15px;">Statut</th>
                <th style="padding: 15px;">Actions</th>
              </tr>
            </thead>

            <tbody id="ordersTableBody">
              <?php if(empty($orders)): ?>
                <tr>
                  <td colspan="6">
                    <div class="empty-table" style="text-align:center; padding: 40px;">
                      <i data-feather="inbox" style="width:40px;height:40px;color:#ccc;margin-bottom:10px;"></i>
                      <p>Aucune commande affichée pour le moment</p>
                    </div>
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($orders as $o): ?>
                  <tr style="border-bottom: 1px solid #f0f0f0;" 
                      data-id="<?= $o['id_commande'] ?>" 
                      data-status="<?= strtolower($o['statut_commande']) ?>"
                      data-client="<?= strtolower(htmlspecialchars($o['nom_client'])) ?>">
                    <td style="padding: 15px;"><?= $o['id_commande'] ?></td>
                    <td style="padding: 15px;"><?= date('Y-m-d', strtotime($o['date_commande'])) ?></td>
                    <td style="padding: 15px;"><?= htmlspecialchars($o['nom_client']) ?></td>
                    <td style="padding: 15px; font-weight: 600;">
                        <?= number_format($o['montant_total'], 2) ?> DT
                        <?php if(!empty($o['code_promo'])): ?>
                            <br><small style="color:#27ae60; font-weight:400;"><?= htmlspecialchars($o['code_promo']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 15px;">
                      <span class="status-badge" style="background:#edf7ec; color:#59b84d; padding:5px 12px; border-radius:20px; font-size:12px; font-weight:600; text-transform:capitalize;">
                        <?= $o['statut_commande'] ?>
                      </span>
                    </td>
                    <td style="padding: 15px;">
                      <a href="?action=admin_order_view&id=<?= $o['id_commande'] ?>" style="color:#59b84d; text-decoration:none; margin-right:10px; font-weight:500;">Voir</a>
                      <a href="?action=admin_order_delete&id=<?= $o['id_commande'] ?>" onclick="return confirm('Supprimer ?')" style="color:#e74c3c; text-decoration:none; font-weight:500;">Supprimer</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>

    </main>
  </div>

  <script src="view/back/comb.js"></script>
  <script>
    feather.replace();

    const orderSearch = document.getElementById('orderSearch');
    const statusFilter = document.getElementById('statusFilter');
    const tableRows = document.querySelectorAll('#ordersTableBody tr[data-id]');

    function filterTable() {
        const searchTerm = orderSearch.value.toLowerCase();
        const statusTerm = statusFilter.value.toLowerCase();

        tableRows.forEach(row => {
            const id = row.getAttribute('data-id').toLowerCase();
            const client = row.getAttribute('data-client').toLowerCase();
            const status = row.getAttribute('data-status').toLowerCase();

            const matchesSearch = id.includes(searchTerm) || client.includes(searchTerm);
            const matchesStatus = statusTerm === 'all' || status === statusTerm;

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    orderSearch.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);

    // PDF Export Logic
    document.getElementById('exportBtn').addEventListener('click', function () {
        // Create a temporary container for the export
        const element = document.createElement('div');
        element.style.padding = '20px';
        
        // Add a title
        const title = document.createElement('h1');
        title.innerText = 'Les Commandes - NutriVerse';
        title.style.textAlign = 'center';
        title.style.color = '#0b8d34';
        title.style.marginBottom = '20px';
        title.style.fontFamily = 'Poppins, sans-serif';
        element.appendChild(title);

        // Add the table content
        const tableClone = document.querySelector('.table-wrapper').cloneNode(true);
        element.appendChild(tableClone);

        const opt = {
            margin:       [10, 10],
            filename:     'liste_commandes_nutriverse.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2 },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
        };

        html2pdf().set(opt).from(element).save();
    });

    // Sorting Logic
    let sortAsc = true;
    document.getElementById('sortBtn').addEventListener('click', function() {
        const tableBody = document.getElementById('ordersTableBody');
        const rows = Array.from(tableBody.querySelectorAll('tr[data-id]'));

        rows.sort((a, b) => {
            const idA = parseInt(a.getAttribute('data-id'));
            const idB = parseInt(b.getAttribute('data-id'));
            return sortAsc ? idA - idB : idB - idA;
        });

        // Re-append rows in sorted order
        rows.forEach(row => tableBody.appendChild(row));
        
        // Toggle direction for next click
        sortAsc = !sortAsc;
        
        // Update icon or text if desired (optional)
        this.innerHTML = sortAsc ? '<i data-feather="arrow-up"></i> Trier' : '<i data-feather="arrow-down"></i> Trier';
        feather.replace();
    });
  </script>
</body>
</html>
