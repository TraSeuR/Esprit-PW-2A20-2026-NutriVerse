<?php if(session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriVerse &mdash; Offre d&apos;ingr&eacute;dients &amp; &Eacute;changes (administration)</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../view/backoffice/assets/back.css" />
  <link rel="stylesheet" href="../view/backoffice/assets/offre-echange-admin.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="../../public/js/validation.js" defer></script>
  <script>
    function valider_ad(e) {
      if (!document.getElementById('ech-id').value.trim() || !document.getElementById('ech-decision').value.trim()) {
        alert('ID et Décision requis.'); e.preventDefault(); return false;
      }
    }
    function v_admin_del(e, form) {
      let f = form.querySelector('[name=id_echange]').value.trim();
      if (!f) { alert('Identifiant requis pour nettoyer.'); e.preventDefault(); return false; }
    }
  </script>
</head>
<body>
  <input type="checkbox" id="ssa-sidebar-toggle" />
  <label for="ssa-sidebar-toggle" class="ssa-scrim" aria-label="Fermer le menu de navigation"></label>

  <aside class="sidebar" id="sidebar" aria-label="Navigation administration">
    <div class="sidebar-top">
      <div class="brand">
        <img src="../view/backoffice/images/logo.png" alt="Logo NutriVerse" class="brand-logo" width="58" height="58" />
        <div>
          <h2>NutriVerse</h2>
          <p>Back Office</p>
        </div>
      </div>
      <label for="ssa-sidebar-toggle" class="close-sidebar" aria-label="Fermer le menu">&times;</label>
    </div>

    <nav class="sidebar-menu" aria-label="Menu principal">
      <a href="../view/backoffice/back.html" class="menu-item"><span class="menu-item-icon" aria-hidden="true">&#9638;</span><span>Dashboard</span></a>
      <a href="#" class="menu-item"><span class="menu-item-icon" aria-hidden="true">&#9671;</span><span>Recettes</span></a>
      <a href="#" class="menu-item"><span class="menu-item-icon" aria-hidden="true">&#9671;</span><span>Utilisateurs</span></a>
      <a href="#" class="menu-item"><span class="menu-item-icon" aria-hidden="true">&#9671;</span><span>Produits</span></a>
      <a href="#" class="menu-item"><span class="menu-item-icon" aria-hidden="true">&#9671;</span><span>Commandes</span></a>
      <a href="AdminController.php?action=list" class="menu-item active"><span class="menu-item-icon" aria-hidden="true">&#8644;</span><span>Offres &amp; &eacute;changes</span></a>
      <a href="#" class="menu-item"><span class="menu-item-icon" aria-hidden="true">&#9671;</span><span>Programmes</span></a>
      <a href="#" class="menu-item"><span class="menu-item-icon" aria-hidden="true">&#9671;</span><span>Param&egrave;tres</span></a>
    </nav>

    <div class="sidebar-footer"><p>&copy; 2026 NutriVerse</p></div>
  </aside>

  <div class="main-content">
    <header class="topbar">
      <div class="topbar-left">
        <label for="ssa-sidebar-toggle" class="menu-btn" aria-label="Ouvrir le menu">&#9776;</label>
        <div class="search-box">
          <span aria-hidden="true" style="opacity: 0.55; font-size: 1.1rem">&#8982;</span>
          <input type="search" placeholder="Rechercher offre, &eacute;change, utilisateur&hellip;" autocomplete="off" />
        </div>
      </div>
      <div class="topbar-right">
        <form method="post" action="php/admin_deconnexion.php"><button type="submit" class="ssa-btn ssa-btn-outline">D&eacute;connexion</button></form>
        <div class="admin-box"><div class="admin-avatar" aria-hidden="true">A</div><div><h4>Admin</h4><p>Administrateur</p></div></div>
      </div>
    </header>

    <main class="dashboard-content" id="contenu-principal">
      <div class="nutri-auto-hide">
        <?php if (!empty($error)) echo "<div style='color:red; background:#ffebeb; padding:10px; margin-bottom:15px; border-radius:5px;'>$error</div>"; ?>
        <?php if (!empty($success)) echo "<div style='color:green; background:#ebffef; padding:10px; margin-bottom:15px; border-radius:5px;'>$success</div>"; ?>
      </div>
      <div>
        <span class="ssa-badge">Module OffreIngredient &amp; &Eacute;change</span>
        <p style="color: var(--muted); margin-top: 8px; max-width: 780px">
          Gestion des entit&eacute;s <strong>OffreIngredient</strong> et <strong>&Eacute;change</strong> : recherche par localisation (zone + rayon), dons solidaires et <strong>note moyenne par utilisateur</strong> (sur 5, apr&egrave;s les &eacute;changes). Une offre admet 0..* &eacute;changes ; un &eacute;change r&eacute;f&eacute;rence une seule offre.
        </p>
      </div>

      <?php $active_tab = $active_tab ?? 'offres'; ?>
      <input type="radio" name="adm-vue-module" id="adm-vue-offres" class="adm-vue-input" <?= $active_tab === 'offres' ? 'checked' : '' ?> hidden />
      <input type="radio" name="adm-vue-module" id="adm-vue-echanges" class="adm-vue-input" <?= $active_tab === 'echanges' ? 'checked' : '' ?> hidden />

      <div class="adm-vue-tab-bar vue-tab-bar" role="tablist" aria-label="Administration Offres ou &Eacute;changes">
        <label for="adm-vue-offres" class="vue-tab-btn" role="tab">OffreIngredient</label>
        <label for="adm-vue-echanges" class="vue-tab-btn" role="tab">&Eacute;change</label>
      </div>

      <div class="adm-vue-panel adm-vue-panel--offres vue-panel vue-panel--offres">
        <section class="ssa-stats" aria-labelledby="stats-offres-title">
          <h2 id="stats-offres-title" class="visually-hidden">Indicateurs offres</h2>
          <article class="ssa-stat-card"><div><p>Offres actives</p><h2><?= $stats['actives'] ?? '0' ?></h2><p style="color: var(--success)">Disponible</p></div><div class="ssa-stat-icon green" role="presentation"></div></article>
          <article class="ssa-stat-card"><div><p>Dons solidaires</p><h2><?= $stats['dons'] ?? '0' ?></h2><p>Mode don</p></div><div class="ssa-stat-icon orange" role="presentation"></div></article>
          <article class="ssa-stat-card"><div><p>Offres bloquées</p><h2><?= $stats['bloquees'] ?? '0' ?></h2><p style="color: #E74C3C">Modération</p></div><div class="ssa-stat-icon orange" role="presentation" style="filter: hue-rotate(320deg);"></div></article>
          <article class="ssa-stat-card"><div><p>Échanges en attente</p><h2><?= $stats['attente'] ?? '0' ?></h2><p>Traitement</p></div><div class="ssa-stat-icon blue" role="presentation"></div></article>
        </section>

        <div class="ssa-grid-2">
          <section class="ssa-card" aria-labelledby="offres-admin-title" style="flex: 1.5;">
            <div class="ssa-card-header" style="flex-wrap: wrap; gap: 10px;">
              <div>
                <h3 id="offres-admin-title">OffreIngredient &mdash; Supervision</h3>
                <form method="get" action="AdminController.php" style="display:flex; gap:5px; margin-top:8px;">
                  <input type="hidden" name="action" value="list" />
                  <select name="filtre_etat" style="font-size: 0.75rem;"><option value="Toutes">Tous États</option><option value="disponible" <?= ($f_etat == 'disponible') ? 'selected' : '' ?>>Disponible</option><option value="bloqué" <?= ($f_etat == 'bloqué') ? 'selected' : '' ?>>Bloqué</option></select>
                  <select name="filtre_type" style="font-size: 0.75rem;"><option value="Tous">Tous Types</option><option value="échange" <?= ($f_type == 'échange') ? 'selected' : '' ?>>Échange</option><option value="don" <?= ($f_type == 'don') ? 'selected' : '' ?>>Don</option></select>
                  <button type="submit" class="ssa-badge" style="border:none; cursor:pointer; margin-bottom:0; padding: 4px 10px;">Ok</button>
                </form>
              </div>
            </div>
            <div class="ssa-table-wrap">
              <table class="ssa-table">
                <thead><tr><th>ID</th><th>Ingrédient</th><th>Loc.</th><th>Statut</th><th>Type</th><th>Actions</th></tr></thead>
                <tbody>
                  <?php if(!empty($offres)): foreach($offres as $o): ?>
                  <tr id="row-adm-offre-<?= $o['id_offre'] ?>">
                    <td>OFF-<?= htmlspecialchars($o['id_offre']) ?></td>
                    <td title="Quantité: <?= $o['quantite'] ?>"><?= htmlspecialchars($o['ingredient']) ?></td>
                    <td>
                        <?= htmlspecialchars($o['localisation']) ?>
                        <?php if(!empty($o['latitude'])): ?>
                            <button onclick="zoomToOffer(<?= $o['latitude'] ?>, <?= $o['longitude'] ?>, '<?= addslashes($o['ingredient']) ?>')" class="ssa-link" style="border:none; background:none; cursor:pointer; font-size: 1rem; margin-left:5px;" title="Voir sur la carte">📍</button>
                        <?php endif; ?>
                    </td>
                    <td><span class="ssa-status <?= ($o['etat'] == 'bloqué') ? 'warn' : 'ok' ?>"><?= htmlspecialchars($o['etat']) ?></span></td>
                    <td><span class="ssa-badge" style="padding: 2px 8px; font-size: 0.7rem;"><?= htmlspecialchars($o['type_offre']) ?></span></td>
                    <td style="white-space: nowrap;">
                        <?php 
                           $is_blocked = (trim($o['etat']) === 'bloqué');
                           $next_etat = $is_blocked ? 'disponible' : 'bloqué'; 
                        ?>
                        <form method="post" action="OffreController.php?action=admin_toggle_block" style="display:inline;">
                            <input type="hidden" name="id_offre" value="<?= $o['id_offre'] ?>">
                            <input type="hidden" name="nouvel_etat" value="<?= $next_etat ?>">
                            <button type="submit" class="ssa-link" style="border:none; background:none; cursor:pointer; font-size: 0.82rem; color: <?= $is_blocked ? 'var(--green)' : 'orange' ?>;">
                                <?= $is_blocked ? 'Débloquer' : 'Bloquer' ?>
                            </button>
                        </form> |
                        <form method="post" action="OffreController.php?action=admin_delete" style="display:inline;">
                           <input type="hidden" name="id_offre" value="<?= $o['id_offre'] ?>">
                           <button type="submit" class="ssa-link" style="border:none; background:none; cursor:pointer; color:red; font-size: 0.82rem;">Suppr</button>
                        </form>
                    </td>
                  </tr>
                  <?php endforeach; endif; ?>
                </tbody>
              </table>
            </div>
          </section>

          <section class="ssa-card" aria-labelledby="notes-off-title">
            <div class="ssa-card-header"><div><h3 id="notes-off-title">Notes utilisateurs (innovation)</h3><p>Consultez la r&eacute;putation moyenne sur 5 apr&egrave;s les &eacute;changes ; d&eacute;tection des profils signal&eacute;s.</p></div></div>
            <p style="color: var(--muted); font-size: 0.92rem; line-height: 1.6;">L'administrateur a le pouvoir de <strong>bloquer</strong> les offres suspectes ou de <strong>supprimer</strong> définitivement les contenus inappropriés pour garantir la sécurité de la communauté NutriVerse.</p>
            <div style="margin-top: 20px; padding: 15px; background: rgba(11, 143, 60, 0.08); border-radius: 12px; border-left: 4px solid var(--green);">
               <p style="color: var(--green-dark); font-size: 0.85rem;"><strong>Rappel :</strong> Les blocages masquent l'offre, les suppressions sont irréversibles.</p>
            </div>
            
            <div id="adminOffreMap" style="height: 300px; width: 100%; border-radius: 12px; margin-top: 20px; border: 1px solid var(--border); box-shadow: var(--shadow-sm);"></div>
          </section>
        </div>

        <section class="ssa-card" aria-labelledby="localisation-admin-title" style="margin-bottom: 22px;">
          <div class="ssa-card-header">
            <div>
              <h3 id="localisation-admin-title">Recherche des offres par localisation</h3>
              <p>Supervision des zones les plus actives (zone + rayon).</p>
            </div>
          </div>
          <div class="ssa-bars" role="img" aria-label="Activit&eacute; par localisation">
            <div class="ssa-bar" style="height: 88%" data-label="Tunis"></div>
            <div class="ssa-bar" style="height: 76%" data-label="Nabeul"></div>
            <div class="ssa-bar" style="height: 70%" data-label="Sousse"></div>
            <div class="ssa-bar" style="height: 64%" data-label="Bizerte"></div>
            <div class="ssa-bar" style="height: 59%" data-label="Kairouan"></div>
          </div>
        </section>
      </div>

      <div class="adm-vue-panel adm-vue-panel--echanges vue-panel vue-panel--echanges">
        <section class="ssa-stats" aria-labelledby="stats-ech-title">
          <h2 id="stats-ech-title" class="visually-hidden">Indicateurs &eacute;changes</h2>
          <article class="ssa-stat-card"><div><p>Demandes d&apos;&eacute;change</p><h2><?= $stats['attente'] ?? '0' ?></h2><p>58 en attente</p></div><div class="ssa-stat-icon orange" role="presentation"></div></article>
          <article class="ssa-stat-card"><div><p>&Eacute;changes accept&eacute;s</p><h2>132</h2><p>Taux d&apos;acceptation 61 %</p></div><div class="ssa-stat-icon blue" role="presentation"></div></article>
        </section>

        <section class="ssa-card" style="margin-bottom:20px;">
            <div class="ssa-card-header"><div><h3 id="echanges-admin-list">Tous les échanges (Supervision)</h3><p>Traitez les demandes directement depuis le tableau.</p></div></div>
            <div class="ssa-table-wrap">
              <table class="ssa-table">
                <thead><tr><th>ID</th><th>Reçu (Demandeur)</th><th>Donné (Offreur)</th><th>Statut</th><th>Actions</th></tr></thead>
                <tbody>
                  <?php if(!empty($echanges)): foreach($echanges as $e): ?>
                  <tr>
                    <td>ECH-<?= htmlspecialchars($e['id_echange']) ?></td>
                    <td>
                        <strong style="color:var(--green-dark)">
                            <?= htmlspecialchars($e['ing_demande'] ?? 'OFF-'.$e['id_offre_demandeur']) ?>
                        </strong> 
                        <?php if(isset($e['ing_demande'])): ?>
                            <span style="font-size:0.8rem; opacity:0.7">(<?= htmlspecialchars($e['qte_demande'] ?? '') ?>)</span>
                        <?php else: ?>
                            <span style="font-size:0.8rem; color:red;">(Supprimée)</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong>
                            <?= htmlspecialchars($e['ing_offreur'] ?? 'OFF-'.$e['id_offre_offreur']) ?>
                        </strong> 
                        <?php if(isset($e['ing_offreur'])): ?>
                            <span style="font-size:0.8rem; opacity:0.7">(<?= htmlspecialchars($e['qte_offreur'] ?? '') ?>)</span>
                        <?php else: ?>
                            <span style="font-size:0.8rem; color:red;">(Supprimée)</span>
                        <?php endif; ?>
                    </td>
                    <td>
                       <?php 
                          $ech_is_blocked = (trim($e['statut']) === 'bloqué');
                       ?>
                       <span class="ssa-badge" style="background: <?= $ech_is_blocked ? '#e74c3c' : 'var(--green)' ?>; color:white;">
                          <?= htmlspecialchars($e['statut']) ?>
                       </span>
                    </td>
                    <td style="white-space: nowrap;">
                      <?php if($e['statut'] === 'en_attente'): ?>
                        <form method="post" action="EchangeController.php?action=traiter&source=admin" style="display:inline;">
                          <input type="hidden" name="id_echange" value="<?= $e['id_echange'] ?>">
                          <input type="hidden" name="decision" value="accepte">
                          <button type="submit" class="ssa-link" style="border:none; background:none; cursor:pointer; font-size: 0.82rem; color: var(--green);">Accepter</button>
                        </form> |
                        <form method="post" action="EchangeController.php?action=traiter&source=admin" style="display:inline;">
                          <input type="hidden" name="id_echange" value="<?= $e['id_echange'] ?>">
                          <input type="hidden" name="decision" value="refuse">
                          <button type="submit" class="ssa-link" style="border:none; background:none; cursor:pointer; font-size: 0.82rem; color: #e74c3c;">Refuser</button>
                        </form> |
                      <?php endif; ?>
                      <?php $next_st = $ech_is_blocked ? 'en_attente' : 'bloqué'; ?>
                      <form method="post" action="EchangeController.php?action=traiter&source=admin" style="display:inline;">
                          <input type="hidden" name="id_echange" value="<?= $e['id_echange'] ?>">
                          <input type="hidden" name="decision" value="<?= $next_st ?>">
                          <button type="submit" class="ssa-link" style="border:none; background:none; cursor:pointer; font-size: 0.82rem; color: <?= $ech_is_blocked ? 'var(--green)' : 'orange' ?>;">
                              <?= $ech_is_blocked ? 'Débloquer' : 'Bloquer' ?>
                          </button>
                      </form> |
                      <form method="post" action="EchangeController.php?action=delete&source=admin" style="display:inline;">
                        <input type="hidden" name="id_echange" value="<?= $e['id_echange'] ?>">
                        <button type="submit" class="ssa-link" style="border:none; background:none; cursor:pointer; color:red; font-size: 0.82rem;">Supprimer</button>
                      </form>
                    </td>
                  </tr>
                  <?php endforeach; else: ?>
                  <tr><td colspan="5">Aucun échange trouvé.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
        </section>

        <div class="ssa-grid-2">
          <section class="ssa-card" aria-labelledby="infos-ech-title">
            <div class="ssa-card-header"><div><h3 id="infos-ech-title">Supervision des flux</h3><p>Analyse de la pertinence des échanges r&eacute;alis&eacute;s sur la plateforme.</p></div></div>
            <p style="color: var(--muted); font-size: 0.92rem; line-height: 1.6;">Le module d'échange permet de relier deux offres distinctes. En tant qu'administrateur, vous intervenez pour valider les décisions ou supprimer les demandes obsolètes.</p>
            <div style="margin-top: 20px; padding: 15px; background: rgba(52, 152, 219, 0.08); border-radius: 12px; border-left: 4px solid var(--blue);">
               <p style="color: var(--blue); font-size: 0.85rem;"><strong>Note :</strong> L'acceptation d'un échange peut influencer la note de réputation des participants.</p>
            </div>
          </section>

          <section class="ssa-card" aria-labelledby="notes-ech-admin-title">
            <div class="ssa-card-header"><div><h3 id="notes-ech-admin-title">&Eacute;valuations apr&egrave;s &eacute;change</h3><p>Mod&eacute;ration des notes et des avis laiss&eacute;s par les participants.</p></div></div>
            <p style="color: var(--muted); font-size: 0.92rem">File d&apos;attente : 2 avis &agrave; v&eacute;rifier (contenu ou note incoh&eacute;rente).</p>
          </section>
        </div>
      </div>
    </main>
  </div>

  <script>
    const adminOffresJSON = <?= json_encode($offres ?? []) ?>;
    let adminMap, adminMarkers = [];

    // --- Auto-hide Messages & Clean URL ---
    document.addEventListener('DOMContentLoaded', () => {
      // 1. Initialize Map
      adminMap = L.map('adminOffreMap').setView([33.8869, 9.5375], 6); // Tunisia center
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
      }).addTo(adminMap);

      // 2. Add Markers
      const bounds = [];
      adminOffresJSON.forEach(o => {
        if (o.latitude && o.longitude) {
          const m = L.marker([o.latitude, o.longitude]).addTo(adminMap);
          m.bindPopup(`<strong>${o.ingredient}</strong><br>${o.localisation}<br><span class="ssa-status ${o.etat === 'bloqué' ? 'warn' : 'ok'}">${o.etat}</span>`);
          adminMarkers.push({ id: o.id_offre, marker: m });
          bounds.push([o.latitude, o.longitude]);
        }
      });

      if (bounds.length > 0) {
        adminMap.fitBounds(bounds, { padding: [30, 30] });
      }

      // 3. Message auto-hide
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
    });

    function zoomToOffer(lat, lng, name) {
      if (!adminMap) return;
      adminMap.setView([lat, lng], 13);
      // Find the marker and open its popup
      const markerObj = adminMarkers.find(m => m.marker.getLatLng().lat == lat && m.marker.getLatLng().lng == lng);
      if (markerObj) markerObj.marker.openPopup();
      
      // Scroll to map
      document.getElementById('adminOffreMap').scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  </script>
  <style>
    .visually-hidden { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }
  </style>
</body>
</html>
