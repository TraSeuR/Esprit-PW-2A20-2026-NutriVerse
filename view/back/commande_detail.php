<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriVerse - Détail Commande #<?= $order['id_commande'] ?></title>

  <link rel="stylesheet" href="view/back/assets/comb.css" />

  <!-- Google Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Icons -->
  <script src="https://unpkg.com/feather-icons"></script>
  
  <style>
    .detail-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-top: 24px;
    }
    
    .detail-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    
    .detail-card h3 {
        margin-bottom: 20px;
        color: #1c2733;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 8px;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 12px;
    }
    
    .info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .info-list li {
        margin-bottom: 12px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    
    .info-label {
        font-weight: 600;
        color: #6f7680;
        min-width: 120px;
    }
    
    .info-value {
        color: #1c2733;
        font-weight: 500;
    }
    
    .status-select {
        width: 100%;
        padding: 10px;
        border-radius: 8px;
        border: 1px solid #ddd;
        margin-bottom: 16px;
        font-family: inherit;
    }
    
    .btn-update {
        background: #59b84d;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
        width: 100%;
    }
    
    .btn-update:hover {
        background: #3f9636;
    }
    
    .product-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .product-line:last-child {
        border-bottom: none;
    }
    
    .qty-badge {
        background: #edf7ec;
        color: #59b84d;
        padding: 4px 8px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.9rem;
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
      <section class="page-header fade-up" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
          <a href="?action=admin_orders" style="color: #59b84d; text-decoration: none; font-weight: 600; margin-bottom: 8px; display: inline-block;">← Retour à la liste</a>
          <div style="display: flex; align-items: center; gap: 16px;">
            <h1 style="margin: 0;">Détail de la commande #<?= $order['id_commande'] ?></h1>
            <button onclick="exportPDF()" style="background: #e74c3c; color: white; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.3s; box-shadow: 0 2px 8px rgba(231, 76, 60, 0.2);" onmouseover="this.style.background='#c0392b'" onmouseout="this.style.background='#e74c3c'">
              <i data-feather="download"></i> Exporter PDF
            </button>
          </div>
        </div>
        <div>
          <span style="background: #edf7ec; color: #59b84d; padding: 8px 16px; border-radius: 20px; font-weight: 600; text-transform: uppercase;">
             Statut actuel : <?= $order['statut_commande'] ?>
          </span>
        </div>
      </section>

      <section class="detail-container fade-up delay-1">
          
          <!-- INFO CLIENT & STATUS -->
          <div style="display: flex; flex-direction: column; gap: 24px;">
              <div class="detail-card">
                  <h3><i data-feather="user"></i> Informations Client</h3>
                  <ul class="info-list">
                      <li>
                          <span class="info-label">Nom complet :</span>
                          <span class="info-value"><?= htmlspecialchars($order['nom_client']) ?></span>
                      </li>
                      <li>
                          <span class="info-label">Téléphone :</span>
                          <span class="info-value"><?= $order['telephone_client'] ?></span>
                      </li>
                      <li>
                          <span class="info-label">Adresse :</span>
                          <span class="info-value"><?= nl2br(htmlspecialchars($order['adresse_livraison'])) ?></span>
                      </li>
                      <li>
                          <span class="info-label">Date :</span>
                          <span class="info-value"><?= date('d/m/Y à H:i', strtotime($order['date_commande'])) ?></span>
                      </li>
                      <li>
                          <span class="info-label">Paiement :</span>
                          <span class="info-value" style="text-transform: capitalize;"><?= $order['mode_paiement'] ?></span>
                      </li>
                  </ul>
              </div>

              <div class="detail-card">
                  <h3><i data-feather="edit-2"></i> Mettre à jour le statut</h3>
                  <form method="post" action="?action=admin_order_edit">
                      <input type="hidden" name="id" value="<?= $order['id_commande'] ?>">
                      <select name="statut" class="status-select">
                          <option value="en attente" <?= $order['statut_commande'] == 'en attente' ? 'selected' : '' ?>>En attente</option>
                          <option value="confirmée" <?= $order['statut_commande'] == 'confirmée' ? 'selected' : '' ?>>Confirmée</option>
                          <option value="expédiée" <?= $order['statut_commande'] == 'expédiée' ? 'selected' : '' ?>>Expédiée</option>
                          <option value="livrée" <?= $order['statut_commande'] == 'livrée' ? 'selected' : '' ?>>Livrée</option>
                          <option value="annulée" <?= $order['statut_commande'] == 'annulée' ? 'selected' : '' ?>>Annulée</option>
                      </select>
                      <button type="submit" class="btn-update">Enregistrer les modifications</button>
                  </form>
              </div>
          </div>

          <!-- PRODUITS COMMANDES -->
          <div class="detail-card">
              <h3><i data-feather="shopping-bag"></i> Produits Commandés</h3>
              
              <div style="margin-bottom: 20px;">
                  <?php foreach ($lines as $line): ?>
                      <div class="product-line">
                          <div style="display: flex; align-items: center; gap: 12px;">
                              <span class="qty-badge"><?= $line['quantite'] ?>x</span>
                              <span style="font-weight: 500; color: #1c2733;"><?= htmlspecialchars($line['nom']) ?></span>
                          </div>
                          <div style="font-weight: 600; color: #6f7680;">
                              <?= number_format($line['prix_unitaire'], 2) ?> DT
                          </div>
                      </div>
                  <?php endforeach; ?>
              </div>
              
              <div style="border-top: 2px dashed #edf7ec; padding-top: 16px; display: flex; justify-content: space-between; align-items: center;">
                  <span style="font-size: 1.1rem; font-weight: 600; color: #6f7680;">Total Payé</span>
                  <span style="font-size: 1.5rem; font-weight: 700; color: #59b84d;"><?= number_format($order['montant_total'], 2) ?> DT</span>
              </div>
          </div>

      </section>

    </main>
  </div>

  <script src="view/back/comb.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <script>
    feather.replace();

    function exportPDF() {
        const element = document.querySelector('.detail-container');
        
        // Add a temporary title for the PDF
        const title = document.createElement('h2');
        title.innerHTML = 'Détail de la commande #<?= $order['id_commande'] ?>';
        title.style.textAlign = 'center';
        title.style.marginBottom = '20px';
        title.style.color = '#1c2733';
        title.style.gridColumn = '1 / -1'; // Span across all grid columns
        title.id = 'pdf-title';
        element.prepend(title);

        const opt = {
            margin:       0.5,
            filename:     'commande_<?= $order['id_commande'] ?>.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        
        html2pdf().set(opt).from(element).save().then(() => {
            // Remove the title after export is complete
            document.getElementById('pdf-title').remove();
        });
    }
  </script>
</body>
</html>