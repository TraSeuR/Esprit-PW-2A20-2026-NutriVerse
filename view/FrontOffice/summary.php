<?php
require_once __DIR__ . '/../../controller/PlanningController.php';

$id_regime = isset($_GET['id_regime']) ? $_GET['id_regime'] : null;

$pCtrl = new PlanningController();

// INNER JOIN : récupère le planning ET le régime en une seule requête SQL
$data = $pCtrl->getPlanningWithRegime($id_regime);

if (!$data) {
    echo "Programme introuvable.";
    exit();
}

// Calcul des macros pour le dashboard
$p_perc = min(($data['proteine'] / 200) * 100, 100);
$g_perc = min(($data['glucide'] / 300) * 100, 100);
$l_perc = min(($data['lipides'] / 100) * 100, 100);
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analyse - <?php echo htmlspecialchars($data['nom']); ?></title>

    <!-- Nutrition styles -->
    <link rel="stylesheet" href="assets/front.css">
    <style>
        @media print {

            footer,
            .no-print,
            .btn-premium,
            #action-footer,
            header,
            .header {
                display: none !important;
                visibility: hidden !important;
                opacity: 0 !important;
                height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                position: absolute !important;
                top: -9999px !important;
            }
        }
    </style>
    <!-- Existing programme styles -->
    <link rel="stylesheet" href="../assets/style.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">


</head>

<body
    style="background: linear-gradient(135deg, #59b84d 0%, #a8dba0 45%, #ffffff 100%); min-height: 100vh; font-family: 'Poppins', sans-serif;">

    <!-- SHARED NAVBAR -->
    <header class="header">
        <div class="container nav">
            <div class="logo">
                <img src="images/logo.png" alt="Logo NutriVerse" class="logo-img">
            </div>
            <nav class="navbar">
                <a href="nutri_front.php">Accueil</a>
                <a href="nutri_front.php#categories">Marketplace</a>
                <a href="nutri_front.php#recipes">Recettes</a>
                <a href="mode_selection.php" class="active">Programmes</a>
                <a href="nutri_front.php#suivi">Suivi</a>
                <a href="#" class="btn-primary">Mon Compte</a>
            </nav>
        </div>
    </header>



    <div class="container fade-up" style="max-width: 1200px; padding: 120px 20px 60px;">

        <div class="glass-dashboard-card">

            <!-- HEADER DASHBOARD DENSE -->
            <header class="dashboard-header"
                style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <p
                        style="text-transform: uppercase; letter-spacing: 2px; font-weight: 800; font-size: 0.7rem; color: var(--primary);">
                        NUTRIVERSE TECHNICAL REPORT</p>
                    <h1 class="dashboard-title">Dashboard : <?php echo htmlspecialchars($data['nom']); ?></h1>
                </div>
                <div style="text-align: right;">
                    <span
                        style="font-weight: 900; font-size: 1.2rem; color: var(--primary-dark);">#<?php echo $data['id_regime']; ?>-PREMIUM</span>
                </div>
            </header>

            <div class="report-dual-grid" style="padding: 40px;">

                <!-- COLONNE 1 : ANALYSE NUTRITIONNELLE -->
                <div class="report-col">
                    <h3
                        style="font-family: 'Playfair Display'; border-bottom: 1px solid rgba(0,0,0,0.1); padding-bottom: 15px; margin-bottom: 30px;">
                        1. Analyse de la Structure</h3>

                    <div
                        style="background: rgba(255,255,255,0.4); padding: 25px; border-radius: 12px; margin-bottom: 30px; border: 1px solid rgba(255,255,255,0.5);">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span style="font-weight: 800; font-size: 0.8rem; color: #444;">APPORT ÉNERGÉTIQUE
                                TOTAL</span>
                            <span
                                style="font-weight: 900; color: var(--primary-dark);"><?php echo $data['calorie_jour']; ?>
                                KCAL</span>
                        </div>
                        <div class="macro-track">
                            <div class="macro-fill" style="width: 100%; background: var(--primary);"></div>
                        </div>
                    </div>

                    <div class="macro-container">
                        <div class="macro-label"><span>Protéines (Mass)</span>
                            <span><?php echo $data['proteine']; ?>g</span></div>
                        <div class="macro-track">
                            <div class="macro-fill fill-p" style="width: <?php echo $p_perc; ?>%;"></div>
                        </div>
                    </div>
                    <div class="macro-container">
                        <div class="macro-label"><span>Glucides (Energie)</span>
                            <span><?php echo $data['glucide']; ?>g</span></div>
                        <div class="macro-track">
                            <div class="macro-fill fill-g" style="width: <?php echo $g_perc; ?>%;"></div>
                        </div>
                    </div>
                    <div class="macro-container">
                        <div class="macro-label"><span>Lipides (Vitalité)</span>
                            <span><?php echo $data['lipides']; ?>g</span></div>
                        <div class="macro-track">
                            <div class="macro-fill fill-l" style="width: <?php echo $l_perc; ?>%;"></div>
                        </div>
                    </div>

                    <div style="margin-top: 40px; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 20px;">
                        <strong
                            style="display: block; font-size: 0.7rem; margin-bottom: 10px; color: #666;">INSTRUCTIONS
                            DÉTAILLÉES</strong>
                        <p style="font-size: 0.95rem; line-height: 1.8; color: #222;">
                            <?php echo nl2br(htmlspecialchars($data['description'] ?? '')); ?></p>
                    </div>
                </div>

                <!-- COLONNE 2 : SYSTÈME DE PERFORMANCE -->
                <div class="report-col">
                    <h3
                        style="font-family: 'Playfair Display'; border-bottom: 1px solid rgba(0,0,0,0.1); padding-bottom: 15px; margin-bottom: 30px;">
                        2. Plan de Performance</h3>

                    <div
                        style="border-left: 5px solid var(--primary-dark); padding: 25px; background: rgba(255,255,255,0.3); margin-bottom: 30px;">
                        <strong style="display: block; font-size: 0.7rem; color: #666; margin-bottom: 10px;">OBJECTIF
                            PLANNING</strong>
                        <h4
                            style="font-size: 1.4rem; margin-bottom: 10px; font-family: 'Playfair Display'; color: #000;">
                            <?php echo htmlspecialchars($data['titre_planning'] ?? 'Standard Plan'); ?></h4>
                        <p style="font-size: 0.95rem; line-height: 1.8; color: #333;">
                            <?php echo isset($data['programme_sport']) ? nl2br(htmlspecialchars($data['programme_sport'])) : 'Consignes générales : 30min de marche quotidienne.'; ?>
                        </p>
                    </div>

                    <div
                        style="background: rgba(255,255,255,0.5); padding: 20px; border-radius: 12px; margin-bottom: 30px; border: 1px solid rgba(255,255,255,0.5);">
                        <strong style="display: block; font-size: 0.7rem; color: #666; margin-bottom: 15px;">Calendrier
                            sport</strong>
                        <?php
                        $heures = !empty($data['heures_semaine']) ? json_decode($data['heures_semaine'], true) : null;
                        if ($heures && is_array($heures)):
                            ?>
                            <div
                                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(70px, 1fr)); gap: 10px;">
                                <?php foreach ($heures as $j => $h): ?>
                                    <div
                                        style="background: #fff; padding: 10px 5px; border-radius: 8px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
                                        <b
                                            style="font-size: 0.7rem; color: #888; display: block; margin-bottom: 5px; text-transform: uppercase;"><?php echo htmlspecialchars(substr($j, 0, 3)); ?></b>
                                        <span
                                            style="font-size: 0.85rem; font-weight: 800; color: <?php echo ($h && $h !== 'Rest-day') ? 'var(--primary-dark)' : '#aaa'; ?>;">
                                            <?php echo htmlspecialchars($h ? $h : '-'); ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p style="font-size: 0.85rem; color: #666;">Aucun horaire de sport défini.</p>
                        <?php endif; ?>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div
                            style="background: rgba(255,255,255,0.5); padding: 20px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.5);">
                            <span
                                style="display: block; font-size: 0.65rem; font-weight: 800; color: #666; margin-bottom: 10px;">CYCLE
                                SOMMEIL</span>
                            <div style="font-size: 1.1rem; font-weight: 900;">🌙
                                <?php echo htmlspecialchars($data['sommeil'] ?? '8h Cible'); ?></div>
                        </div>
                        <div
                            style="background: rgba(255,255,255,0.5); padding: 20px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.5);">
                            <span
                                style="display: block; font-size: 0.65rem; font-weight: 800; color: #666; margin-bottom: 10px;">SESSION
                                STATUS</span>
                            <div style="font-size: 0.8rem; font-weight: 900; color: var(--primary-dark);">✓ ACTIF</div>
                        </div>
                    </div>
                </div>

            </div>

            <?php if (!isset($_GET['print']) || $_GET['print'] !== '1'): ?>
                <footer id="action-footer" class="no-print"
                    style="padding: 40px; border-top: 1px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.2); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                        <a href="mode_selection.php" class="btn-premium"
                            style="width: auto; padding: 12px 30px; font-size: 0.85rem; background: #fff; color: #000; font-weight: 800;">RETOUR</a>
                        <a href="view_ready_plannings.php" class="btn-premium"
                            style="width: auto; padding: 12px 30px; font-size: 0.85rem; background: var(--primary-dark); font-weight: 800;">VOIR
                            DANS LA GALERIE</a>
                        <a href="edit_programme_front.php?id_regime=<?php echo $data['id_regime']; ?>" class="btn-premium"
                            style="width: auto; padding: 12px 30px; font-size: 0.85rem; background: #59b84d; font-weight: 800;">MODIFIER</a>
                        <a href="javascript:confirmDelete(<?php echo $data['id_regime']; ?>)" class="btn-premium"
                            style="width: auto; padding: 12px 30px; font-size: 0.85rem; background: #e63946; font-weight: 800;">SUPPRIMER</a>
                    </div>
                    <a href="javascript:window.print()" class="btn-premium"
                        style="width: auto; padding: 12px 30px; font-size: 0.85rem; background: #000; font-weight: 800;">TÉLÉCHARGER
                        LE RAPPORT</a>
                </footer>
            <?php endif; ?>

        </div>
    </div>

    <script>
        function confirmDelete(id) {
            if (confirm("Êtes-vous sûr de vouloir supprimer définitivement ce programme et toutes les données associées ?")) {
                window.location.href = "delete_programme_front.php?id_regime=" + id;
            }
        }

        // Déclenchement automatique du PDF si demandé (avec petit délai pour le chargement CSS)
        window.onload = function () {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('print') === '1') {
                setTimeout(() => {
                    window.print();
                }, 500);
            }
        }
    </script>

</body>

</html>