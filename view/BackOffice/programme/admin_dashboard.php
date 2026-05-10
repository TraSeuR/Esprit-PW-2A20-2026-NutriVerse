<?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);


require_once __DIR__ . '/../../../controller/RegimeC.php';
require_once __DIR__ . '/../../../controller/PlanningC.php';

$regimeCtrl = new RegimeC();
$regimes = $regimeCtrl->listRegimes(); // utilisÃ© pour le tableau des RÃ©gimes (Ã‰Étape 1)

$planningCtrl = new PlanningC();
// INNER JOIN : rÃ©cupÃ¨re les plannings avec le nom du rÃ©gime associÃ© en une seule requÃªte
$planningsWithRegimes = $planningCtrl->listPlanningsWithRegimes();
$plannings = $planningCtrl->listPlannings(); // utilisÃ© uniquement pour les compteurs

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
        // Le refus entraÃ®ne la suppression complÃ¨te du dossier (RÃ©gime + Planning)
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
    <link rel="stylesheet" href="../assets/back.css">
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
<style>
    .user-menu-container { position: relative; }
    .user-dropdown {
      position: absolute; top: 110%; right: 0; width: 220px;
      background: #fff; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.15);
      z-index: 10001; display: none; border: 1px solid #eee; overflow: hidden;
    }
    .user-dropdown.show { display: block; animation: slideDownUser 0.2s ease; }
    .user-dropdown a {
      display: flex; align-items: center; gap: 10px; padding: 12px 20px;
      color: #333; text-decoration: none; font-size: 14px; transition: 0.2s;
      text-align: left;
    }
    .user-dropdown a:hover { background: #f9f9f9; color: #27ae60; }
    .user-dropdown a.logout { color: #e74c3c; border-top: 1px solid #eee; }
    .user-dropdown a.logout:hover { background: #fff5f5; }
    .admin-box { cursor: pointer; transition: 0.2s; }
    .admin-box:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    @keyframes slideDownUser { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
</style>
<body>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/integ/view/BackOffice/sidebar.php'; ?>

    <div class="main-content">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/integ/view/BackOffice/topbar.php'; ?>

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
                        AJOUTER REGIME
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
                        <h2><?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo $totalR; ?></h2>
                    </div>
                    <div class="stat-icon green"><i data-feather="bar-chart"></i></div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <p>En Attente</p>
                        <h2><?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo $pnd; ?></h2>
                    </div>
                    <div class="stat-icon orange"><i data-feather="clock"></i></div>
                </div>
            </section>

            <!-- SECTION ReGIMES -->
            <div class="glass-card" style="padding: 40px; background: white; border-radius: 28px; margin-bottom: 40px;">
                <div style="margin-bottom: 30px;">
                    <span class="section-badge" style="margin-bottom: 10px;">Étape 1</span>
                    <h1 style="font-size: 2.2rem; margin-bottom: 10px; color: var(--text);">Régimes Alimentaires</h1>
                    <p style="color: var(--muted); font-size: 0.95rem;">Profils nutritionnels et objectifs caloriques
                        détailles.</p>
                </div>
                <div class="table-wrapper">
                    <table class="creative-table">
                        <thead>
                            <tr>
                                <th style="font-size: 0.7rem;">ID</th>
                                <th style="font-size: 0.7rem;">NOM DU REGIME</th>
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
                            <?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 if (empty($regimes)): ?>
                                <tr>
                                    <td colspan="9" style="text-align: center; padding: 20px;">Aucun régime trouvé.</td>
                                </tr>
                            <?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 else: ?>
                                <?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 foreach ($regimes as $r):
                                    $heures = json_decode($r->getHeuresSemaine(), true);
                                    ?>
                                    <tr>
                                        <td style="font-weight: 700; color: var(--muted);">#<?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo $r->getIdRegime(); ?>
                                        </td>
                                        <td style="font-weight: 800; color: #222; font-size: 0.95rem;">
                                            <?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo htmlspecialchars($r->getNom()); ?></td>
                                        <td>
                                            <span
                                                style="font-size: 0.65rem; color: #59b84d; font-weight: 800; text-transform: uppercase; background: rgba(89,184,77,0.1); padding: 4px 10px; border-radius: 6px;">
                                                <?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo str_replace('_', ' ', $r->getType()); ?>
                                            </span>
                                        </td>
                                        <td style="font-weight: 800; color: var(--text);"><?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo $r->getCalorieJour(); ?>
                                        </td>
                                        <td style="color: #4361ee; font-weight: 700;"><?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo $r->getProteine(); ?></td>
                                        <td style="color: #ff9f1c; font-weight: 700;"><?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo $r->getGlucide(); ?></td>
                                        <td style="color: #2ec4b6; font-weight: 700;"><?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo $r->getLipides(); ?></td>
                                        <td style="font-weight: 800; color: #222; font-size: 0.85rem; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                                            title="<?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo htmlspecialchars($r->getDescription()); ?>">
                                            <?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo htmlspecialchars($r->getDescription()) ?: '-'; ?>
                                        </td>
                                        <td>
                                            <div style="display: flex; flex-wrap: wrap; gap: 4px; max-width: 250px;">
                                                <?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 if (is_array($heures)):
                                                    foreach ($heures as $j => $h):
                                                        if ($h !== 'Rest-day'): ?>
                                                            <span style="color: #8338ec; font-weight: 700; font-size: 0.85rem;"
                                                                title="<?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo $j; ?>">
                                                                <?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo mb_substr($j, 0, 1); ?>:<?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo $h; ?>
                                                            </span>
                                                        <?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 endif; endforeach; endif; ?>
                                            </div>
                                        </td>
                                        <td style="text-align: right;">
                                            <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                                <a href="add_regime.php?id_regime=<?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo $r->getIdRegime(); ?>"
                                                    class="btn-action-text btn-mod">Mod</a>
                                                <a href="../../FrontOffice/programme/delete_regime.php?id=<?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo $r->getIdRegime(); ?>&redirect=../../BackOffice/programme/admin_dashboard.php"
                                                    onclick="return confirm('Supprimer ce rÃ©gime ?')"
                                                    class="btn-action-text btn-del">Supp</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 endforeach; ?>
                            <?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 endif; ?>
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
                                <th style="font-size: 0.7rem; width: 30%;">SPORT DETAILLES</th>
                                <th style="font-size: 0.7rem;">SOMMEIL</th>
                                <th style="font-size: 0.7rem; width: 20%;">DESCRIPTION</th>
                                <th style="font-size: 0.7rem;">STATUT</th>
                                <th style="text-align: right; font-size: 0.7rem;">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 if (empty($planningsWithRegimes)): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 20px;">Aucun planning trouvé.</td>
                                </tr>
                            <?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 else: ?>
                                <?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 foreach ($planningsWithRegimes as $p): ?>
                                    <tr class="planning-row"
                                        data-title="<?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo htmlspecialchars($p['titre_planning'] . ' ' . $p['nom_regime']); ?>"
                                        data-type="<?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo htmlspecialchars($p['regime_type']); ?>">
                                        <td style="font-weight: 700; color: var(--muted);">#<?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo $p['id_planning']; ?>
                                        </td>
                                        <td style="font-weight: 800; color: #222;">
                                            <?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo htmlspecialchars($p['titre_planning']); ?>
                                            <!-- nom_regime vient directement de l'INNER JOIN SQL -->
                                            <div style="font-size: 0.65rem; color: #4361ee; font-weight: 700; margin-top: 4px;">
                                                RÃ‰GIME: <?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo htmlspecialchars($p['nom_regime']); ?></div>
                                        </td>
                                        <td>
                                            <div style="font-size: 0.8rem; color: #444; line-height: 1.4;">
                                                <?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);


                                                $sport_text = htmlspecialchars(mb_strimwidth($p['programme_sport'], 0, 200, "..."));
                                                $sport_text = preg_replace('/(Lundi|Mardi|Mercredi|Jeudi|Vendredi|Samedi|Dimanche)\s*:/i', '<strong style="color: #222; font-weight: 800;">$1:</strong>', $sport_text);
                                                echo nl2br($sport_text);
                                                ?>
                                            </div>
                                        </td>
                                        <td style="font-weight: 700; color: #444; font-size: 0.85rem;">
                                            <?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo htmlspecialchars($p['sommeil']); ?>
                                        </td>
                                        <td>
                                            <div style="font-size: 0.75rem; color: var(--muted);">
                                                <?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo htmlspecialchars(mb_strimwidth($p['description'], 0, 100, "...")); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo $p['statut']; ?>">
                                                <?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo strtoupper(str_replace('_', ' ', $p['statut'])); ?>
                                            </span>
                                        </td>
                                        <td style="text-align: right;">
                                            <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                                <a href="javascript:void(0)"
                                                    onclick="printDirectPDF(<?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo $p['id_regime']; ?>)"
                                                    class="btn-action-text" style="background: #000; color: #fff;"
                                                    title="GÃ©nÃ©rer PDF">PDF</a>
                                                <?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 if ($p['statut'] == 'en_attente'): ?>
                                                    <a href="admin_dashboard.php?action=upd_s&s=accepte&id=<?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo $p['id_planning']; ?>"
                                                        class="btn-action-text btn-ok" title="Valider">Accepter</a>
                                                    <a href="admin_dashboard.php?action=reject_p&id=<?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo $p['id_planning']; ?>"
                                                        class="btn-action-text btn-no" title="Rejeter"
                                                        onclick="return confirm('Refuser et supprimer dÃ©finitivement ce dossier ?')">Refuser</a>
                                                <?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 endif; ?>
                                                <a href="add_programme_back.php?id_planning=<?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo $p['id_planning']; ?>"
                                                    class="btn-action-text btn-mod">Mod</a>
                                                <a href="admin_dashboard.php?action=del_p&id=<?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 echo $p['id_planning']; ?>"
                                                    onclick="return confirm('Supprimer ce planning ?')"
                                                    class="btn-action-text btn-del">Supp</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 endforeach; ?>
                            <?php
require_once __DIR__ . '/../../../Controller/rbac_guard.php';
rbac_check(['Responsable Programmes']);

 endif; ?>
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

            iframe.src = `../../FrontOffice/programme/summary.php?id_regime=${id}&print=1`;
            document.body.appendChild(iframe);
        }
    </script>
</body>

</html>





