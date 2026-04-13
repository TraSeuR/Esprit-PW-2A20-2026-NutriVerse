<?php if(session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriVerse &mdash; Gestion des Échanges (Administration)</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../view/back/assets/back.css" />
  <link rel="stylesheet" href="../view/back/assets/offre-echange-admin.css" />
  <script src="../../public/js/validation.js" defer></script>
</head>
<body>
  <input type="checkbox" id="ssa-sidebar-toggle" />
  <label for="ssa-sidebar-toggle" class="ssa-scrim" aria-label="Fermer le menu"></label>

  <aside class="sidebar" id="sidebar" aria-label="Navigation">
    <div class="sidebar-top">
      <div class="brand">
        <img src="../view/back/images/logo.png" alt="Logo NutriVerse" class="brand-logo" width="58" height="58" />
        <div>
          <h2>NutriVerse</h2>
          <p>Back Office</p>
        </div>
      </div>
      <label for="ssa-sidebar-toggle" class="close-sidebar">&times;</label>
    </div>

    <nav class="sidebar-menu">
      <a href="../view/back/back.html" class="menu-item"><span>Dashboard</span></a>
      <a href="OffreController.php?action=admin_list" class="menu-item"><span>Voir les Offres</span></a>
      <a href="EchangeController.php?action=admin_list&source=admin" class="menu-item active"><span>Gérer les Échanges</span></a>
    </nav>
  </aside>

  <div class="main-content">
    <header class="topbar">
      <div class="topbar-left">
        <label for="ssa-sidebar-toggle" class="menu-btn">&#9776;</label>
        <h2>Gestion des Échanges</h2>
      </div>
    </header>

    <main class="dashboard-content">
      <?php if (!empty($error)) echo "<div style='color:red; background:#ffebeb; padding:10px; margin-bottom:15px; border-radius:5px;'>$error</div>"; ?>
      <?php if (!empty($success)) echo "<div style='color:green; background:#ebffef; padding:10px; margin-bottom:15px; border-radius:5px;'>$success</div>"; ?>

      <section class="ssa-card">
        <div class="ssa-card-header">
           <h3>Échanges en attente de traitement</h3>
           <p>Utilisez les formulaires ci-dessous pour valider ou supprimer un échange par son ID.</p>
        </div>
        <div class="ssa-table-wrap">
          <table class="ssa-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Offre Demandeur</th>
                <th>Offre Offreur</th>
                <th>Statut Actuel</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($echanges)): foreach($echanges as $e): ?>
                <tr>
                  <td>ECH-<?= htmlspecialchars($e['id_echange']) ?></td>
                  <td>OFF-<?= htmlspecialchars($e['id_offre_demandeur']) ?></td>
                  <td>OFF-<?= htmlspecialchars($e['id_offre_offreur']) ?></td>
                  <td><span class="ssa-badge"><?= htmlspecialchars($e['statut']) ?></span></td>
                </tr>
              <?php endforeach; else: ?>
                <tr><td colspan="4">Aucun échange en attente.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>

      <div class="ssa-grid-2" style="margin-top: 20px;">
        <article class="ssa-card">
          <div class="ssa-card-header"><h3>Traitement des décisions</h3></div>
          <form method="post" action="EchangeController.php?action=traiter&source=admin" class="sss-form-grid ss-form-grid">
            <div class="sss-field ss-field">
              <label for="id_echange">ID de l'échange</label>
              <input type="number" id="id_echange" name="id_echange" placeholder="Ex: 5" />
            </div>
            <div class="sss-field ss-field">
              <label for="decision">Décision</label>
              <select id="decision" name="decision">
                <option value="">Choisir la décision</option>
                <option value="accepté">Accepter l'échange</option>
                <option value="refusé">Refuser l'échange</option>
              </select>
            </div>
            <button type="submit" class="ssa-btn">Valider la décision</button>
          </form>
        </article>

        <article class="ssa-card">
          <div class="ssa-card-header"><h3>Annulation / Suppression</h3></div>
          <form method="post" action="EchangeController.php?action=delete&source=admin" class="sss-form-grid ss-form-grid">
            <div class="sss-field ss-field">
              <label for="id_annuler">ID à annuler</label>
              <input type="number" id="id_annuler" name="id_annuler" placeholder="ID de l'échange à supprimer" />
            </div>
            <button type="submit" class="ssa-btn ssa-btn-outline" style="color: #E74C3C; border-color: #E74C3C;">Annuler l'échange</button>
          </form>
        </article>
      </div>
    </main>
  </div>
</body>
</html>
