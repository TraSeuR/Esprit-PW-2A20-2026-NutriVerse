<?php if(session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriVerse &mdash; Offre d'ingrédients & Échanges (utilisateur)</title>
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
    /* Toast Styles */
    #nutriToastContainer {
      position: fixed; bottom: 20px; right: 20px; z-index: 20000;
      display: flex; flex-direction: column; gap: 10px; pointer-events: none;
    }
    .nutri-toast {
      background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(8px);
      padding: 16px 24px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      border-left: 5px solid var(--green); color: var(--text);
      font-weight: 500; min-width: 250px; pointer-events: auto;
      transform: translateX(120%); transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .nutri-toast.active { transform: translateX(0); }
    .nutri-toast--error { border-left-color: var(--alert); }
    /* Map Container Style */
    #selectionMap, #globalOffresMap {
      width: 100%;
      height: 350px;
      border-radius: 16px;
      margin-bottom: 20px;
      box-shadow: var(--shadow);
      z-index: 1;
    }
  </style>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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
      <p class="front-sidebar-nav-label">Module</p>
      <nav class="front-sidebar-nav" aria-label="Choisir l'interface">
        <label for="vue-offres" class="front-sidebar-nav-switch">Offres</label>
        <label for="vue-echanges" class="front-sidebar-nav-switch">Échanges</label>
      </nav>
      <p class="front-sidebar-nav-label">Raccourcis &mdash; Offres</p>
      <nav class="front-sidebar-nav front-sidebar-nav--sub" aria-label="Sections OffreIngredient">
        <a href="#offres-mes-offres">Mes offres</a>
        <a href="#offres-localisation">Recherche localisation</a>
        <a href="#offres-don">Mode don solidaire</a>
        <a href="#offres-note">Note moyenne utilisateur</a>
      </nav>
      <p class="front-sidebar-nav-label">Raccourcis &mdash; Échanges</p>
      <nav class="front-sidebar-nav front-sidebar-nav--sub" aria-label="Sections Échange">
        <a href="#ech-demande">Demander un échange</a>
        <a href="#ech-traiter">Traiter les demandes</a>
      </nav>
      <div class="front-sidebar-footer">
        <p class="front-sidebar-user">Alice Martin</p>
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
      <span class="front-mobile-title">Offres &amp; échanges</span>
    </header>

    <main class="front-main-content">
      <div class="nutri-auto-hide">
        <?php if (!empty($error)) echo "<div style='color:red; background:#ffebeb; padding:10px; margin-bottom:15px; border-radius:5px;'>$error</div>"; ?>
        <?php if (!empty($success)) echo "<div style='color:green; background:#ebffef; padding:10px; margin-bottom:15px; border-radius:5px;'>$success</div>"; ?>
      </div>
      
      <?php $active_tab = $active_tab ?? 'offres'; ?>
      <input type="radio" name="vue-module" id="vue-offres" class="vue-input" <?= $active_tab === 'offres' ? 'checked' : '' ?> hidden />
      <input type="radio" name="vue-module" id="vue-echanges" class="vue-input" <?= $active_tab === 'echanges' ? 'checked' : '' ?> hidden />

      <div class="vue-tab-bar" role="tablist" aria-label="Choisir OffreIngredient ou Échange">
        <label for="vue-offres" class="vue-tab-btn" role="tab">Offres</label>
        <label for="vue-echanges" class="vue-tab-btn" role="tab">Échanges</label>
      </div>

      <!-- PANEL OFFRE INGREDIENT -->
      <div class="vue-panel vue-panel--offres" id="vue-panel-offres">
        <section class="sss-section ss-section ss-section--alt" id="offres-mes-offres" aria-labelledby="offres-title">
          <div class="container">
            <header class="sss-section-header ss-section-header">
              <span class="sss-tag ss-tag">Entité OffreIngredient</span>
              <h2 id="offres-title">Mes offres d'ingrédients</h2>
              <p>Une offre peut recevoir plusieurs demandes d'échange (0..*).</p>
            </header>
            
            <article class="sss-card ss-card" style="margin-bottom:20px;">
              <h3 style="margin-bottom: 15px;">Liste de mes offres publiées</h3>
              <div class="ss-table-wrap">
                <table class="ss-table">
                  <thead><tr><th>ID</th><th>Ingrédient</th><th>Quantité</th><th>Lieu</th><th>Type</th><th>Actions</th></tr></thead>
                  <tbody>
                    <?php if (!empty($offres)): foreach ($offres as $o): ?>
                      <tr id="row-offre-<?= $o['id_offre'] ?>">
                        <td>OFF-<?= htmlspecialchars($o['id_offre']) ?></td>
                        <td>
                          <strong><?= htmlspecialchars($o['ingredient']) ?></strong>
                          <?php if(!empty($o['description'])): ?>
                            <br><small style="color:var(--muted); font-style:italic; font-size: 0.75rem;"><?= htmlspecialchars($o['description']) ?></small>
                          <?php endif; ?>
                        </td>
                        <td class="qty-cell"><?= htmlspecialchars($o['quantite'] . ' ' . $o['unite_mesure']) ?></td>
                        <td><?= htmlspecialchars($o['localisation']) ?></td>
                        <td><span class="ss-badge <?= $o['type_offre'] === 'don' ? 'ok' : '' ?>"><?= htmlspecialchars($o['type_offre']) ?></span></td>
                        <td>
                          <button onclick="modifierOffre(<?= $o['id_offre'] ?>, '<?= htmlspecialchars($o['quantite']) ?>', '<?= addslashes($o['localisation']) ?>', <?= $o['latitude'] ?? 'null' ?>, <?= $o['longitude'] ?? 'null' ?>)" class="ss-btn ss-btn-ghost" style="padding: 4px 10px; font-size: 0.8rem; color: var(--green-dark);">Modifier</button>
                          <button onclick="supprimerOffre(<?= $o['id_offre'] ?>)" class="ss-btn ss-btn-ghost" style="padding: 4px 10px; font-size: 0.8rem; color: var(--alert);">Supprimer</button>
                        </td>
                      </tr>
                    <?php endforeach; else: ?>
                      <tr><td colspan="6">Aucune offre publiée. Ajoutez-en une ci-dessous !</td></tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </article>

            <div class="sss-grid-1 ss-grid-1">
              <article class="sss-card ss-card">
                <h3>Ajouter une offre</h3>
                <form method="post" action="OffreController.php?action=add" class="sss-form-grid ss-form-grid">
                  <div class="sss-field ss-field"><label>Ingrédient</label><input type="text" name="ingredient" placeholder="Tomates bio" /></div>
                  <div class="sss-field ss-field"><label>Quantité</label><input type="text" name="quantite" placeholder="5" /></div>
                  <div class="sss-field ss-field"><label>Ville (Recherche automatique sur la carte)</label><input type="text" id="villeInput" name="ville" placeholder="Tunis, Monastir..." /></div>
                  <input type="hidden" name="latitude" id="latInput">
                  <input type="hidden" name="longitude" id="lngInput">
                  
                  <div style="grid-column: 1 / -1;">
                    <p style="font-size: 0.85rem; color: var(--muted); margin-bottom: 10px;">Cliquez sur la carte pour définir la localisation exacte :</p>
                    <div id="selectionMap"></div>
                  </div>

                  <button type="submit" class="ss-btn ss-btn-primary">Publier l'offre</button>
                </form>
              </article>
            </div>
          </div>
        </section>

        <section class="sss-section ss-section ss-section--alt" id="offres-don" aria-labelledby="don-title">
          <div class="container">
            <header class="sss-section-header ss-section-header">
              <span class="sss-tag ss-tag">Innovation</span>
              <h2 id="don-title">Mode don solidaire</h2>
              <p>Publiez une offre gratuite pour donner vos ingrédients sans contrepartie.</p>
            </header>
            <article class="sss-card ss-card">
              <form id="formDon" method="post" action="EchangeController.php?action=add_don&source=front" class="sss-form-grid ss-form-grid">
                <div class="sss-field ss-field"><label>Ingrédient</label><input type="text" name="ingredient" placeholder="Ex: Panier de légumes" /></div>
                <div class="sss-field ss-field" style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                  <div><label>Quantité</label><input type="text" name="quantite" placeholder="5" /></div>
                  <div><label>Ville</label><input type="text" name="ville" placeholder="Tunis" /></div>
                </div>
                <div class="sss-field ss-field"><label>Message / Description</label><textarea name="message" rows="3" placeholder="Ex: Récolte de mon jardin..."></textarea></div>
                <button type="button" onclick="publierDon()" class="ss-btn ss-btn-primary">Publier le don solidaire</button>
              </form>
            </article>
          </div>
        </section>

        <section class="sss-section ss-section" id="offres-note" aria-labelledby="note-title">
          <div class="container">
            <header class="sss-section-header ss-section-header">
              <span class="sss-tag ss-tag">Innovation</span>
              <h2 id="note-title">Note & Évaluation utilisateur</h2>
              <p>Après chaque échange conclu, donnez une <strong>note sur 5</strong>.</p>
            </header>
            <article class="sss-card ss-card">
              <form method="post" action="OffreController.php?action=evaluer" class="sss-form-grid ss-form-grid">
                <div class="sss-field ss-field"><label>ID de l'échange</label>
                  <select name="id_echange">
                    <option value="">-- Sélectionnez l'échange --</option>
                    <?php if(!empty($echanges)): foreach($echanges as $e): ?>
                      <?php if($e['statut'] === 'accepte'): ?>
                        <option value="<?= $e['id_echange'] ?>">ECH-<?= $e['id_echange'] ?> : <?= htmlspecialchars($e['ing_recu'] ?? 'Offre') ?></option>
                      <?php endif; ?>
                    <?php endforeach; endif; ?>
                  </select>
                </div>
                <div class="sss-field ss-field"><label>Note</label>
                  <select name="note"><option value="5">⭐⭐⭐⭐⭐</option><option value="4">⭐⭐⭐⭐</option><option value="3">⭐⭐⭐</option><option value="2">⭐⭐</option><option value="1">⭐</option></select>
                </div>
                <button type="submit" class="ss-btn ss-btn-outline">Évaluer</button>
              </form>
            </article>
          </div>
        </section>

        <section class="sss-section ss-section--alt" id="offres-dispo" aria-labelledby="dispo-title">
          <div class="container">
            <header class="sss-section-header ss-section-header">
              <span class="sss-tag ss-tag">Communauté</span>
              <h2 id="dispo-title">Découvrir les offres</h2>
              <p>Visualisez les dons et échanges à proximité sur la carte interactive.</p>
            </header>

            <article class="sss-card ss-card" style="margin-bottom: 20px;">
              <div style="margin-bottom: 15px;">
                <input type="text" id="globalSearchInput" class="nutri-modal-input" placeholder="🔍 Rechercher un ingrédient ou une ville (ex: Tomates, Monastir...)" style="margin-top:0; border-radius: 20px; padding-left: 20px;">
              </div>
              <div id="globalOffresMap"></div>
            </article>

            <article class="sss-card ss-card">
              <div class="ss-table-wrap">
                <table class="ss-table">
                  <thead><tr><th>ID</th><th>Ingrédient</th><th>Quantité</th><th>Ville</th><th>Type</th><th>Actions</th></tr></thead>
                  <tbody>
                    <?php if (!empty($offres_disponibles)): foreach ($offres_disponibles as $od): ?>
                        <tr id="row-dispo-<?= $od['id_offre'] ?>">
                          <td>OFF-<?= $od['id_offre'] ?></td>
                          <td><strong><?= htmlspecialchars($od['ingredient']) ?></strong></td>
                          <td><?= htmlspecialchars($od['quantite']) ?></td>
                          <td>
                            <?= htmlspecialchars($od['localisation']) ?>
                            <?php if(!empty($od['latitude'])): ?>
                                <button onclick="zoomToGlobalOffer(<?= $od['latitude'] ?>, <?= $od['longitude'] ?>)" class="ss-btn ss-btn-ghost" style="padding: 0; margin-left:5px; font-size: 1.1rem; min-width:auto; height:auto; background:none; border:none;" title="Voir sur la carte">📍</button>
                            <?php endif; ?>
                          </td>
                          <td><span class="ss-badge <?= $od['type_offre'] === 'don' ? 'ok' : 'warn' ?>"><?= htmlspecialchars($od['type_offre']) ?></span></td>
                          <td>
                            <?php if($od['type_offre'] === 'don'): ?>
                              <button onclick="prendreDon(<?= $od['id_offre'] ?>)" class="ss-btn ss-btn-primary" style="padding: 4px 10px; font-size: 0.8rem;">Récupérer le don</button>
                            <?php else: ?>
                              <button onclick="preparerEchange(<?= $od['id_offre'] ?>)" class="ss-btn ss-btn-ghost" style="padding: 4px 10px; font-size: 0.8rem; color: var(--green-dark);">Demander l'échange</button>
                            <?php endif; ?>
                          </td>
                        </tr>
                    <?php endforeach; else: ?>
                      <tr><td colspan="6">Aucune offre disponible pour le moment.</td></tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </article>
          </div>
        </section>
      </div>

      <!-- PANEL ECHANGE -->
      <div class="vue-panel vue-panel--echanges" id="vue-panel-echanges">
        <section class="sss-section ss-section" id="ech-demande" aria-labelledby="demandes-title">
          <div class="container">
            <header class="sss-section-header ss-section-header">
              <span class="sss-tag ss-tag">Entité Échange</span>
              <h2 id="demandes-title">Demandes d'échange</h2>
              <p>Un <strong>échange</strong> relie <strong>deux offres</strong>.</p>
            </header>
            <div class="sss-grid-1 ss-grid-1">
              <article class="sss-card ss-card">
                <h3>Envoyer une demande</h3>
                <form method="post" action="EchangeController.php?action=add&source=front" class="sss-form-grid ss-form-grid">
                  <div class="sss-field ss-field"><label>Offre convoitée</label>
                    <select name="id_offre_offreur" id="select_offre_cible">
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
                  <div class="sss-field ss-field"><label>Date de l'échange</label><input type="text" name="date_proposee" value="<?= date('Y-m-d') ?>" placeholder="YYYY-MM-DD" /></div>
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
                  <thead><tr><th>ID échange</th><th>Donné</th><th>Reçu</th><th>Date</th><th>Statut</th><th>Message</th><th>Note Échange</th><th>Actions</th></tr></thead>
                  <tbody>
                    <?php if (!empty($echanges)): foreach ($echanges as $e): ?>
                      <tr id="row-ech-<?= $e['id_echange'] ?>">
                        <td>ECH-<?= htmlspecialchars($e['id_echange']) ?></td>
                        <td class="donne-cell">OFF-<?= $e['id_offre_demandeur'] ?> (<?= htmlspecialchars($e['ing_donne'] ?? 'Inconnu') ?>)</td>
                        <td class="recu-cell">OFF-<?= $e['id_offre_offreur'] ?> (<?= htmlspecialchars($e['ing_recu'] ?? 'Inconnu') ?>)</td>
                        <td class="date-cell"><?= date('d/m/Y', strtotime($e['date_demande'] ?? 'now')) ?></td>
                        <td><span class="ss-badge statut-badge <?= ($e['statut'] == 'accepte') ? 'ok' : (($e['statut'] == 'refuse') ? 'alert' : 'warn') ?>"><?= htmlspecialchars($e['statut']) ?></span></td>
                        <td class="msg-cell"><small><?= htmlspecialchars($e['message'] ?? '-') ?></small></td>
                        <td>
                          <?php 
                            // On affiche la note que l'utilisateur a DONNÉE pour cet échange
                            $n_eche = ($e['id_demandeur'] == $id_user) ? ($e['note_demandeur'] ?? 0) : ($e['note_offreur'] ?? 0);
                          ?>
                          <?php if ($n_eche > 0): ?>
                            <span style="color: #f1c40f; font-size: 1.1rem;">
                              <?= str_repeat('⭐', $n_eche) ?>
                            </span>
                            <br><small>(<?= $n_eche ?>/5)</small>
                          <?php else: ?>
                            <small style="opacity:0.5; font-style:italic;">Non noté</small>
                          <?php endif; ?>
                        </td>
                        <td>
                          <div class="action-buttons" style="display:flex; gap:5px; flex-wrap:wrap;">
                            <?php if ($e['statut'] === 'en_attente'): ?>
                              <?php if ($e['id_offreur'] == $id_user): ?>
                                <button onclick="handleEchangeAction(<?= $e['id_echange'] ?>, 'accepte')" class="ss-btn ss-btn-ghost" style="padding: 4px 10px; font-size: 0.8rem; color: var(--ok);">Accepter</button>
                                <button onclick="handleEchangeAction(<?= $e['id_echange'] ?>, 'refuse')" class="ss-btn ss-btn-ghost" style="padding: 4px 10px; font-size: 0.8rem; color: var(--alert);">Refuser</button>
                              <?php endif; ?>
                              <?php if ($e['id_demandeur'] == $id_user): ?>
                                <button onclick="modifierEchange(<?= $e['id_echange'] ?>, '<?= date('Y-m-d', strtotime($e['date_demande'])) ?>', '<?= htmlspecialchars($e['message'] ?? '') ?>', <?= $e['id_offre_demandeur'] ?>, <?= $e['id_offre_offreur'] ?>)" class="ss-btn ss-btn-ghost" style="padding: 4px 10px; font-size: 0.8rem; color: var(--green-dark);">Modifier</button>
                              <?php endif; ?>
                            <?php endif; ?>
                            <button onclick="handleEchangeAction(<?= $e['id_echange'] ?>, 'delete')" class="ss-btn ss-btn-ghost" style="padding: 4px 10px; font-size: 0.8rem; color: var(--alert);"><?= ($e['statut'] === 'en_attente' && $e['id_demandeur'] == $id_user) ? 'Annuler' : 'Supprimer' ?></button>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; else: ?>
                      <tr><td colspan="8">Aucun échange trouvé.</td></tr>
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
  <form id="hiddenUpdateForm" method="post" action="OffreController.php?action=update" style="display:none;">
    <input type="hidden" name="id_offre" id="updateId">
    <input type="hidden" name="nouvelle_quantite" id="updateQty">
  </form>
  <form id="hiddenEchangeUpdateForm" method="post" action="EchangeController.php?action=update&source=front" style="display:none;">
    <input type="hidden" name="id_echange" id="upEchId">
    <input type="hidden" name="date_proposee" id="upEchDate">
    <input type="hidden" name="message" id="upEchMsg">
  </form>
  <form id="hiddenEchangeForm" method="post" action="EchangeController.php?action=traiter&source=front" style="display:none;">
    <input type="hidden" name="id_echange" id="echId">
    <input type="hidden" name="decision" id="echDecision">
  </form>
  <form id="hiddenDeleteOffreForm" method="post" action="OffreController.php?action=delete" style="display:none;">
    <input type="hidden" name="id_offre" id="deleteOffreId">
  </form>

  <!-- NutriModal Structure -->
  <div id="nutriModal" class="nutri-modal-overlay">
    <div class="nutri-modal-card">
      <div id="modalTitle" class="nutri-modal-title">Confirmation</div>
      <div id="modalBody" class="nutri-modal-body">Description...</div>
      <div id="modalPrompt" style="display:none;">
        <div id="promptFields">
          <div id="extraFields" style="display:none; margin-bottom:15px; text-align:left;">
            <label style="font-size:0.8rem; color:var(--muted);">Votre offre :</label>
            <select id="modalSelectOD" class="nutri-modal-input" style="margin-bottom:10px;"></select>
            <label style="font-size:0.8rem; color:var(--muted);">Offre convoitée :</label>
            <select id="modalSelectOO" class="nutri-modal-input" style="margin-bottom:10px;"></select>
          </div>
          <label id="labelMsg" style="font-size:0.8rem; color:var(--muted); display:none;">Message :</label>
          <input type="text" id="modalInput" class="nutri-modal-input" placeholder="Valeur...">
          
          <div id="modalLocationFields" style="display:none; margin-top:10px;">
            <label style="font-size:0.8rem; color:var(--muted);">Ville :</label>
            <input type="text" id="modalLocInput" class="nutri-modal-input" placeholder="Ex: Tunis...">
            <input type="hidden" id="modalLatInput">
            <input type="hidden" id="modalLngInput">
            <div id="modalMap" style="height: 150px; width: 100%; margin-top:10px; border-radius:8px; border:1px solid var(--border);"></div>
          </div>

          <label id="labelDate" style="font-size:0.8rem; color:var(--muted); display:none; margin-top:10px;">Date :</label>
          <input type="text" id="modalDateInput" class="nutri-modal-input" style="display:none; margin-top:5px;" placeholder="YYYY-MM-DD">
        </div>
      </div>
      <div class="nutri-modal-footer">
        <button id="modalBtnCancel" class="ss-btn ss-btn-ghost">Annuler</button>
        <button id="modalBtnOk" class="ss-btn ss-btn-primary">Continuer</button>
      </div>
    </div>
  </div>

  <div id="nutriToastContainer"></div>

  <script>
    const mesOffresJSON = <?= json_encode($mes_offres_actives ?? []) ?>;
    const offresDispoJSON = <?= json_encode($offres_disponibles ?? []) ?>;
    let modalCallback = null;
    let modalMap = null, modalMarker = null;

    function showNutriModal(title, message, isPrompt, callback, dateVal = null, showOffers = false, showLoc = false, currentLoc = '', currentLat = null, currentLng = null) {
      document.getElementById('modalTitle').innerText = title;
      document.getElementById('modalBody').innerText = message;
      document.getElementById('modalPrompt').style.display = isPrompt ? 'block' : 'none';
      
      const labelMsg = document.getElementById('labelMsg');
      labelMsg.innerText = (title.includes("Quantité") || title.includes("Offre")) ? "Quantité (kg) :" : "Message :";
      labelMsg.style.display = isPrompt ? 'block' : 'none';

      const modalDate = document.getElementById('modalDateInput');
      document.getElementById('labelDate').style.display = dateVal ? 'block' : 'none';
      
      if(dateVal) {
        modalDate.style.display = 'block';
        modalDate.value = dateVal;
      } else {
        modalDate.style.display = 'none';
      }

      document.getElementById('extraFields').style.display = showOffers ? 'block' : 'none';
      document.getElementById('modalLocationFields').style.display = showLoc ? 'block' : 'none';

      if(showLoc) {
        document.getElementById('modalLocInput').value = currentLoc;
        document.getElementById('modalLatInput').value = currentLat || '';
        document.getElementById('modalLngInput').value = currentLng || '';
        
        setTimeout(() => {
            if (!modalMap) {
                modalMap = L.map('modalMap').setView([currentLat || 36.8065, currentLng || 10.1815], 10);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(modalMap);
                modalMap.on('click', (e) => {
                    const lat = e.latlng.lat;
                    const lng = e.latlng.lng;
                    if (modalMarker) modalMarker.setLatLng(e.latlng);
                    else modalMarker = L.marker(e.latlng).addTo(modalMap);
                    document.getElementById('modalLatInput').value = lat;
                    document.getElementById('modalLngInput').value = lng;
                });
                
                // Geocoding in modal
                document.getElementById('modalLocInput').addEventListener('input', function() {
                    const q = this.value;
                    if(q.length < 3) return;
                    clearTimeout(this.t);
                    this.t = setTimeout(async () => {
                        const r = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}`);
                        const d = await r.json();
                        if(d && d.length > 0) {
                            const lat = d[0].lat, lon = d[0].lon;
                            modalMap.setView([lat, lon], 12);
                            if(modalMarker) modalMarker.setLatLng([lat, lon]);
                            else modalMarker = L.marker([lat, lon]).addTo(modalMap);
                            document.getElementById('modalLatInput').value = lat;
                            document.getElementById('modalLngInput').value = lon;
                        }
                    }, 800);
                });
            } else {
                modalMap.invalidateSize();
                if (currentLat && currentLng) {
                    modalMap.setView([currentLat, currentLng], 12);
                    if (modalMarker) modalMarker.setLatLng([currentLat, currentLng]);
                    else modalMarker = L.marker([currentLat, currentLng]).addTo(modalMap);
                }
            }
        }, 200);
      }

      if(isPrompt) document.getElementById('modalInput').value = '';
      
      const modal = document.getElementById('nutriModal');
      modal.classList.add('active');
      modalCallback = callback;
    }

    document.getElementById('modalBtnOk').onclick = function() {
      const isPrompt = document.getElementById('modalPrompt').style.display === 'block';
      const isDate = document.getElementById('modalDateInput').style.display === 'block';
      const isOffers = document.getElementById('extraFields').style.display === 'block';
      const isLoc = document.getElementById('modalLocationFields').style.display === 'block';
      
      // Perform manual validation (No HTML5)
      if (!validateModalInputs(isPrompt, isDate, isOffers, isLoc)) return;

      let res = true;
      if (isLoc) {
        res = {
            qty: document.getElementById('modalInput').value,
            loc: document.getElementById('modalLocInput').value,
            lat: document.getElementById('modalLatInput').value,
            lng: document.getElementById('modalLngInput').value
        };
      } else if (isOffers) {
        res = { 
          msg: document.getElementById('modalInput').value, 
          date: document.getElementById('modalDateInput').value,
          id_od: document.getElementById('modalSelectOD').value,
          id_oo: document.getElementById('modalSelectOO').value
        };
      } else if (isPrompt && isDate) {
        res = { msg: document.getElementById('modalInput').value, date: document.getElementById('modalDateInput').value };
      } else if (isPrompt) {
        res = document.getElementById('modalInput').value;
      }
      
      closeNutriModal();
      if(modalCallback) modalCallback(res);
    };

    document.getElementById('modalBtnCancel').onclick = closeNutriModal;
    function closeNutriModal() { document.getElementById('nutriModal').classList.remove('active'); }

    // Validation helper for modals
    function validateModalInputs(isPrompt, isDate, isOffers, isLoc) {
      let isValid = true;
      const title = document.getElementById('modalTitle').innerText;
      
      // Reset borders
      document.querySelectorAll('.nutri-modal-input').forEach(i => i.style.borderColor = '');

      if (isPrompt) {
        const input = document.getElementById('modalInput');
        if (input.value.trim() === '') {
            input.style.borderColor = 'var(--alert)';
            isValid = false;
        } else if ((title.includes("Offre") || title.includes("Quantité"))) {
            if (isNaN(input.value) || parseFloat(input.value) <= 0) {
                input.style.borderColor = 'var(--alert)';
                showToast("Veuillez entrer une quantité valide.", "error");
                isValid = false;
            }
        }
      }

      if (isLoc) {
        const locInput = document.getElementById('modalLocInput');
        if (locInput.value.trim() === '') {
            locInput.style.borderColor = 'var(--alert)';
            isValid = false;
        }
      }

      if (isDate) {
        const dateInput = document.getElementById('modalDateInput');
        if (dateInput.value.trim() === '') {
            dateInput.style.borderColor = 'var(--alert)';
            isValid = false;
        }
      }

      if (isOffers) {
        if (document.getElementById('modalSelectOD').value === '' || document.getElementById('modalSelectOO').value === '') {
            isValid = false;
        }
      }
      
      if (!isValid) {
        showToast("Veuillez remplir correctement tous les champs.", "error");
      }
      return isValid;
    }

    // Toast logic
    function showToast(msg, type = 'success') {
      const container = document.getElementById('nutriToastContainer');
      const toast = document.createElement('div');
      toast.className = 'nutri-toast' + (type === 'error' ? ' nutri-toast--error' : '');
      toast.innerText = msg;
      container.appendChild(toast);
      setTimeout(() => toast.classList.add('active'), 10);
      setTimeout(() => {
        toast.classList.remove('active');
        setTimeout(() => toast.remove(), 400);
      }, 4000);
    }

    // AJAX Helper
    async function nutriAJAX(url, formData, onSuccess) {
      try {
        const response = await fetch(url + (url.includes('?') ? '&' : '?') + 'ajax=1', {
          method: 'POST',
          body: formData
        });
        const res = await response.json();
        if (res.status === 'success') {
          showToast(res.message);
          if (onSuccess) onSuccess(res);
        } else {
          showToast(res.message, 'error');
        }
      } catch (e) {
        showToast("Erreur de connexion serveur.", 'error');
      }
    }

    // Logic Functions
    function modifierOffre(id, currentQty, currentLoc, currentLat, currentLng) {
      showNutriModal("Modifier Offre", "Mettez à jour la quantité et la localisation de votre offre :", true, (res) => {
        if (res && (res.qty != currentQty || res.loc != currentLoc || res.lat != currentLat)) {
          const fd = new FormData();
          fd.append('id_offre', id);
          fd.append('nouvelle_quantite', res.qty);
          fd.append('localisation', res.loc);
          fd.append('latitude', res.lat);
          fd.append('longitude', res.lng);
          
          nutriAJAX('OffreController.php?action=update', fd, () => {
            const row = document.getElementById('row-offre-' + id);
            if (row) {
              const qtyCell = row.querySelector('.qty-cell');
              if (qtyCell) qtyCell.innerText = res.qty + " kg";
              // Update text in localisation cell if needed (it's the 4th cell)
              const locCell = row.querySelectorAll('td')[3];
              if (locCell) locCell.innerText = res.loc;
            }
            setTimeout(() => window.location.reload(), 1000); // Reload to update map
          });
        }
      }, null, false, true, currentLoc, currentLat, currentLng);
      document.getElementById('modalInput').value = currentQty;
    }

    function supprimerOffre(id) {
      showNutriModal("Supprimer Offre", "Voulez-vous vraiment supprimer cet ingrédient ?", false, (confirm) => {
        if(confirm) {
          const fd = new FormData();
          fd.append('id_offre', id);
          nutriAJAX('OffreController.php?action=delete', fd, () => {
            const row = document.getElementById('row-offre-' + id);
            if (row) row.style.opacity = '0';
            setTimeout(() => { if(row) row.remove(); }, 400);
          });
        }
      });
    }

    function modifierEchange(id, currentDate, currentMsg, id_od, id_oo) {
      // Populate dropdowns
      const selOD = document.getElementById('modalSelectOD');
      const selOO = document.getElementById('modalSelectOO');
      selOD.innerHTML = mesOffresJSON.map(o => `<option value="${o.id_offre}" ${o.id_offre == id_od ? 'selected' : ''}>OFF-${o.id_offre} : ${o.ingredient}</option>`).join('');
      selOO.innerHTML = offresDispoJSON.map(o => `<option value="${o.id_offre}" ${o.id_offre == id_oo ? 'selected' : ''}>OFF-${o.id_offre} : ${o.ingredient}</option>`).join('');

      showNutriModal("Modifier Échange", "Mettez à jour les détails de cet échange :", true, (res) => {
        if(res && res.date && res.id_od !== undefined && res.id_oo !== undefined) {
          const fd = new FormData();
          fd.append('id_echange', id);
          fd.append('date_proposee', res.date);
          fd.append('message', res.msg);
          fd.append('id_offre_demandeur', res.id_od);
          fd.append('id_offre_offreur', res.id_oo);
          nutriAJAX('EchangeController.php?action=update&source=front', fd, () => {
             const row = document.getElementById('row-ech-' + id);
             if (row) {
                // Message
                const msgEl = row.querySelector('.msg-cell small');
                if (msgEl) msgEl.innerText = res.msg || '-';
                
                // Date
                const dateEl = row.querySelector('.date-cell');
                if (dateEl && res.date) {
                   const d = res.date.split('-');
                   if(d.length === 3) dateEl.innerText = `${d[2]}/${d[1]}/${d[0]}`;
                }
                
                // Noms ingrédients
                const selOD = document.getElementById('modalSelectOD');
                const selOO = document.getElementById('modalSelectOO');
                const donneEl = row.querySelector('.donne-cell');
                const recuEl = row.querySelector('.recu-cell');
                if (donneEl) donneEl.innerText = selOD.options[selOD.selectedIndex].text;
                if (recuEl) recuEl.innerText = selOO.options[selOO.selectedIndex].text;
             }
          });
        }
      }, currentDate, true);
      document.getElementById('modalInput').value = currentMsg;
    }

    function handleEchangeAction(id, action) {
      let title = action === 'accepte' ? "Accepter l'échange" : (action === 'refuse' ? "Refuser l'échange" : "Supprimer l'échange");
      let msg = action === 'delete' ? "Voulez-vous vraiment supprimer cet échange ?" : "Voulez-vous confirmer cette action sur l'échange ECH-" + id + " ?";
      
      showNutriModal(title, msg, false, (confirm) => {
        if(confirm) {
          const fd = new FormData();
          fd.append('id_echange', id);
          fd.append('decision', action);
          let url = action === 'delete' ? 'EchangeController.php?action=delete&source=front' : 'EchangeController.php?action=traiter&source=front';
          
          nutriAJAX(url, fd, (res) => {
            const row = document.getElementById('row-ech-' + id);
            if (!row) return;
            if (action === 'delete') {
              row.style.opacity = '0';
              setTimeout(() => row.remove(), 400);
            } else {
              const badge = row.querySelector('.statut-badge');
              if (badge) {
                badge.innerText = action;
                badge.className = 'ss-badge statut-badge ' + (action === 'accepte' ? 'ok' : (action === 'refuse' ? 'alert' : 'warn'));
              }
              const actionDiv = row.querySelector('.action-buttons');
              if (actionDiv && (action === 'accepte' || action === 'refuse')) {
                actionDiv.innerHTML = `<button onclick="handleEchangeAction(${id}, 'delete')" class="ss-btn ss-btn-ghost" style="padding: 4px 10px; font-size: 0.8rem; color: var(--alert);">Supprimer</button>`;
              }
            }
          });
        }
      });
    }

    function publierDon() {
      const form = document.getElementById('formDon');
      if (!validateNutriForm(form)) return;
      
      const fd = new FormData(form);
      nutriAJAX(form.getAttribute('action'), fd, () => {
        form.reset();
      });
    }

    function prendreDon(id) {
      showNutriModal("Récupérer un don", "Voulez-vous vraiment récupérer cet ingrédient gratuitement ?", false, (confirm) => {
        if(confirm) {
          const fd = new FormData();
          fd.append('id_offre', id);
          nutriAJAX('EchangeController.php?action=prendre_don&source=front', fd, () => {
            const row = document.getElementById('row-dispo-' + id);
            if (row) row.remove();
            // Optionnel : recharger la page pour voir le nouvel échange
            setTimeout(() => window.location.reload(), 1000);
          });
        }
      });
    }

    function preparerEchange(id) {
      // 1. On change d'onglet pour aller vers "Échanges"
      const tabEchanges = document.getElementById('vue-echanges');
      if (tabEchanges) tabEchanges.checked = true;

      // 2. On sélectionne l'offre dans la liste déroulante
      const select = document.getElementById('select_offre_cible');
      if (select) {
        select.value = id;
        // 3. On scrolle vers le formulaire
        setTimeout(() => {
          document.getElementById('ech-demande').scrollIntoView({ behavior: 'smooth' });
          // 4. Petit effet visuel
          select.style.boxShadow = '0 0 10px var(--green-light)';
          setTimeout(() => select.style.boxShadow = '', 2000);
        }, 100); // Petit délai pour laisser l'onglet s'afficher
      }
    }

    // Initialize row opacity for animations
    document.querySelectorAll('tr[id^="row-"]').forEach(tr => tr.style.transition = 'opacity 0.4s ease');

    // --- Auto-hide Messages & Clean URL ---
    const autoHideDiv = document.querySelector('.nutri-auto-hide');
    if (autoHideDiv && autoHideDiv.innerText.trim() !== '') {
      setTimeout(() => {
        autoHideDiv.style.transition = 'opacity 1s ease, transform 1s ease';
        autoHideDiv.style.opacity = '0';
        autoHideDiv.style.transform = 'translateY(-10px)';
        setTimeout(() => autoHideDiv.remove(), 1000);
      }, 4000);

      // Clean URL parameters without reloading
      const url = new URL(window.location);
      url.searchParams.delete('success');
      url.searchParams.delete('error');
      window.history.replaceState({}, document.title, url);
    }

    // ============================================================
    // INNOVATION MÉTIER : INTERFACE GÉOSPATIALE (Leaflet.js)
    // ============================================================

    /**
     * CARTE 1 : Sélection de l'emplacement (Ajout d'Offre)
     * Cette carte permet à l'utilisateur de définir précisément où se trouve l'ingrédient.
     */
    const selectionMap = L.map('selectionMap').setView([36.8065, 10.1815], 6); // Centré par défaut sur la Tunisie
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(selectionMap);

    let selectionMarker = null;

    // Événement : Clic sur la carte pour placer manuellement un marqueur
    selectionMap.on('click', function(e) {
      const lat = e.latlng.lat;
      const lng = e.latlng.lng;
      
      if (selectionMarker) {
        selectionMarker.setLatLng(e.latlng);
      } else {
        selectionMarker = L.marker(e.latlng).addTo(selectionMap);
      }
      
      // On injecte les coordonnées dans des champs cachés pour le formulaire PHP
      document.getElementById('latInput').value = lat;
      document.getElementById('lngInput').value = lng;
      showToast(`Position définie : ${lat.toFixed(4)}, ${lng.toFixed(4)}`);
    });

    /**
     * GÉOCODAGE EN TEMPS RÉEL (API Nominatim)
     * Lorsque l'utilisateur tape le nom d'une ville, nous interrogeons un service externe
     * pour convertir ce texte en coordonnées GPS (Latitude/Longitude).
     */
    let geocodeTimeout;
    const villeInput = document.getElementById('villeInput');
    if (villeInput) {
        villeInput.addEventListener('input', function() {
          clearTimeout(geocodeTimeout);
          const query = this.value;
          if (query.length < 3) return; // On attend au moins 3 caractères

          geocodeTimeout = setTimeout(async () => {
            try {
              // Appel asynchrone à l'API gratuite Nominatim (OpenStreetMap)
              const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`);
              const data = await response.json();
              if (data && data.length > 0) {
                const result = data[0];
                const lat = parseFloat(result.lat);
                const lon = parseFloat(result.lon);
                
                // On déplace la carte vers le résultat trouvé
                selectionMap.setView([lat, lon], 12);
                
                if (selectionMarker) {
                  selectionMarker.setLatLng([lat, lon]);
                } else {
                  selectionMarker = L.marker([lat, lon]).addTo(selectionMap);
                }
                // Mise à jour des champs de formulaire
                document.getElementById('latInput').value = lat;
                document.getElementById('lngInput').value = lon;
              }
            } catch (error) {
              console.error("Erreur de géocodage:", error);
            }
          }, 800); // "Debounce" pour ne pas saturer l'API lors de la frappe
        });
    }

    /**
     * CARTE 2 : Visualisation Communautaire (Découverte)
     * Affiche toutes les offres du réseau NutriVerse sur une carte globale.
     */
    const globalOffresMap = L.map('globalOffresMap').setView([36.8065, 10.1815], 7);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(globalOffresMap);

    // Fonctionnalité : Bouton "Ma position" (Géolocalisation du navigateur)
    const geoControl = L.control({ position: 'topleft' });
    geoControl.onAdd = function() {
      const btn = L.DomUtil.create('button', 'ss-btn ss-btn-primary');
      btn.innerHTML = '📍 Ma position';
      btn.style.marginTop = '10px';
      btn.style.padding = '5px 10px';
      btn.style.fontSize = '0.7rem';
      btn.style.cursor = 'pointer';
      btn.onclick = function(e) {
        e.preventDefault();
        globalOffresMap.locate({ setView: true, maxZoom: 14 });
      };
      return btn;
    };
    geoControl.addTo(globalOffresMap);

    globalOffresMap.on('locationfound', function(e) {
      L.marker(e.latlng).addTo(globalOffresMap).bindPopup("Vous êtes ici").openPopup();
    });

    /**
     * CHARGEMENT DES MARQUEURS DEPUIS LA BASE DE DONNÉES
     * On convertit les données PHP (provenant de MySQL) en objets JSON pour les afficher.
     */
    if (offresDispoJSON && offresDispoJSON.length > 0) {
      const markers = [];
      const markerObjects = []; 

      offresDispoJSON.forEach(o => {
        if (o.latitude && o.longitude) {
          const m = L.marker([o.latitude, o.longitude]).addTo(globalOffresMap);
          
          // Contenu riche de la Popup (HTML dynamique)
          const popupContent = `
            <div style="font-family: Poppins, sans-serif;">
              <strong style="color: var(--green-dark);">${o.ingredient}</strong><br>
              <small>${o.quantite} ${o.unite_mesure} - ${o.localisation}</small><br>
              <span class="ss-badge ${o.type_offre === 'don' ? 'ok' : 'warn'}" style="margin-top:5px; display:inline-block;">${o.type_offre}</span><br>
              <button onclick="preparerEchange(${o.id_offre})" class="ss-btn ss-btn-primary" style="padding: 2px 8px; font-size: 0.7rem; margin-top: 8px; width:100%;">
                ${o.type_offre === 'don' ? 'Récupérer' : 'Échanger'}
              </button>
            </div>
          `;
          m.bindPopup(popupContent);
          markers.push([o.latitude, o.longitude]);
          markerObjects.push({ marker: m, data: o });
        }
      });
      
      // Ajustement automatique du zoom pour voir TOUTES les offres
      if (markers.length > 0) {
        globalOffresMap.fitBounds(markers, { padding: [50, 50] });
      }

      /**
       * FILTRAGE CROISÉ : Recherche Textuelle + Mise à jour de la Carte
       * Lorsque l'utilisateur cherche un aliment, la carte "filtre" les marqueurs en temps réel.
       */
      const searchInput = document.getElementById('globalSearchInput');
      if (searchInput) {
        searchInput.addEventListener('input', function() {
          const query = this.value.toLowerCase();
          
          // 1. On filtre les lignes du tableau
          const tableRows = document.querySelectorAll('tr[id^="row-dispo-"]');
          tableRows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
          });

          // 2. On filtre les marqueurs sur la carte
          const visibleMarkers = [];
          markerObjects.forEach(item => {
            const match = item.data.ingredient.toLowerCase().includes(query) || 
                        item.data.localisation.toLowerCase().includes(query);
            
            if (match) {
              item.marker.addTo(globalOffresMap);
              visibleMarkers.push(item.marker.getLatLng());
            } else {
              globalOffresMap.removeLayer(item.marker);
            }
          });

          // Recentre la carte si des résultats sont trouvés
          if (visibleMarkers.length > 0 && query.length > 0) {
            globalOffresMap.fitBounds(visibleMarkers, { padding: [50, 50], maxZoom: 13 });
          }
        });
      }
    }

    /**
     * FONCTION DE NAVIGATION : Zoom vers une offre depuis le tableau
     * Permet une interaction bidirectionnelle entre la liste et la carte.
     */
    function zoomToGlobalOffer(lat, lng) {
      if (!globalOffresMap) return;
      
      const tabOffres = document.getElementById('vue-offres');
      if (tabOffres) tabOffres.checked = true;

      globalOffresMap.setView([lat, lng], 14);
      
      globalOffresMap.eachLayer(function (layer) {
        if (layer instanceof L.Marker) {
          const pos = layer.getLatLng();
          if (Math.abs(pos.lat - lat) < 0.0001 && Math.abs(pos.lng - lng) < 0.0001) {
            layer.openPopup();
          }
        }
      });

      document.getElementById('offres-dispo').scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  </script>
</body>
</html>
