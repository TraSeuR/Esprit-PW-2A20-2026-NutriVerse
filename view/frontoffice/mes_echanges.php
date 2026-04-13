<?php if(session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriVerse &mdash; Offre d'ingrédients & Échanges (utilisateur)</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="stylesheet" href="../view/frontoffice/assets/offre-echange-utilisateur.css" />
  <script src="../view/frontoffice/assets/js/validation.js" defer></script>
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
      <?php if (!empty($error)) echo "<div style='color:red; background:#ffebeb; padding:10px; margin-bottom:15px; border-radius:5px;'>$error</div>"; ?>
      <?php if (!empty($success)) echo "<div style='color:green; background:#ebffef; padding:10px; margin-bottom:15px; border-radius:5px;'>$success</div>"; ?>
      
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
                  <thead><tr><th>ID</th><th>Ingrédient</th><th>Quantité</th><th>Lieu</th><th>Type</th></tr></thead>
                  <tbody>
                    <?php if (!empty($offres)): foreach ($offres as $o): ?>
                      <tr>
                        <td>OFF-<?= htmlspecialchars($o['id_offre']) ?></td>
                        <td><?= htmlspecialchars($o['ingredient']) ?></td>
                        <td><?= htmlspecialchars($o['quantite'] . ' ' . $o['unite_mesure']) ?></td>
                        <td><?= htmlspecialchars($o['localisation']) ?></td>
                        <td><span class="ss-badge"><?= htmlspecialchars($o['type_offre']) ?></span></td>
                      </tr>
                    <?php endforeach; else: ?>
                      <tr><td colspan="5">Aucune offre publiée. Ajoutez-en une ci-dessous !</td></tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </article>

            <div class="sss-grid-2 ss-grid-2">
              <article class="sss-card ss-card">
                <h3>Ajouter une offre</h3>
                <form method="post" action="OffreController.php?action=add" class="sss-form-grid ss-form-grid">
                  <div class="sss-field ss-field"><label>Ingrédient</label><input type="text" name="ingredient" placeholder="Tomates bio" /></div>
                  <div class="sss-field ss-field"><label>Quantité</label><input type="text" name="quantite" placeholder="5" /></div>
                  <div class="sss-field ss-field"><label>Ville</label><input type="text" name="ville" placeholder="Tunis" /></div>
                  <button type="submit" class="ss-btn ss-btn-primary">Publier l'offre</button>
                </form>
              </article>
              <article class="sss-card ss-card">
                <h3>Modifier / supprimer une offre</h3>
                <form method="post" action="OffreController.php?action=update" class="sss-form-grid ss-form-grid">
                  <div class="sss-field ss-field"><label>ID offre</label><input type="text" name="id_offre" placeholder="OFF-18" /></div>
                  <div class="sss-field ss-field"><label>Nouvelle quantité</label><input type="text" name="nouvelle_quantite" /></div>
                  <button type="submit" class="ss-btn ss-btn-outline">Modifier l'offre</button>
                </form>
                <form method="post" action="OffreController.php?action=delete" onsubmit="return confirm('Voulez-vous vraiment supprimer cette offre ?')" class="sss-form-grid ss-form-grid" style="margin-top: 14px">
                  <input type="text" name="id_offre" placeholder="ID offre à supprimer" style="margin-bottom:10px;" />
                  <button type="submit" class="ss-btn ss-btn-ghost">Supprimer l'offre</button>
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
              <form method="post" action="DonController.php?action=add" class="sss-form-grid ss-form-grid">
                <div class="sss-field ss-field"><label>Description du don</label><textarea name="description" rows="3" placeholder="Ex. : surplus de tomates"></textarea></div>
                <div class="sss-field ss-field"><label><input type="checkbox" name="don_solidaire" value="1" /> Cette offre est un don (gratuit)</label></div>
                <button type="submit" class="ss-btn ss-btn-primary">Publier en don</button>
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
              <form method="post" action="NoteController.php?action=add" class="sss-form-grid ss-form-grid">
                <div class="sss-field ss-field"><label>ID de l'échange</label><input type="text" name="id_echange" /></div>
                <div class="sss-field ss-field"><label>Votre rôle</label>
                  <select name="role"><option value="demandeur">Demandeur</option><option value="offreur">Offreur</option></select>
                </div>
                <div class="sss-field ss-field"><label>Note</label>
                  <select name="note"><option value="5">⭐⭐⭐⭐⭐</option><option value="4">⭐⭐⭐⭐</option><option value="3">⭐⭐⭐</option><option value="2">⭐⭐</option><option value="1">⭐</option></select>
                </div>
                <button type="submit" class="ss-btn ss-btn-outline">Évaluer</button>
              </form>
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
            <div class="sss-grid-2 ss-grid-2">
              <article class="sss-card ss-card">
                <h3>Envoyer une demande</h3>
                <form method="post" action="EchangeController.php?action=add&source=front" class="sss-form-grid ss-form-grid">
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
                  <div class="sss-field ss-field"><label>Date</label><input type="date" name="date_proposee" value="<?= date('Y-m-d') ?>" /></div>
                  <button type="submit" class="ss-btn ss-btn-primary">Lancer l'échange</button>
                </form>
              </article>
              <article class="sss-card ss-card">
                <h3>Traiter les demandes reçues</h3>
                <form method="post" action="EchangeController.php?action=traiter&source=front" class="sss-form-grid ss-form-grid">
                  <div class="sss-field ss-field"><label>ID échange</label><input type="text" name="id_echange" /></div>
                  <div class="sss-field ss-field"><label>Décision</label>
                    <select name="decision"><option value="accepte">Accepter</option><option value="refuse">Refuser</option></select>
                  </div>
                  <button type="submit" class="ss-btn ss-btn-outline">Valider</button>
                </form>
                <form method="post" action="EchangeController.php?action=delete&source=front" onsubmit="return confirm('Voulez-vous vraiment annuler cet échange ?')" class="sss-form-grid ss-form-grid" style="margin-top: 14px">
                  <input type="text" name="id_echange" placeholder="ID échange à annuler" />
                  <button type="submit" class="ss-btn ss-btn-ghost">Annuler l'échange</button>
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
                  <thead><tr><th>ID échange</th><th>Donné</th><th>Reçu</th><th>Statut</th></tr></thead>
                  <tbody>
                    <?php if (!empty($echanges)): foreach ($echanges as $e): ?>
                      <tr>
                        <td>ECH-<?= htmlspecialchars($e['id_echange']) ?></td>
                        <td>OFF-<?= $e['id_offre_demandeur'] ?> (<?= htmlspecialchars($e['ing_donne']) ?>)</td>
                        <td>OFF-<?= $e['id_offre_offreur'] ?> (<?= htmlspecialchars($e['ing_recu']) ?>)</td>
                        <td><span class="ss-badge <?= ($e['statut'] == 'accepte') ? 'ok' : 'warn' ?>"><?= htmlspecialchars($e['statut']) ?></span></td>
                      </tr>
                    <?php endforeach; else: ?>
                      <tr><td colspan="4">Aucun échange trouvé.</td></tr>
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
</body>
</html>
