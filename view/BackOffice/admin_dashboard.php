<?php
require_once __DIR__ . '/../../controller/RegimeController.php';
require_once __DIR__ . '/../../controller/PlanningController.php';

$regimeCtrl = new RegimeController();
$regimes = $regimeCtrl->listRegimes(); // utilisé pour le tableau des Régimes (Étape 1)

$planningCtrl = new PlanningController();
// INNER JOIN : récupère les plannings avec le nom du régime associé en une seule requête
$planningsWithRegimes = $planningCtrl->listPlanningsWithRegimes();
$plannings = $planningCtrl->listPlannings(); // utilisé uniquement pour les compteurs

$totalR = count($plannings);
$pnd = 0;
foreach ($plannings as $p) {
    if ($p->getStatut() == 'en_attente')
        $pnd++;
}

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'del_p') {
        $planningCtrl->deletePlanning($_GET['id']);
    }
    if ($_GET['action'] == 'upd_s') {
        $planningCtrl->updateStatut($_GET['id'], $_GET['s']);
    }
    if ($_GET['action'] == 'reject_p') {
        // Le refus entraîne la suppression complète du dossier (Régime + Planning)
        $p = $planningCtrl->getPlanningById($_GET['id']);
        if ($p) {
            $regimeCtrl->deleteRegime($p->getIdRegime());
        }
    }
    header("Location: admin_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriVerse - Gestion Programmes</title>

    <!-- Nutrition styles -->
    <link rel="stylesheet" href="assets/back.css">
    <!-- Existing programme styles for table -->
    <link rel="stylesheet" href="../assets/style.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <script src="https://unpkg.com/feather-icons"></script>

    <style>
        .creative-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
            margin-top: 10px;
        }

        .btn-action-text {
            font-size: 0.65rem;
            font-weight: 800;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 8px;
            text-transform: uppercase;
            transition: 0.3s;
        }

        .btn-mod {
            color: #59b84d;
            background: rgba(89, 184, 77, 0.1);
        }

        .btn-mod:hover {
            background: #59b84d;
            color: white;
        }

        .btn-del {
            color: #e63946;
            background: rgba(230, 57, 70, 0.1);
        }

        .btn-del:hover {
            background: #e63946;
            color: white;
        }

        .btn-ok {
            color: #27ae60;
            background: #e9f7ef;
            border: 1px solid #27ae60;
        }

        .btn-no {
            color: #e74c3c;
            background: #fdeded;
            border: 1px solid #e74c3c;
        }

        /* Overriding style for compatibility with new sidebar */
        body {
            display: flex;
            background: var(--bg);
        }

        .main-content {
            padding-bottom: 50px;
        }

        .glass-card {
            border: 1px solid var(--border) !important;
            box-shadow: var(--shadow) !important;
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-top">
            <div class="brand">
                <img src="images/logo.png" alt="Logo NutriVerse" class="brand-logo" />
                <div>
                    <h2>NutriVerse</h2>
                    <p>Back Office</p>
                </div>
            </div>
        </div>

        <nav class="sidebar-menu">
            <a href="nutri_back.php" class="menu-item">
                <i data-feather="grid"></i>
                <span>Dashboard</span>
            </a>
            <a href="#" class="menu-item"><i data-feather="book-open"></i><span>Recettes</span></a>
            <a href="#" class="menu-item"><i data-feather="users"></i><span>Utilisateurs</span></a>
            <a href="#" class="menu-item"><i data-feather="package"></i><span>Produits</span></a>
            <a href="#" class="menu-item"><i data-feather="shopping-cart"></i><span>Commandes</span></a>
            <a href="#" class="menu-item"><i data-feather="activity"></i><span>Suivi Santé</span></a>
            <a href="admin_dashboard.php" class="menu-item active">
                <i data-feather="heart"></i>
                <span>Programmes</span>
            </a>
            <a href="#" class="menu-item"><i data-feather="settings"></i><span>Paramètres</span></a>
        </nav>

        <div class="sidebar-footer">
            <p>© 2026 NutriVerse</p>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <header class="topbar">
            <div class="topbar-left">
                <div class="search-box" style="display: flex; gap: 10px; align-items: center; width: 100%;">
                    <i data-feather="search"></i>
                    <input type="text" id="adminSearchInput" placeholder="Rechercher des programmes..."
                        style="flex: 1;">
                    <select id="adminTypeFilter"
                        style="background: white; border: 1px solid var(--border); border-radius: 8px; padding: 5px 10px; font-size: 0.8rem; color: var(--text); cursor: pointer; outline: none;">
                        <option value="all">Filtrer par type</option>
                        <option value="prise_masse">Prise de masse</option>
                        <option value="perte_poids">Perte de poids</option>
                        <option value="equilibre">Équilibre santé</option>
                    </select>
                </div>
            </div>

            <div class="topbar-right">
                <div class="admin-box">
                    <div class="admin-avatar">A</div>
                    <div>
                        <h4>Admin</h4>
                        <p>Administrateur</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="dashboard-content">

            <!-- HEADER CRUD -->
            <section class="page-header fade-up">
                <div>
                    <span class="section-badge">Gestion Programmes</span>
                    <h1>Programmes & Plannings</h1>
                    <p>Validez les demandes clients et gérez votre catalogue de régimes nutritionnels et sportifs.</p>
                </div>

                <div style="display: flex; gap: 10px;">
                    <a href="add_regime.php" class="export-btn" style="text-decoration: none; background: #27ae60;">
                        <i data-feather="plus"></i>
                        AJOUTER RÉGIME
                    </a>
                    <a href="add_programme_back.php" class="export-btn" style="text-decoration: none;">
                        <i data-feather="plus"></i>
                        NOUVEAU PLANNING
                    </a>
                </div>
            </section>

            <!-- STATS SIMPLE -->
            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <p>Total Actifs</p>
                        <h2><?php echo $totalR; ?></h2>
                    </div>
                    <div class="stat-icon green"><i data-feather="bar-chart"></i></div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <p>En Attente</p>
                        <h2><?php echo $pnd; ?></h2>
                    </div>
                    <div class="stat-icon orange"><i data-feather="clock"></i></div>
                </div>
            </section>

            <!-- SECTION RÉGIMES -->
            <div class="glass-card" style="padding: 40px; background: white; border-radius: 28px; margin-bottom: 40px;">
                <div style="margin-bottom: 30px;">
                    <span class="section-badge" style="margin-bottom: 10px;">Étape 1</span>
                    <h1 style="font-size: 2.2rem; margin-bottom: 10px; color: var(--text);">Régimes Alimentaires</h1>
                    <p style="color: var(--muted); font-size: 0.95rem;">Profils nutritionnels et objectifs caloriques
                        détaillés.</p>
                </div>
                <div class="table-wrapper">
                    <table class="creative-table">
                        <thead>
                            <tr>
                                <th style="font-size: 0.7rem;">ID</th>
                                <th style="font-size: 0.7rem;">NOM DU RÉGIME</th>
                                <th style="font-size: 0.7rem;">TYPE / OBJECTIF</th>
                                <th style="font-size: 0.7rem;">KCAL</th>
                                <th style="font-size: 0.7rem;">PROT (g)</th>
                                <th style="font-size: 0.7rem;">GLUC (g)</th>
                                <th style="font-size: 0.7rem;">LIP (g)</th>
                                <th style="font-size: 0.7rem;">NOTE</th>
                                <th style="font-size: 0.7rem;">HORAIRES SPORT</th>
                                <th style="text-align: right; font-size: 0.7rem;">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($regimes)): ?>
                                <tr>
                                    <td colspan="9" style="text-align: center; padding: 20px;">Aucun régime trouvé.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($regimes as $r):
                                    $heures = json_decode($r->getHeuresSemaine(), true);
                                    ?>
                                    <tr>
                                        <td style="font-weight: 700; color: var(--muted);">#<?php echo $r->getIdRegime(); ?>
                                        </td>
                                        <td style="font-weight: 800; color: #222; font-size: 0.95rem;">
                                            <?php echo htmlspecialchars($r->getNom()); ?></td>
                                        <td>
                                            <span
                                                style="font-size: 0.65rem; color: #59b84d; font-weight: 800; text-transform: uppercase; background: rgba(89,184,77,0.1); padding: 4px 10px; border-radius: 6px;">
                                                <?php echo str_replace('_', ' ', $r->getType()); ?>
                                            </span>
                                        </td>
                                        <td style="font-weight: 800; color: var(--text);"><?php echo $r->getCalorieJour(); ?>
                                        </td>
                                        <td style="color: #4361ee; font-weight: 700;"><?php echo $r->getProteine(); ?></td>
                                        <td style="color: #ff9f1c; font-weight: 700;"><?php echo $r->getGlucide(); ?></td>
                                        <td style="color: #2ec4b6; font-weight: 700;"><?php echo $r->getLipides(); ?></td>
                                        <td style="font-weight: 800; color: #222; font-size: 0.85rem; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                                            title="<?php echo htmlspecialchars($r->getDescription()); ?>">
                                            <?php echo htmlspecialchars($r->getDescription()) ?: '-'; ?>
                                        </td>
                                        <td>
                                            <div style="display: flex; flex-wrap: wrap; gap: 4px; max-width: 250px;">
                                                <?php if (is_array($heures)):
                                                    foreach ($heures as $j => $h):
                                                        if ($h !== 'Rest-day'): ?>
                                                            <span style="color: #8338ec; font-weight: 700; font-size: 0.85rem;"
                                                                title="<?php echo $j; ?>">
                                                                <?php echo mb_substr($j, 0, 1); ?>:<?php echo $h; ?>
                                                            </span>
                                                        <?php endif; endforeach; endif; ?>
                                            </div>
                                        </td>
                                        <td style="text-align: right;">
                                            <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                                <a href="add_regime.php?id_regime=<?php echo $r->getIdRegime(); ?>"
                                                    class="btn-action-text btn-mod">Mod</a>
                                                <a href="../FrontOffice/delete_regime.php?id=<?php echo $r->getIdRegime(); ?>&redirect=../BackOffice/admin_dashboard.php"
                                                    onclick="return confirm('Supprimer ce régime ?')"
                                                    class="btn-action-text btn-del">Supp</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- SECTION PLANNINGS -->
            <div class="glass-card" style="padding: 40px; background: white; border-radius: 28px; margin-bottom: 40px;">
                <div style="margin-bottom: 30px;">
                    <span class="section-badge" style="margin-bottom: 10px; background: #eef2ff; color: #4361ee;">Étape
                        2</span>
                    <h1 style="font-size: 2.2rem; margin-bottom: 10px; color: var(--text);">Plannings Activités</h1>
                    <p style="color: var(--muted); font-size: 0.95rem;">Détails sportifs, sommeil et statuts de
                        validation.</p>
                </div>
                <div class="table-wrapper">
                    <table class="creative-table">
                        <thead>
                            <tr>
                                <th style="font-size: 0.7rem;">ID</th>
                                <th style="font-size: 0.7rem;">TITRE PLANNING</th>
                                <th style="font-size: 0.7rem; width: 30%;">SPORT DÉTAILLÉ</th>
                                <th style="font-size: 0.7rem;">SOMMEIL</th>
                                <th style="font-size: 0.7rem; width: 20%;">DESCRIPTION</th>
                                <th style="font-size: 0.7rem;">STATUT</th>
                                <th style="text-align: right; font-size: 0.7rem;">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($planningsWithRegimes)): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 20px;">Aucun planning trouvé.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($planningsWithRegimes as $p): ?>
                                    <tr class="planning-row"
                                        data-title="<?php echo htmlspecialchars($p['titre_planning'] . ' ' . $p['nom_regime']); ?>"
                                        data-type="<?php echo htmlspecialchars($p['regime_type']); ?>">
                                        <td style="font-weight: 700; color: var(--muted);">#<?php echo $p['id_planning']; ?>
                                        </td>
                                        <td style="font-weight: 800; color: #222;">
                                            <?php echo htmlspecialchars($p['titre_planning']); ?>
                                            <!-- nom_regime vient directement de l'INNER JOIN SQL -->
                                            <div style="font-size: 0.65rem; color: #4361ee; font-weight: 700; margin-top: 4px;">
                                                RÉGIME: <?php echo htmlspecialchars($p['nom_regime']); ?></div>
                                        </td>
                                        <td>
                                            <div style="font-size: 0.8rem; color: #444; line-height: 1.4;">
                                                <?php
                                                $sport_text = htmlspecialchars(mb_strimwidth($p['programme_sport'], 0, 200, "..."));
                                                $sport_text = preg_replace('/(Lundi|Mardi|Mercredi|Jeudi|Vendredi|Samedi|Dimanche)\s*:/i', '<strong style="color: #222; font-weight: 800;">$1:</strong>', $sport_text);
                                                echo nl2br($sport_text);
                                                ?>
                                            </div>
                                        </td>
                                        <td style="font-weight: 700; color: #444; font-size: 0.85rem;">
                                            <?php echo htmlspecialchars($p['sommeil']); ?>
                                        </td>
                                        <td>
                                            <div style="font-size: 0.75rem; color: var(--muted);">
                                                <?php echo htmlspecialchars(mb_strimwidth($p['description'], 0, 100, "...")); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo $p['statut']; ?>">
                                                <?php echo strtoupper(str_replace('_', ' ', $p['statut'])); ?>
                                            </span>
                                        </td>
                                        <td style="text-align: right;">
                                            <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                                <a href="javascript:void(0)"
                                                    onclick="printDirectPDF(<?php echo $p['id_regime']; ?>)"
                                                    class="btn-action-text" style="background: #000; color: #fff;"
                                                    title="Générer PDF">PDF</a>
                                                <?php if ($p['statut'] == 'en_attente'): ?>
                                                    <a href="admin_dashboard.php?action=upd_s&s=accepte&id=<?php echo $p['id_planning']; ?>"
                                                        class="btn-action-text btn-ok" title="Valider">Accepter</a>
                                                    <a href="admin_dashboard.php?action=reject_p&id=<?php echo $p['id_planning']; ?>"
                                                        class="btn-action-text btn-no" title="Rejeter"
                                                        onclick="return confirm('Refuser et supprimer définitivement ce dossier ?')">Refuser</a>
                                                <?php endif; ?>
                                                <a href="add_programme_back.php?id_planning=<?php echo $p['id_planning']; ?>"
                                                    class="btn-action-text btn-mod">Mod</a>
                                                <a href="admin_dashboard.php?action=del_p&id=<?php echo $p['id_planning']; ?>"
                                                    onclick="return confirm('Supprimer ce planning ?')"
                                                    class="btn-action-text btn-del">Supp</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
        </main>
    </div>

    <script>
        feather.replace();

        // FILTRAGE DYNAMIQUE ADMIN
        const adminSearchInput = document.getElementById('adminSearchInput');
        const adminTypeFilter = document.getElementById('adminTypeFilter');
        const planningRows = document.querySelectorAll('.planning-row');

        function filterAdminPlannings() {
            const term = adminSearchInput.value.toLowerCase();
            const selectedType = adminTypeFilter.value;

            planningRows.forEach(row => {
                const title = row.getAttribute('data-title').toLowerCase();
                const type = row.getAttribute('data-type');

                const matchesSearch = title.includes(term);
                const matchesType = (selectedType === 'all' || type === selectedType);

                if (matchesSearch && matchesType) {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        if (adminSearchInput && adminTypeFilter) {
            adminSearchInput.addEventListener('input', filterAdminPlannings);
            adminTypeFilter.addEventListener('change', filterAdminPlannings);
        }

        // FONCTION EXPORT PDF 
        function printDirectPDF(id) {
            const oldIframe = document.getElementById('pdf-frame');
            if (oldIframe) oldIframe.remove();

            const iframe = document.createElement('iframe');
            iframe.id = 'pdf-frame';
            // On utilise visibility:hidden au lieu de display:none pour forcer le chargement
            iframe.style.position = 'fixed';
            iframe.style.bottom = '0';
            iframe.style.right = '0';
            iframe.style.width = '1px';
            iframe.style.height = '1px';
            iframe.style.border = 'none';
            iframe.style.visibility = 'hidden';

            iframe.src = `../FrontOffice/summary.php?id_regime=${id}&print=1`;
            document.body.appendChild(iframe);
        }
    </script>
</body>

</html>