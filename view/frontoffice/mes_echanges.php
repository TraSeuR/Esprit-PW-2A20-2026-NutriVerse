<?php if(session_status() === PHP_SESSION_NONE) session_start(); ?>
<?php $id_user = $_SESSION['id_user'] ?? 1; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriVerse &mdash; Mes Échanges</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../view/frontoffice/assets/offre-echange-utilisateur.css" />
  <style>
    /* Custom NutriModal Styles */
    .nutri-modal-overlay {
      position: fixed; inset: 0; background: rgba(0,0,0,0.4);
      display: none; align-items: center; justify-content: center;
      z-index: 10000; backdrop-filter: blur(4px);
      transition: opacity 0.3s ease; opacity: 0;
    }
    .nutri-modal-overlay.active { display: flex; opacity: 1; }
    .nutri-modal-card {
      background: var(--white); width: min(400px, 90%);
      padding: 30px; border-radius: 24px; box-shadow: var(--shadow);
      transform: translateY(20px); transition: transform 0.3s ease;
    }
    .nutri-modal-overlay.active .nutri-modal-card { transform: translateY(0); }
    .nutri-modal-title { font-size: 1.25rem; font-weight: 700; color: var(--green-dark); margin-bottom: 12px; }
    .nutri-modal-body { margin-bottom: 24px; color: var(--muted); font-size: 0.95rem; }
    .nutri-modal-footer { display: flex; gap: 12px; justify-content: flex-end; }
    .nutri-modal-input {
      width: 100%; padding: 12px; border-radius: 12px; border: 1px solid var(--border);
      margin-top: 10px; font-family: inherit; font-size: 1rem;
    }
  </style>
  <script src="../public/js/validation.js" defer></script>
</head>
<body class="offre-echange-user-app">
  <input type="checkbox" id="sss-nav-toggle" />
  <label for="sss-nav-toggle" class="front-sidebar-scrim" aria-label="Fermer le menu"></label>

  <aside class="front-sidebar" aria-label="Navigation">
    <div class="front-sidebar-inner">
      <a class="front-sidebar-logo" href="../view/frontoffice/front.html">
        <img class="front-sidebar-logo-img" src="../view/frontoffice/images/logo.png" width="120" height="60" alt="Logo NutriVerse" />
        <span class="front-sidebar-brand-text">
          <strong>NutriVerse</strong>
          <span>Offre d'ingrédients & échanges locaux</span>
        </span>
      </a>
      <nav class="front-sidebar-nav" aria-label="Navigation du site">
        <a href="../view/frontoffice/front.html">Accueil</a>
        <a href="../view/frontoffice/front.html#categories">Marketplace</a>
        <a href="../view/frontoffice/front.html#recipes">Recettes</a>
        <a href="../view/frontoffice/front.html#programs">Programmes</a>
        <a href="OffreController.php?action=front_list" aria-current="page">Offre &amp; Échange</a>
      </nav>
      <div class="front-sidebar-footer">
        <p class="front-sidebar-user">Alice Martin (ID: <?= $id_user ?>)</p>
        <form method="post" action="php/deconnexion.php">
          <button type="submit" class="ss-btn ss-btn-danger-outline front-sidebar-logout">Déconnexion</button>
        </form>
        <a class="front-sidebar-site-link" href="../view/frontoffice/front.html">&larr; Retour au site NutriVerse</a>
      </div>
    </div>
  </aside>

  <div class="front-main">
    <header class="front-mobile-header">
      <label for="sss-nav-toggle" class="menu-toggle" aria-label="Ouvrir le menu">&#9776;</label>
      <a class="front-mobile-logo" href="../view/frontoffice/front.html">
        <img class="front-mobile-logo-img" src="../view/frontoffice/images/logo.png" width="48" height="48" alt="" />
      </a>
      <span class="front-mobile-title">Mes Échanges</span>
    </header>

    <main class="front-main-content">
      <?php if (!empty($error)) echo "<div style='color:red; background:#ffebeb; padding:10px; margin-bottom:15px; border-radius:5px;'>$error</div>"; ?>
      <?php if (!empty($success)) echo "<div style='color:green; background:#ebffef; padding:10px; margin-bottom:15px; border-radius:5px;'>$success</div>"; ?>
      
      <div class="vue-panel vue-panel--echanges" style="display:block;">
        <section class="sss-section ss-section" id="ech-demande" aria-labelledby="demandes-title">
          <div class="container">
            <header class="sss-section-header ss-section-header">
              <span class="sss-tag ss-tag">Entité Échange</span>
              <h2 id="demandes-title">Nouvelle demande</h2>
              <p>Sélectionnez les offres pour initier un troc.</p>
            </header>
            <div class="sss-grid-1 ss-grid-1">
              <article class="sss-card ss-card">
                <h3>Envoyer une demande</h3>
                <form method="post" action="EchangeController.php?action=add&source=front&view=echanges" class="sss-form-grid ss-form-grid">
                  <div class="sss-field ss-field"><label>Offre convoitée</label>
                    <select name="id_offre_offreur">
                      <option value="">-- Sélectionnez l'offre --</option>
                      <?php if (!empty($offres_disponibles)): foreach ($offres_disponibles as $od): ?>
                        <option value="<?= $od['id_offre'] ?>">OFF-<?= $od['id_offre'] ?> : <?= htmlspecialchars($od['ingredient']) ?></option>
                      <?php endforeach; endif; ?>
                    </select>
                  </div>
                  <div class="sss-field ss-field"><label>Votre offre</label>
                    <select name="id_offre_demandeur">
                      <option value="">-- Votre offre à céder --</option>
                      <?php if (!empty($mes_offres_actives)): foreach ($mes_offres_actives as $mo): ?>
                        <option value="<?= $mo['id_offre'] ?>">OFF-<?= $mo['id_offre'] ?> : <?= htmlspecialchars($mo['ingredient']) ?></option>
                      <?php endforeach; endif; ?>
                    </select>
                  </div>
                  <div class="sss-field ss-field"><label>Date de l'échange</label><input type="date" name="date_proposee" value="<?= date('Y-m-d') ?>" /></div>
                  <div class="sss-field ss-field"><label>Message (optionnel)</label><input type="text" name="message" placeholder="Ex: Pour une salade ce soir..." /></div>
                  <button type="submit" class="ss-btn ss-btn-primary">Lancer l'échange</button>
                </form>
              </article>
            </div>
          </div>
        </section>

        <section class="sss-section ss-section ss-section--alt" id="ech-traiter" aria-labelledby="suivi-title">
          <div class="container">
            <header class="sss-section-header ss-section-header">
              <span class="sss-tag ss-tag">Suivi</span>
              <h2>Mes échanges en cours</h2>
            </header>
            <article class="sss-card ss-card">
              <div class="ss-table-wrap">
                <table class="ss-table">
                  <thead><tr><th>ID échange</th><th>Donné</th><th>Reçu</th><th>Date</th><th>Statut</th><th>Actions</th></tr></thead>
                  <tbody>
                    <?php if (!empty($echanges)): foreach ($echanges as $e): ?>
                      <tr>
                        <td>ECH-<?= htmlspecialchars($e['id_echange']) ?></td>
                        <td>OFF-<?= $e['id_offre_demandeur'] ?> (<?= htmlspecialchars($e['ing_donne'] ?? 'Inconnu') ?>)</td>
                        <td>OFF-<?= $e['id_offre_offreur'] ?> (<?= htmlspecialchars($e['ing_recu'] ?? 'Inconnu') ?>)</td>
                        <td><?= date('d/m/Y', strtotime($e['date_demande'] ?? 'now')) ?></td>
                        <td><span class="ss-badge <?= ($e['statut'] == 'accepte') ? 'ok' : (($e['statut'] == 'refuse') ? 'alert' : 'warn') ?>"><?= htmlspecialchars($e['statut']) ?></span></td>
                        <td>
                          <?php if ($e['statut'] === 'en_attente'): ?>
                            <?php if ($e['id_offreur'] == $id_user): ?>
                              <button onclick="handleEchangeAction(<?= $e['id_echange'] ?>, 'accepte')" class="ss-btn ss-btn-ghost" style="padding: 4px 10px; font-size: 0.8rem; color: var(--ok);">Accepter</button>
                              <button onclick="handleEchangeAction(<?= $e['id_echange'] ?>, 'refuse')" class="ss-btn ss-btn-ghost" style="padding: 4px 10px; font-size: 0.8rem; color: var(--alert);">Refuser</button>
                            <?php endif; ?>
                            <?php if ($e['id_demandeur'] == $id_user): ?>
                              <button onclick="modifierEchange(<?= $e['id_echange'] ?>, '<?= date('Y-m-d', strtotime($e['date_demande'])) ?>', '<?= htmlspecialchars($e['message'] ?? '') ?>')" class="ss-btn ss-btn-ghost" style="padding: 4px 10px; font-size: 0.8rem; color: var(--green-dark);">Modifier</button>
                              <button onclick="handleEchangeAction(<?= $e['id_echange'] ?>, 'delete')" class="ss-btn ss-btn-ghost" style="padding: 4px 10px; font-size: 0.8rem; color: var(--muted);">Annuler</button>
                            <?php endif; ?>
                          <?php else: ?>
                            <span style="color:var(--muted); font-size: 0.8rem;">Terminé</span>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endforeach; else: ?>
                      <tr><td colspan="6">Aucun échange trouvé.</td></tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </article>
          </div>
        </section>
      </div>
    </main>
  </div>

  <form id="hiddenEchangeUpdateForm" method="post" action="EchangeController.php?action=update&source=front&view=echanges" style="display:none;">
    <input type="hidden" name="id_echange" id="upEchId">
    <input type="hidden" name="date_proposee" id="upEchDate">
    <input type="hidden" name="message" id="upEchMsg">
  </form>
  <form id="hiddenEchangeForm" method="post" action="EchangeController.php?action=traiter&source=front&view=echanges" style="display:none;">
    <input type="hidden" name="id_echange" id="echId">
    <input type="hidden" name="decision" id="echDecision">
  </form>

  <!-- NutriModal Structure -->
  <div id="nutriModal" class="nutri-modal-overlay">
    <div class="nutri-modal-card">
      <div id="modalTitle" class="nutri-modal-title">Confirmation</div>
      <div id="modalBody" class="nutri-modal-body">Description...</div>
      <div id="modalPrompt" style="display:none;">
        <div id="promptFields">
          <input type="text" id="modalInput" class="nutri-modal-input" placeholder="Valeur...">
          <input type="date" id="modalDateInput" class="nutri-modal-input" style="display:none; margin-top:10px;">
        </div>
      </div>
      <div class="nutri-modal-footer">
        <button id="modalBtnCancel" class="ss-btn ss-btn-ghost">Annuler</button>
        <button id="modalBtnOk" class="ss-btn ss-btn-primary">Continuer</button>
      </div>
    </div>
  </div>

  <script>
    let modalCallback = null;

    function showNutriModal(title, message, isPrompt, callback, dateVal = null) {
      document.getElementById('modalTitle').innerText = title;
      document.getElementById('modalBody').innerText = message;
      document.getElementById('modalPrompt').style.display = isPrompt ? 'block' : 'none';
      
      const modalDate = document.getElementById('modalDateInput');
      if(dateVal) {
        modalDate.style.display = 'block';
        modalDate.value = dateVal;
      } else {
        modalDate.style.display = 'none';
      }

      if(isPrompt) document.getElementById('modalInput').value = '';
      
      const modal = document.getElementById('nutriModal');
      modal.classList.add('active');
      modalCallback = callback;
    }

    document.getElementById('modalBtnOk').onclick = function() {
      const isPrompt = document.getElementById('modalPrompt').style.display === 'block';
      const isDate = document.getElementById('modalDateInput').style.display === 'block';
      
      let res = true;
      if (isPrompt && isDate) {
        res = { msg: document.getElementById('modalInput').value, date: document.getElementById('modalDateInput').value };
      } else if (isPrompt) {
        res = document.getElementById('modalInput').value;
      }
      
      closeNutriModal();
      if(modalCallback) modalCallback(res);
    };

    document.getElementById('modalBtnCancel').onclick = closeNutriModal;
    function closeNutriModal() { document.getElementById('nutriModal').classList.remove('active'); }

    function modifierEchange(id, currentDate, currentMsg) {
      showNutriModal("Modifier Échange", "Mettez à jour la date et votre message pour cet échange :", true, (res) => {
        if(res && res.date) {
            document.getElementById('upEchId').value = id;
            document.getElementById('upEchDate').value = res.date;
            document.getElementById('upEchMsg').value = res.msg;
            document.getElementById('hiddenEchangeUpdateForm').submit();
        }
      }, currentDate);
      document.getElementById('modalInput').value = currentMsg;
    }

    function handleEchangeAction(id, action) {
      let title = action === 'accepte' ? "Accepter l'échange" : (action === 'refuse' ? "Refuser l'échange" : "Annuler l'échange");
      let msg = "Voulez-vous confirmer cette action sur l'échange ECH-" + id + " ?";
      
      showNutriModal(title, msg, false, (confirm) => {
        if(confirm) {
          if(action === 'delete') {
            document.getElementById('hiddenEchangeForm').action = "EchangeController.php?action=delete&source=front&view=echanges";
          } else {
            document.getElementById('hiddenEchangeForm').action = "EchangeController.php?action=traiter&source=front&view=echanges";
          }
          document.getElementById('echId').value = id;
          document.getElementById('echDecision').value = action;
          document.getElementById('hiddenEchangeForm').submit();
        }
      });
    }
  </script>
</body>
</html>
