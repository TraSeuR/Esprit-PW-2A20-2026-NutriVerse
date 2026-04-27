<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../FrontOffice/login.php");
  exit();
}
require_once "../../Controller/userC.php";
require_once "../../Controller/profileC.php";
$userC = new userC();
$profileC = new profileC();
$list = $userC->listUser();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriVerse - Dashboard Back Office</title>

  <!-- CSS -->
  <link rel="stylesheet" href="assets/css/back.css" />
  <link rel="stylesheet" href="../FrontOffice/assets/css/userbox.css" />

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet" />

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- Feather Icons -->
  <script src="https://unpkg.com/feather-icons"></script>
</head>

<body>

  <!-- =========================
       SIDEBAR
  ========================== -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-top">
      <div class="brand">
        <img src="images/logo.png" alt="Logo NutriVerse" class="brand-logo" />
        <div>
          <h2>NutriVerse</h2>
          <p>Back Office</p>
        </div>
      </div>

      <button class="close-sidebar" id="closeSidebar">✕</button>
    </div>

    <nav class="sidebar-menu">
      <a href="#" class="menu-item active" data-section="dashboard">
        <i data-feather="grid"></i>
        <span>Dashboard</span>
      </a>

      <a href="#" class="menu-item" data-section="other">
        <i data-feather="book-open"></i>
        <span>Recettes</span>
      </a>

      <a href="#" class="menu-item" data-section="utilisateurs" id="nav-utilisateurs">
        <i data-feather="users"></i>
        <span>Utilisateurs</span>
      </a>

      <a href="#" class="menu-item" data-section="other">
        <i data-feather="package"></i>
        <span>Produits</span>
      </a>

      <a href="#" class="menu-item" data-section="other">
        <i data-feather="shopping-cart"></i>
        <span>Commandes</span>
      </a>

      <a href="#" class="menu-item" data-section="other">
        <i data-feather="activity"></i>
        <span>Suivi Santé</span>
      </a>

      <a href="#" class="menu-item" data-section="other">
        <i data-feather="heart"></i>
        <span>Programmes</span>
      </a>

      <a href="#" class="menu-item" data-section="other">
        <i data-feather="settings"></i>
        <span>Paramètres</span>
      </a>
    </nav>

    <div class="sidebar-footer">
      <p>© 2026 NutriVerse</p>
    </div>
  </aside>

  <!-- =========================
       MAIN CONTENT
  ========================== -->
  <div class="main-content">

    <!-- TOPBAR -->
    <header class="topbar">
      <div class="topbar-left">
        <button class="menu-btn" id="menuBtn">
          <i data-feather="menu"></i>
        </button>

        <div class="search-box">
          <i data-feather="search"></i>
          <input type="text" placeholder="Rechercher..." />
        </div>
      </div>

      <div class="topbar-right">
        <button class="icon-btn notification-btn">
          <i data-feather="bell"></i>
          <span class="notif-dot"></span>
        </button>

        <div class="user-menu admin-box">
          <button class="user-btn" id="userMenuBtn"
            style="background-color: transparent; color: #333; gap: 10px; padding: 5px;">
            <div class="admin-avatar"><?= strtoupper(substr($_SESSION['prenom'] ?? 'A', 0, 1)) ?></div>
            <div style="text-align: left; display: flex; flex-direction: column;">
              <h4 style="margin:0; font-size: 0.95rem;">
                <?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?>
              </h4>
              <p style="margin:0; font-size: 0.8rem; color: #777;">Administrateur</p>
            </div>
            <span>▼</span>
          </button>

          <div class="user-dropdown" id="userDropdown" style="top: 100%; right: 0;">
            <a href="../FrontOffice/logout.php" class="logout"><i data-feather="log-out" style="width: 16px;"></i>
              Déconnexion</a>
          </div>
        </div>
      </div>
    </header>

    <!-- DASHBOARD CONTENT -->
    <!-- ═══════════════════════════════════════════
         SECTION: DASHBOARD (section par défaut)
    ════════════════════════════════════════════ -->
    <main class="dashboard-content" id="section-dashboard">

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

        <div class="stat-card stat-placeholder">
          <div class="stat-top">
            <div>
              <p class="stat-title">Utilisateurs</p>
              <h2>--</h2>
            </div>
            <div class="stat-icon green"></div>
          </div>
          <p class="stat-subtitle">Les statistiques apparaîtront ici</p>
        </div>

        <div class="stat-card stat-placeholder">
          <div class="stat-top">
            <div>
              <p class="stat-title">Recettes</p>
              <h2>--</h2>
            </div>
            <div class="stat-icon orange"></div>
          </div>
          <p class="stat-subtitle">Les statistiques apparaîtront ici</p>
        </div>

        <div class="stat-card stat-placeholder">
          <div class="stat-top">
            <div>
              <p class="stat-title">Produits</p>
              <h2>--</h2>
            </div>
            <div class="stat-icon blue"></div>
          </div>
          <p class="stat-subtitle">Les statistiques apparaîtront ici</p>
        </div>

        <div class="stat-card stat-placeholder">
          <div class="stat-top">
            <div>
              <p class="stat-title">Commandes</p>
              <h2>--</h2>
            </div>
            <div class="stat-icon purple"></div>
          </div>
          <p class="stat-subtitle">Les statistiques apparaîtront ici</p>
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

          <div class="chart-placeholder">
            <div class="placeholder-icon"></div>
            <h4>Le graphique apparaîtra ici</h4>
            <p>
              Cette zone sera connectée plus tard aux données réelles
              des utilisateurs, recettes et commandes.
            </p>
          </div>
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
        <div class="table-card">
          <div class="card-header">
            <div>
              <h3>Commandes Récentes</h3>
              <p>Dernières commandes passées sur la plateforme</p>
            </div>
            <a href="#" class="view-all">Voir tout</a>
          </div>

          <div class="empty-table-box">
            <div class="empty-table-icon"></div>
            <h4>Les commandes apparaîtront ici</h4>
            <p>
              Les dernières commandes clients seront affichées automatiquement
              une fois la base de données connectée.
            </p>
          </div>
        </div>

        <!-- Nouveaux Utilisateurs -->
        <div class="users-card">
          <div class="card-header">
            <div>
              <h3>Nouveaux Utilisateurs</h3>
              <p>Dernières inscriptions</p>
            </div>
          </div>

          <div class="empty-users-box">
            <div class="empty-table-icon"></div>
            <h4>Les utilisateurs apparaîtront ici</h4>
            <p>
              Cette section affichera automatiquement les nouveaux comptes
              inscrits sur la plateforme.
            </p>
          </div>
        </div>

      </section>

    </main><!-- /#section-dashboard -->

    <!-- ═══════════════════════════════════════════════════════════════
         SECTION: UTILISATEURS – Gestion des comptes utilisateurs
         Inséré par extension du Back Office NutriVerse
    ════════════════════════════════════════════════════════════════ -->
    <main class="dashboard-content u-section" id="section-utilisateurs" style="display:none;">

      <!-- ── PAGE HEADER ──────────────────────────────────────── -->
      <section class="page-header fade-up">
        <div>
          <span class="section-badge">Gestion utilisateurs</span>
          <h1>Utilisateurs</h1>
          <p>Gérez les comptes utilisateurs de la plateforme NutriVerse.</p>
        </div>
        <button class="export-btn" onclick="openModal('modal-add')">
          <i data-feather="user-plus"></i>
          Ajouter un utilisateur
        </button>
      </section>

      <!-- ── STAT CARDS ───────────────────────────────────────── 
      <section class="stats-grid fade-up delay-1">

        <div class="stat-card">
          <div class="stat-info">
            <p>Total utilisateurs</p>
            <h2>24</h2>
            <span class="positive">↑ +3 ce mois</span>
          </div>
          <div class="stat-icon green">
            <i data-feather="users" style="width:28px;height:28px;"></i>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-info">
            <p>Comptes actifs</p>
            <h2>20</h2>
            <span class="positive">83% du total</span>
          </div>
          <div class="stat-icon blue">
            <i data-feather="user-check" style="width:28px;height:28px;"></i>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-info">
            <p>Comptes désactivés</p>
            <h2>4</h2>
            <span style="color:var(--orange);font-weight:500;font-size:.9rem;">↓ -1 ce mois</span>
          </div>
          <div class="stat-icon orange">
            <i data-feather="user-x" style="width:28px;height:28px;"></i>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-info">
            <p>Administrateurs</p>
            <h2>2</h2>
            <span class="positive">Accès complet</span>
          </div>
          <div class="stat-icon purple">
            <i data-feather="shield" style="width:28px;height:28px;"></i>
          </div>
        </div>

      </section> -->


      <!-- ── USERS TABLE ──────────────────────────────────────── -->
      <section class="table-card fade-up">
        <div class="card-header">
          <div>
            <h3>Liste des utilisateurs</h3>
          </div>
          <!-- <button class="mini-btn" id="btn-refresh-table">
            <i data-feather="refresh-cw" style="width:15px;height:15px;vertical-align:middle;"></i>
            Actualiser
          </button> -->
        </div>

        <!-- ── SEARCH & FILTERS AND SORT ──────────────────────── -->
        <div class="u-toolbar" style="margin-bottom:24px;">
          <div class="u-search-wrap">
            <i data-feather="search" class="u-search-icon"></i>
            <input type="text" id="u-search-input" class="u-search-input"
              placeholder="Rechercher par nom, prénom ou email…" />
          </div>

          <div class="u-filters">
            <select id="u-filter-role" class="u-select">
              <option value="">Tous les rôles</option>
              <option value="admin">Administrateur</option>
              <option value="utilisateur">Utilisateur</option>
            </select>

            <select id="u-filter-status" class="u-select">
              <option value="">Tous les statuts</option>
              <option value="actif">Actif</option>
              <option value="desactive">Désactivé</option>
            </select>

            <select id="u-sort-table" class="u-select" onchange="sortTable()">
              <option value="id_asc">↑ Trier par ID (Croissant)</option>
              <option value="id_desc">↓ Trier par ID (Décroissant)</option>
              <option value="nom_asc">A-Z Trier par Nom</option>
              <option value="nom_desc">Z-A Trier par Nom</option>
            </select>
          </div>
        </div>

        <div class="table-wrapper">
          <table id="users-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Utilisateur</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Statut</th>
                <th>Inscrit le</th>
                <th style="text-align:right;">Actions</th>
              </tr>
            </thead>
            <tbody id="users-tbody">
              <?php foreach ($list as $p): ?>
                <tr data-role="<?= strtolower($p['role'] ?? '') ?>"
                  data-status="<?= strtolower($p['etat_compte'] ?? '') ?>" data-id="<?= $p['id_user'] ?>"
                  data-name="<?= strtolower(($p['prenom'] ?? '') . ' ' . ($p['nom'] ?? '')) ?>">
                  <td><?= $p['id_user'] ?></td>
                  <td>
                    <div class="u-avatar-cell">
                      <div class="u-avatar u-avatar-blue"><?= strtoupper(substr($p['prenom'] ?? 'U', 0, 1)) ?></div>
                      <div>
                        <div class="u-name">
                          <?= htmlspecialchars($p['prenom'] ?? '') . ' ' . htmlspecialchars($p['nom'] ?? '') ?>
                        </div>
                        <div class="u-sub"><?= ucfirst(htmlspecialchars($p['role'] ?? '')) ?></div>
                      </div>
                    </div>
                  </td>
                  <td class="u-email"><?= htmlspecialchars($p['email'] ?? '') ?></td>
                  <td>
                    <?php if (($p['role'] ?? '') == 'admin'): ?>
                      <span class="u-role-badge role-admin">Admin</span>
                    <?php else: ?>
                      <span class="u-role-badge role-user">Utilisateur</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if (($p['etat_compte'] ?? '') == 'actif'): ?>
                      <span class="status delivered">Actif</span>
                    <?php else: ?>
                      <span class="status pending">Désactivé</span>
                    <?php endif; ?>
                  </td>
                  <td class="u-date"><?= htmlspecialchars($p['date_inscription'] ?? '') ?></td>
                  <td>
                    <div class="u-actions">
                      <a href="javascript:void(0)" onclick="openModal('modal-view-<?= $p['id_user'] ?>')"
                        class="u-btn u-btn-view" title="Voir"><i data-feather="eye"></i></a>
                      <a href="javascript:void(0)" onclick="openModal('modal-edit-<?= $p['id_user'] ?>')"
                        class="u-btn u-btn-edit" title="Modifier"><i data-feather="edit-2"></i></a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div><!-- /.table-wrapper -->

        <!-- Empty state (hidden by default, shown via JS when no results) -->
        <div class="u-empty-state" id="u-empty-state" style="display:none;">
          <div class="placeholder-icon">👤</div>
          <h4>Aucun utilisateur trouvé</h4>
          <p>Modifiez vos critères de recherche ou ajoutez un nouvel utilisateur.</p>
          <button class="export-btn" style="margin-top:18px;" onclick="document.getElementById('btn-add-user').click()">
            <i data-feather="user-plus"></i> Ajouter un utilisateur
          </button>
        </div>

      </section><!-- /.table-card -->

      <!-- ═══════════════════════════════════════════════════════════════
           MODALS: VOIR ET MODIFIER UN UTILISATEUR (Générés dynamiquement)
      ════════════════════════════════════════════════════════════════ -->
      <?php foreach ($list as $p):
        // Les colonnes du profil viennent directement du LEFT JOIN.
        // Si l'utilisateur n'a pas encore de profil, les valeurs sont NULL → on les remplace par ''.
        $prof = [
          'telephone' => $p['telephone'] ?? '',
          'date_naissance' => $p['date_naissance'] ?? '',
          'sexe' => $p['sexe'] ?? '',
          'poids' => $p['poids'] ?? '',
          'taille' => $p['taille'] ?? '',
          'objectif_nutritionnel' => $p['objectif_nutritionnel'] ?? '',
          'preference_alimentaire' => $p['preference_alimentaire'] ?? '',
          'allergies' => $p['allergies'] ?? '',
        ];
        ?>
        <!-- MODAL VOIR -->
        <div class="u-modal-overlay" id="modal-view-<?= $p['id_user'] ?>"
          onclick="closeModal('modal-view-<?= $p['id_user'] ?>')">
          <div class="u-modal" onclick="event.stopPropagation()">
            <button class="u-modal-close" onclick="closeModal('modal-view-<?= $p['id_user'] ?>')">✕</button>

            <!-- MODAL HEADER -->
            <div class="u-modal-hero">
              <div class="u-modal-avatar <?= ($p['role'] ?? '') == 'admin' ? 'u-avatar-green' : 'u-avatar-blue' ?>">
                <?= strtoupper(substr($p['prenom'] ?? 'U', 0, 1)) ?>
              </div>
              <div>
                <h2 class="u-modal-name">
                  <?= htmlspecialchars($p['prenom'] ?? '') . ' ' . htmlspecialchars($p['nom'] ?? '') ?>
                </h2>
                <?php if (($p['role'] ?? '') == 'admin'): ?>
                  <span class="u-role-badge role-admin">Admin</span>
                <?php else: ?>
                  <span class="u-role-badge role-user">Utilisateur</span>
                <?php endif; ?>
                <?php if (($p['etat_compte'] ?? '') == 'actif'): ?>
                  <span class="status delivered" style="margin-left:8px;">Actif</span>
                <?php else: ?>
                  <span class="status pending" style="margin-left:8px;">Désactivé</span>
                <?php endif; ?>
              </div>
            </div>

            <!-- MODAL BODY -->
            <div class="u-modal-grid">
              <div class="u-detail-group">
                <span class="u-detail-label">Prénom</span>
                <span class="u-detail-value"><?= htmlspecialchars($p['prenom'] ?? '') ?></span>
              </div>
              <div class="u-detail-group">
                <span class="u-detail-label">Nom</span>
                <span class="u-detail-value"><?= htmlspecialchars($p['nom'] ?? '') ?></span>
              </div>
              <div class="u-detail-group">
                <span class="u-detail-label">Email</span>
                <span class="u-detail-value"><?= htmlspecialchars($p['email'] ?? '') ?></span>
              </div>
              <div class="u-detail-group">
                <span class="u-detail-label">Téléphone</span>
                <span class="u-detail-value"><?= htmlspecialchars($prof['telephone'] ?? '--') ?></span>
              </div>
              <div class="u-detail-group">
                <span class="u-detail-label">Sexe</span>
                <span class="u-detail-value"><?= htmlspecialchars($prof['sexe'] ?? '--') ?></span>
              </div>
              <div class="u-detail-group">
                <span class="u-detail-label">Date de naissance</span>
                <span class="u-detail-value"><?= htmlspecialchars($prof['date_naissance'] ?? '--') ?></span>
              </div>
              <div class="u-detail-group">
                <span class="u-detail-label">Poids (kg)</span>
                <span class="u-detail-value"><?= htmlspecialchars($prof['poids'] ?? '--') ?></span>
              </div>
              <div class="u-detail-group">
                <span class="u-detail-label">Taille (cm)</span>
                <span class="u-detail-value"><?= htmlspecialchars($prof['taille'] ?? '--') ?></span>
              </div>
              <div class="u-detail-group" style="grid-column:1/-1;">
                <span class="u-detail-label">Objectif nutritionnel</span>
                <span class="u-detail-value"><?= htmlspecialchars($prof['objectif_nutritionnel'] ?? '--') ?></span>
              </div>
              <div class="u-detail-group" style="grid-column:1/-1;">
                <span class="u-detail-label">Préférence alimentaire</span>
                <span class="u-detail-value"><?= htmlspecialchars($prof['preference_alimentaire'] ?? '--') ?></span>
              </div>
              <div class="u-detail-group" style="grid-column:1/-1;">
                <span class="u-detail-label">Allergies</span>
                <span class="u-detail-value"><?= htmlspecialchars($prof['allergies'] ?? 'Aucune') ?></span>
              </div>
              <div class="u-detail-group">
                <span class="u-detail-label">Date d'inscription</span>
                <span class="u-detail-value"><?= htmlspecialchars($p['date_inscription'] ?? '') ?></span>
              </div>
            </div>

            <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:24px;">
              <button class="mini-btn" onclick="closeModal('modal-view-<?= $p['id_user'] ?>')">Fermer</button>
              <button
                onclick="closeModal('modal-view-<?= $p['id_user'] ?>'); openModal('modal-edit-<?= $p['id_user'] ?>')"
                class="export-btn" style="padding:12px 20px;">
                <i data-feather="edit-2"></i> Modifier
              </button>
            </div>
          </div>
        </div>

        <!-- MODAL MODIFIER -->
        <div class="u-modal-overlay" id="modal-edit-<?= $p['id_user'] ?>"
          onclick="closeModal('modal-edit-<?= $p['id_user'] ?>')">
          <div class="u-modal u-modal-wide" onclick="event.stopPropagation()">
            <button class="u-modal-close" onclick="closeModal('modal-edit-<?= $p['id_user'] ?>')">✕</button>

            <h2 class="u-modal-title">Modifier :
              <?= htmlspecialchars($p['prenom'] ?? '') . ' ' . htmlspecialchars($p['nom'] ?? '') ?>
            </h2>
            <p style="color:var(--muted);margin-bottom:24px;font-size:.9rem;">Mettez à jour les informations ci-dessous.
            </p>

            <form action="update.php" method="POST">
              <input type="hidden" name="id_user" value="<?= $p['id_user'] ?>" />

              <div class="u-form-section">Identité</div>
              <div class="u-form-grid">
                <div class="u-form-group">
                  <label class="u-form-label">Prénom</label>
                  <input type="text" name="prenom" class="u-form-input"
                    value="<?= htmlspecialchars($p['prenom'] ?? '') ?>" required />
                </div>
                <div class="u-form-group">
                  <label class="u-form-label">Nom</label>
                  <input type="text" name="nom" class="u-form-input" value="<?= htmlspecialchars($p['nom'] ?? '') ?>"
                    required />
                </div>
                <div class="u-form-group">
                  <label class="u-form-label">Email</label>
                  <input type="email" name="email" class="u-form-input" value="<?= htmlspecialchars($p['email'] ?? '') ?>"
                    required />
                </div>
                <div class="u-form-group">
                  <label class="u-form-label">Mot de passe</label>
                  <input type="password" name="mot_de_passe" class="u-form-input" placeholder="Laisser vide = inchangé" />
                </div>
                <div class="u-form-group">
                  <label class="u-form-label">Rôle</label>
                  <select name="role" class="u-form-input">
                    <option value="utilisateur" <?= (($p['role'] ?? '') == 'utilisateur') ? 'selected' : '' ?>>Utilisateur
                    </option>
                    <option value="admin" <?= (($p['role'] ?? '') == 'admin') ? 'selected' : '' ?>>Administrateur</option>
                  </select>
                </div>
                <div class="u-form-group">
                  <label class="u-form-label">État du compte</label>
                  <select name="etat_compte" class="u-form-input">
                    <option value="actif" <?= (($p['etat_compte'] ?? '') == 'actif') ? 'selected' : '' ?>>Actif</option>
                    <option value="desactive" <?= (($p['etat_compte'] ?? '') == 'desactive') ? 'selected' : '' ?>>Désactivé
                    </option>
                  </select>
                </div>
              </div>

              <div class="u-form-section">Informations personnelles</div>
              <div class="u-form-grid">
                <div class="u-form-group">
                  <label class="u-form-label">Téléphone</label>
                  <input type="tel" name="telephone" class="u-form-input"
                    value="<?= htmlspecialchars($prof['telephone'] ?? '') ?>" />
                </div>
                <div class="u-form-group">
                  <label class="u-form-label">Date de naissance</label>
                  <input type="date" name="date_naissance" class="u-form-input"
                    value="<?= htmlspecialchars($prof['date_naissance'] ?? '') ?>" />
                </div>
                <div class="u-form-group">
                  <label class="u-form-label">Sexe</label>
                  <select name="sexe" class="u-form-input">
                    <option value="" <?= empty($prof['sexe']) ? 'selected' : '' ?>>Choisir…</option>
                    <option value="Homme" <?= (($prof['sexe'] ?? '') == 'Homme') ? 'selected' : '' ?>>Homme</option>
                    <option value="Femme" <?= (($prof['sexe'] ?? '') == 'Femme') ? 'selected' : '' ?>>Femme</option>
                  </select>
                </div>
                <div class="u-form-group">
                  <label class="u-form-label">Poids (kg)</label>
                  <input type="number" name="poids" class="u-form-input"
                    value="<?= htmlspecialchars($prof['poids'] ?? '') ?>" step="0.1" />
                </div>
                <div class="u-form-group">
                  <label class="u-form-label">Taille (cm)</label>
                  <input type="number" name="taille" class="u-form-input"
                    value="<?= htmlspecialchars($prof['taille'] ?? '') ?>" step="0.1" />
                </div>
              </div>

              <div class="u-form-section">Profil nutritionnel</div>
              <div class="u-form-grid">
                <div class="u-form-group">
                  <label class="u-form-label">Objectif nutritionnel</label>
                  <select name="objectif_nutritionnel" class="u-form-input">
                    <option value="" <?= empty($prof['objectif_nutritionnel']) ? 'selected' : '' ?>>Choisir…</option>
                    <option value="Perte de poids" <?= (($prof['objectif_nutritionnel'] ?? '') == 'Perte de poids') ? 'selected' : '' ?>>Perte de poids</option>
                    <option value="Prise de masse" <?= (($prof['objectif_nutritionnel'] ?? '') == 'Prise de masse') ? 'selected' : '' ?>>Prise de masse</option>
                    <option value="Maintien" <?= (($prof['objectif_nutritionnel'] ?? '') == 'Maintien') ? 'selected' : '' ?>>
                      Maintien</option>
                    <option value="Amélioration santé" <?= (($prof['objectif_nutritionnel'] ?? '') == 'Amélioration santé') ? 'selected' : '' ?>>Amélioration santé</option>
                    <option value="Augmenter l'énergie" <?= (($prof['objectif_nutritionnel'] ?? '') == "Augmenter l'énergie") ? 'selected' : '' ?>>Augmenter l'énergie</option>
                  </select>
                </div>
                <div class="u-form-group">
                  <label class="u-form-label">Préférence alimentaire</label>
                  <select name="preference_alimentaire" class="u-form-input">
                    <option value="" <?= empty($prof['preference_alimentaire']) ? 'selected' : '' ?>>Choisir…</option>
                    <option value="Omnivore" <?= (($prof['preference_alimentaire'] ?? '') == 'Omnivore') ? 'selected' : '' ?>>Omnivore</option>
                    <option value="Végétarien" <?= (($prof['preference_alimentaire'] ?? '') == 'Végétarien') ? 'selected' : '' ?>>Végétarien</option>
                    <option value="Vegan" <?= (($prof['preference_alimentaire'] ?? '') == 'Vegan') ? 'selected' : '' ?>>Vegan
                    </option>
                    <option value="Pescétarien" <?= (($prof['preference_alimentaire'] ?? '') == 'Pescétarien') ? 'selected' : '' ?>>Pescétarien</option>
                    <option value="Keto" <?= (($prof['preference_alimentaire'] ?? '') == 'Keto') ? 'selected' : '' ?>>Keto
                    </option>
                  </select>
                </div>
                <div class="u-form-group" style="grid-column:1/-1;">
                  <label class="u-form-label">Allergies</label>
                  <input type="text" name="allergies" class="u-form-input"
                    value="<?= htmlspecialchars($prof['allergies'] ?? '') ?>" placeholder="Ex: Gluten, Lactose" />
                </div>
              </div>

              <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:28px;">
                <button type="button" class="mini-btn"
                  onclick="closeModal('modal-edit-<?= $p['id_user'] ?>')">Annuler</button>
                <button type="submit" class="export-btn" style="padding:12px 24px;">
                  <i data-feather="save"></i> Enregistrer
                </button>
              </div>
            </form>
          </div>
        </div>
      <?php endforeach; ?>

      <!-- ═══════════════════════════════════════════════════════════════
           MODAL: AJOUTER UN UTILISATEUR
      ════════════════════════════════════════════════════════════════ -->
      <div class="u-modal-overlay" id="modal-add" onclick="closeModal('modal-add')">
        <div class="u-modal u-modal-wide" onclick="event.stopPropagation()">
          <button class="u-modal-close" onclick="closeModal('modal-add')">✕</button>

          <h2 class="u-modal-title">Ajouter un utilisateur</h2>
          <p style="color:var(--muted);margin-bottom:24px;font-size:.9rem;">Remplissez tous les champs requis.</p>

          <form action="ajoute.php" method="POST">
            <div class="u-form-section">Identité</div>
            <div class="u-form-grid">
              <div class="u-form-group">
                <label class="u-form-label">Prénom</label>
                <input type="text" name="prenom" class="u-form-input" placeholder="Prénom" required />
              </div>
              <div class="u-form-group">
                <label class="u-form-label">Nom</label>
                <input type="text" name="nom" class="u-form-input" placeholder="Nom" required />
              </div>
              <div class="u-form-group">
                <label class="u-form-label">Email</label>
                <input type="email" name="email" class="u-form-input" placeholder="email@example.com" required />
              </div>
              <div class="u-form-group">
                <label class="u-form-label">Mot de passe</label>
                <input type="password" name="mot_de_passe" class="u-form-input" placeholder="Mot de passe" required />
              </div>
              <div class="u-form-group">
                <label class="u-form-label">Rôle</label>
                <select name="role" class="u-form-input">
                  <option value="utilisateur">Utilisateur</option>
                  <option value="admin">Administrateur</option>
                </select>
              </div>
              <div class="u-form-group">
                <label class="u-form-label">État du compte</label>
                <select name="etat_compte" class="u-form-input">
                  <option value="actif">Actif</option>
                  <option value="desactive">Désactivé</option>
                </select>
              </div>
            </div>

            <div class="u-form-section">Informations personnelles</div>
            <div class="u-form-grid">
              <div class="u-form-group">
                <label class="u-form-label">Téléphone</label>
                <input type="tel" name="telephone" class="u-form-input" placeholder="+216 XX XXX XXX" />
              </div>
              <div class="u-form-group">
                <label class="u-form-label">Date de naissance</label>
                <input type="date" name="date_naissance" class="u-form-input" />
              </div>
              <div class="u-form-group">
                <label class="u-form-label">Sexe</label>
                <select name="sexe" class="u-form-input">
                  <option value="">Choisir…</option>
                  <option value="Homme">Homme</option>
                  <option value="Femme">Femme</option>
                </select>
              </div>
              <div class="u-form-group">
                <label class="u-form-label">Poids (kg)</label>
                <input type="number" name="poids" class="u-form-input" placeholder="Ex: 70" step="0.1" />
              </div>
              <div class="u-form-group">
                <label class="u-form-label">Taille (cm)</label>
                <input type="number" name="taille" class="u-form-input" placeholder="Ex: 175" step="0.1" />
              </div>
            </div>

            <div class="u-form-section">Profil nutritionnel</div>
            <div class="u-form-grid">
              <div class="u-form-group">
                <label class="u-form-label">Objectif nutritionnel</label>
                <select name="objectif_nutritionnel" class="u-form-input">
                  <option value="">Choisir…</option>
                  <option value="Perte de poids">Perte de poids</option>
                  <option value="Prise de masse">Prise de masse</option>
                  <option value="Maintien">Maintien</option>
                  <option value="Amélioration santé">Amélioration santé</option>
                  <option value="Augmenter l'énergie">Augmenter l'énergie</option>
                </select>
              </div>
              <div class="u-form-group">
                <label class="u-form-label">Préférence alimentaire</label>
                <select name="preference_alimentaire" class="u-form-input">
                  <option value="">Choisir…</option>
                  <option value="Omnivore">Omnivore</option>
                  <option value="Végétarien">Végétarien</option>
                  <option value="Vegan">Vegan</option>
                  <option value="Pescétarien">Pescétarien</option>
                  <option value="Keto">Keto</option>
                </select>
              </div>
              <div class="u-form-group" style="grid-column:1/-1;">
                <label class="u-form-label">Allergies</label>
                <input type="text" name="allergies" class="u-form-input" placeholder="Ex: Gluten, Lactose" />
              </div>
            </div>

            <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:28px;">
              <button type="button" class="mini-btn" onclick="closeModal('modal-add')">Annuler</button>
              <button type="submit" class="export-btn" style="padding:12px 24px;">
                <i data-feather="save"></i> Enregistrer
              </button>
            </div>
          </form>
        </div>
      </div>

    </main><!-- /#section-utilisateurs -->

  </div><!-- /.main-content -->

  <!-- JS -->
  <script src="assets/js/back.js"></script>
  <!-- Form validation -->
  <script src="assets/js/back.validate.js"></script>
  <script src="../FrontOffice/assets/js/userbox.js"></script>
</body>

</html>