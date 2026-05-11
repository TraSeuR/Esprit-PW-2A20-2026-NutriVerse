<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../Controller/OrderC.php';
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable commande']);

$db = config::getConnexion();
$stmt = $db->query("SELECT * FROM commande ORDER BY date_commande DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'admin_order_delete' && isset($_GET['id'])) {
    $db->prepare("DELETE FROM commande WHERE id_commande = ?")->execute([(int)$_GET['id']]);
    header('Location: commandes.php');
    exit;
}
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'admin_order_view' && isset($_GET['id'])) {
        $orderId = (int)$_GET['id'];
        $stmt = $db->prepare("SELECT * FROM commande WHERE id_commande = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            $stmtLines = $db->prepare("SELECT l.*, p.nom FROM ligne_commande l JOIN produit p ON l.id_produit = p.idproduit WHERE l.id_commande = ?");
            $stmtLines->execute([$orderId]);
            $lines = $stmtLines->fetchAll(PDO::FETCH_ASSOC);

            require __DIR__ . '/commande_detail.php';
            exit;
        }
    }
    
    if ($_GET['action'] === 'admin_order_edit' && isset($_POST['id']) && isset($_POST['statut'])) {
        $orderId = (int)$_POST['id'];
        $statut = $_POST['statut'];
        $db->prepare("UPDATE commande SET statut_commande = ? WHERE id_commande = ?")->execute([$statut, $orderId]);
        header('Location: commandes.php?action=admin_order_view&id=' . $orderId);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriVerse - Back Office Commandes</title>

  <link rel="stylesheet" href="../assets/back.css" />
  <style>
    .dashboard { display: flex; min-height: 100vh; }
    .dashboard-content { padding: 30px; }
    .filter-bar { background: white; padding: 18px; border-radius: 24px; box-shadow: var(--shadow); display: flex; gap: 18px; margin-bottom: 22px; align-items: center; }
    .table-search { flex:1; display:flex; align-items:center; gap:12px; border:1px solid var(--border); border-radius:16px; padding:14px 16px; }
    .table-search input { border:none; outline:none; width:100%; font-size:0.95rem; font-family:inherit; }
    .filter-bar select { border:1px solid var(--border); border-radius:16px; padding:14px 16px; outline:none; background:white; }
  </style>

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
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/integ/view/BackOffice/sidebar.php'; ?>

    <!-- MAIN -->
    <main class="main-content dashboard-content">
      <?php include $_SERVER['DOCUMENT_ROOT'] . '/integ/view/BackOffice/topbar.php'; ?>

      <!-- PAGE HEADER -->
      <section class="page-header fade-up" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
          <h1>Gestion des Commandes</h1>
          <p>Suivez et gérez les commandes de vos clients</p>
        </div>
        <button id="exportBtn" style="background: #e74c3c; color: white; border: none; padding: 10px 20px; border-radius: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.3s; box-shadow: 0 4px 12px rgba(231, 76, 60, 0.2);" onmouseover="this.style.background='#c0392b'" onmouseout="this.style.background='#e74c3c'">
          <i data-feather="download"></i> Exporter PDF
        </button>
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

  <script src="comb.js"></script>
  <script>
    // Trigger fade-up animations
    document.querySelectorAll('.fade-up').forEach(el => {
      setTimeout(() => el.classList.add('show'), 50);
    });

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
        title.innerText = 'liste des commandes NutriVerse';
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


