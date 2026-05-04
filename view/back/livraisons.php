<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriVerse - Back Office Livraisons</title>

  <link rel="stylesheet" href="view/back/assets/comb.css" />

  <!-- Google Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Icons -->
  <script src="https://unpkg.com/feather-icons"></script>
  <!-- PDF Export -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

  <style>
    /* Modal styles pour l'assignation du livreur */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
      align-items: center;
      justify-content: center;
    }
    
    .modal.active {
      display: flex;
    }
    
    .modal-content {
      background: white;
      padding: 30px;
      border-radius: 16px;
      width: 400px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    
    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
    
    .close-modal {
      cursor: pointer;
      color: #6f7680;
    }
    
    .modal-form label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
      color: #1c2733;
    }
    
    .modal-form input, .modal-form select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 8px;
      margin-bottom: 16px;
      font-family: inherit;
    }
    
    .btn-save {
      width: 100%;
      background: #59b84d;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      margin-top: 10px;
    }
  </style>
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
        <a href="index.php?action=admin_orders"><i data-feather="shopping-cart"></i> Commandes</a>
        <a href="index.php?action=admin_livraisons" class="active"><i data-feather="truck"></i> Livraisons</a>
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
          <button id="exportBtn" style="background: var(--orange, #ff8a00); color: white; border: none; padding: 10px 20px; border-radius: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; margin-left:15px; box-shadow: 0 4px 12px rgba(255, 138, 0, 0.2);">
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
        <h1>Suivi des Livraisons</h1>
        <p>Gérez l'assignation des livreurs et le statut des expéditions (Jointure Commande ↔ Livraison intégrée)</p>
      </section>

      <!-- FILTER BAR -->
      <section class="filter-bar fade-up delay-1" style="background:white; padding:18px; border-radius:24px; box-shadow:var(--shadow); display:flex; justify-content:space-between; gap:18px; margin-bottom:22px;">
        <div class="table-search" style="flex:1; display:flex; align-items:center; gap:12px; border:1px solid var(--border); border-radius:16px; padding:14px 16px;">
          <i data-feather="search"></i>
          <input type="text" id="livraisonSearch" placeholder="Rechercher par ID livraison ou livreur..." style="width:100%; border:none; outline:none; font-size:0.95rem;">
        </div>
        
        <select id="livraisonStatusFilter" style="border:1px solid var(--border); border-radius:16px; padding:14px 16px; outline:none; background:white; min-width:220px;">
          <option value="all">Tous les statuts</option>
          <option value="en cours de préparation">En cours de préparation</option>
          <option value="en route">En route</option>
          <option value="livrée">Livrée</option>
          <option value="annulée">Annulée</option>
        </select>

        <button id="sortBtn" style="background: var(--orange, #ff8a00); color: white; border: none; padding: 10px 20px; border-radius: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(255, 138, 0, 0.15);">
          <i data-feather="list"></i> Trier
        </button>
      </section>

      <!-- TABLE -->
      <section class="table-card fade-up">
        <div class="table-wrapper">
          <table style="width: 100%; border-collapse: collapse;">
            <thead>
              <tr style="text-align: left; border-bottom: 2px solid #edf7ec; color: #6f7680;">
                <th style="padding: 15px;">ID Livraison</th>
                <th style="padding: 15px;">Date de création</th>
                <th style="padding: 15px;">Client (Jointure)</th>
                <th style="padding: 15px;">Adresse</th>
                <th style="padding: 15px;">Livreur</th>
                <th style="padding: 15px;">Statut</th>
                <th style="padding: 15px;">Actions</th>
              </tr>
            </thead>

            <tbody id="livraisonsTableBody">
              <?php if(empty($livraisons)): ?>
                <tr>
                  <td colspan="7">
                    <div class="empty-table" style="text-align:center; padding: 40px;">
                      <i data-feather="truck" style="width:40px;height:40px;color:#ccc;margin-bottom:10px;"></i>
                      <p>Aucune livraison n'est actuellement en cours.</p>
                      <p style="font-size: 0.9rem; color: #999;">Astuce : Modifiez le statut d'une commande en "expédiée" pour générer automatiquement une livraison.</p>
                    </div>
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($livraisons as $l): ?>
                  <tr style="border-bottom: 1px solid #f0f0f0;"
                      data-id="<?= $l['id_livraison'] ?>"
                      data-status="<?= strtolower($l['statut_livraison']) ?>"
                      data-livreur="<?= strtolower(htmlspecialchars($l['nom_livreur'])) ?>">
                    <td style="padding: 15px; font-weight:600;">LIV-#<?= $l['id_livraison'] ?></td>
                    <td style="padding: 15px;"><?= date('d/m/Y', strtotime($l['date_livraison'])) ?></td>
                    <td style="padding: 15px; color:#59b84d; font-weight:500;">
                        <!-- JOIN DISPLAY -->
                        <?= htmlspecialchars($l['nom_client']) ?><br>
                        <small style="color:#6f7680;">Cmd #<?= $l['id_commande'] ?></small>
                    </td>
                    <td style="padding: 15px; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($l['adresse_livraison']) ?>">
                        <?= htmlspecialchars($l['adresse_livraison']) ?>
                    </td>
                    <td style="padding: 15px; font-weight: <?= $l['nom_livreur'] == 'Non assigné' ? 'normal' : '600' ?>; color: <?= $l['nom_livreur'] == 'Non assigné' ? '#e74c3c' : '#1c2733' ?>;">
                        <i data-feather="user" style="width:14px; height:14px; margin-right:4px; vertical-align:middle;"></i>
                        <?= htmlspecialchars($l['nom_livreur']) ?>
                    </td>
                    <td style="padding: 15px;">
                      <span class="status-badge" style="background:#e0f0ff; color:#2980b9; padding:5px 12px; border-radius:20px; font-size:12px; font-weight:600;">
                        <?= htmlspecialchars($l['statut_livraison']) ?>
                      </span>
                    </td>
                    <td style="padding: 15px;">
                      <button onclick="openModal(<?= $l['id_livraison'] ?>, '<?= addslashes($l['nom_livreur']) ?>', '<?= addslashes($l['statut_livraison']) ?>')" style="background:none; border:1px solid #59b84d; color:#59b84d; padding:6px 12px; border-radius:6px; cursor:pointer; font-weight:500; margin-right:10px;">Gérer</button>
                      <a href="?action=admin_livraison_delete&id=<?= $l['id_livraison'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette livraison ?')" style="color:#e74c3c; text-decoration:none; font-weight:500;"><i data-feather="trash-2" style="width:18px;height:18px;vertical-align:middle;"></i></a>
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

  <!-- MODAL D'ASSIGNATION DE LIVREUR -->
  <div id="livraisonModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 style="margin:0; color:#1c2733;"><i data-feather="edit" style="width:18px;height:18px;vertical-align:middle;margin-right:8px;"></i> Mettre à jour la livraison</h3>
        <i data-feather="x" class="close-modal" onclick="closeModal()"></i>
      </div>
      <form action="index.php?action=admin_livraison_update" method="POST" class="modal-form">
        <input type="hidden" name="id_livraison" id="modal_id_livraison">
        
        <label for="nom_livreur">Nom du Livreur</label>
        <input type="text" name="nom_livreur" id="modal_nom_livreur" placeholder="Ex: Mohamed Ali">
        
        <label for="status_livraison">Statut de livraison</label>
        <select name="status_livraison" id="modal_status_livraison">
          <option value="en cours de préparation">En cours de préparation</option>
          <option value="en route">En route</option>
          <option value="livrée">Livrée</option>
          <option value="annulée">Annulée</option>
        </select>
        
        <button type="submit" class="btn-save">Enregistrer les modifications</button>
      </form>
    </div>
  </div>

  <script src="view/back/comb.js"></script>
  <script>
    feather.replace();
    
    function openModal(id, livreur, statut) {
        document.getElementById('modal_id_livraison').value = id;
        document.getElementById('modal_nom_livreur').value = livreur === 'Non assigné' ? '' : livreur;
        document.getElementById('modal_status_livraison').value = statut;
        document.getElementById('livraisonModal').classList.add('active');
    }
    
    function closeModal() {
        document.getElementById('livraisonModal').classList.remove('active');
    }

    // SEARCH & FILTER LOGIC
    const livraisonSearch = document.getElementById('livraisonSearch');
    const livraisonStatusFilter = document.getElementById('livraisonStatusFilter');
    const livraisonRows = document.querySelectorAll('#livraisonsTableBody tr[data-id]');

    function filterLivraisons() {
        const searchTerm = livraisonSearch.value.toLowerCase();
        const statusTerm = livraisonStatusFilter.value.toLowerCase();

        livraisonRows.forEach(row => {
            const id = row.getAttribute('data-id').toLowerCase();
            const livreur = row.getAttribute('data-livreur').toLowerCase();
            const status = row.getAttribute('data-status').toLowerCase();

            const matchesSearch = id.includes(searchTerm) || livreur.includes(searchTerm);
            const matchesStatus = statusTerm === 'all' || status === statusTerm;

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    livraisonSearch.addEventListener('input', filterLivraisons);
    livraisonStatusFilter.addEventListener('change', filterLivraisons);

    // PDF Export Logic
    document.getElementById('exportBtn').addEventListener('click', function () {
        const element = document.createElement('div');
        element.style.padding = '20px';
        
        const title = document.createElement('h1');
        title.innerText = 'Les Livraisons - NutriVerse';
        title.style.textAlign = 'center';
        title.style.color = '#0b8d34';
        title.style.marginBottom = '20px';
        title.style.fontFamily = 'Poppins, sans-serif';
        element.appendChild(title);

        const tableClone = document.querySelector('.table-wrapper').cloneNode(true);
        element.appendChild(tableClone);

        const opt = {
            margin:       [10, 10],
            filename:     'liste_livraisons_nutriverse.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2 },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
        };

        html2pdf().set(opt).from(element).save();
    });

    // Sorting Logic
    let sortAsc = true;
    document.getElementById('sortBtn').addEventListener('click', function() {
        const tableBody = document.getElementById('livraisonsTableBody');
        const rows = Array.from(tableBody.querySelectorAll('tr[data-id]'));

        rows.sort((a, b) => {
            const idA = parseInt(a.getAttribute('data-id'));
            const idB = parseInt(b.getAttribute('data-id'));
            return sortAsc ? idA - idB : idB - idA;
        });

        rows.forEach(row => tableBody.appendChild(row));
        sortAsc = !sortAsc;
        this.innerHTML = sortAsc ? '<i data-feather="arrow-up"></i> Trier' : '<i data-feather="arrow-down"></i> Trier';
        feather.replace();
    });
  </script>
</body>
</html>
