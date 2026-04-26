<?php
require_once __DIR__ . '/../../controller/PlanningController.php';

$pCtrl = new PlanningController();
// INNER JOIN via le Contrôleur : plannings acceptés + données nutritionnelles du régime
$plannings = $pCtrl->listAcceptedPlanningsWithRegimes();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriVerse - Galerie Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body
    style="background: linear-gradient(135deg, #59b84d 0%, #a8dba0 45%, #ffffff 100%); min-height: 100vh; font-family: 'Poppins', sans-serif;">

    <header class="premium-header fade-up">
        <h1 style="color: var(--primary-dark);">Nos Plannings</h1>

    </header>

    <div class="container fade-up" style="animation-delay: 0.2s; padding-bottom: 100px;">

        <div class="search-container" style="display: flex; gap: 15px; justify-content: center; margin-bottom: 40px;">
            <input type="text" id="searchInput" class="search-input"
                style="background: rgba(255,255,255,0.8); backdrop-filter: blur(10px); width: 300px;"
                placeholder="Rechercher un nom...">

            <select id="typeFilter" class="search-input"
                style="background: rgba(255,255,255,0.8); backdrop-filter: blur(10px); width: 180px; cursor: pointer; color: #444; font-weight: 600;">
                <option value="all">Filtrer</option>
                <option value="prise_masse">Prise de masse</option>
                <option value="perte_poids">Perte de poids</option>
                <option value="equilibre">Équilibre santé</option>
            </select>
        </div>

        <div class="mag-grid" id="planningGrid">
            <?php foreach ($plannings as $p):
                $p_perc = min(($p['proteine'] / 200) * 100, 100);
                $g_perc = min(($p['glucide'] / 300) * 100, 100);
                $l_perc = min(($p['lipides'] / 100) * 100, 100);
                ?>
                <div class="glass-card planning-item" style="padding: 0; display: flex; flex-direction: column;"
                    data-title="<?php echo htmlspecialchars($p['titre_planning']); ?>"
                    data-type="<?php echo htmlspecialchars($p['regime_type']); ?>">

                    <!-- HEADER STYLE RAPPORT (COMME CAP 1) -->
                    <div class="dashboard-header"
                        style="padding: 20px 30px; display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <p
                                style="text-transform: uppercase; letter-spacing: 1px; font-weight: 800; font-size: 0.55rem; color: var(--primary); margin-bottom: 5px;">
                                NUTRIVERSE TECHNICAL REPORT</p>
                            <h3 class="dashboard-title" style="font-size: 1.3rem; margin: 0;">
                                <?php echo htmlspecialchars($p['titre_planning']); ?>
                            </h3>
                            <span
                                style="font-size: 0.7rem; font-weight: 700; color: #888;"><?php echo strtoupper(str_replace('_', ' ', $p['regime_type'])); ?></span>
                        </div>
                        <div style="text-align: right;">
                            <span
                                style="font-weight: 900; font-size: 0.8rem; color: var(--primary-dark);">#<?php echo $p['id_regime']; ?>-PREMIUM</span>
                        </div>
                    </div>

                    <div style="padding: 30px; flex-grow: 1;">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 10px;">
                            <span style="font-size: 0.75rem; font-weight: 800; color: #666;">APPORT</span>
                            <span
                                style="font-size: 1.1rem; font-weight: 900; color: var(--primary-dark);"><?php echo $p['calorie_jour']; ?>
                                KCAL</span>
                        </div>

                        <div class="macro-container">
                            <div class="macro-label"><span>P</span> <span><?php echo $p['proteine']; ?>g</span></div>
                            <div class="macro-track">
                                <div class="macro-fill fill-p" style="width: <?php echo $p_perc; ?>%;"></div>
                            </div>
                        </div>
                        <div class="macro-container">
                            <div class="macro-label"><span>G</span> <span><?php echo $p['glucide']; ?>g</span></div>
                            <div class="macro-track">
                                <div class="macro-fill fill-g" style="width: <?php echo $g_perc; ?>%;"></div>
                            </div>
                        </div>
                        <div class="macro-container">
                            <div class="macro-label"><span>L</span> <span><?php echo $p['lipides']; ?>g</span></div>
                            <div class="macro-track">
                                <div class="macro-fill fill-l" style="width: <?php echo $l_perc; ?>%;"></div>
                            </div>
                        </div>

                        <!-- DONNÉES ÉTAPE 2 AJOUTÉES -->
                        <div
                            style="margin-top: 25px; display: grid; grid-template-columns: 1fr 1fr; gap: 15px; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 15px;">
                            <div>
                                <span
                                    style="display: block; font-size: 0.6rem; font-weight: 800; color: #888; text-transform: uppercase;">🌙
                                    SOMMEIL</span>
                                <span
                                    style="font-size: 0.9rem; font-weight: 700; color: #333;"><?php echo htmlspecialchars($p['sommeil']); ?></span>
                            </div>
                            <div>
                                <span
                                    style="display: block; font-size: 0.6rem; font-weight: 800; color: #888; text-transform: uppercase;">🏃
                                    FOCUS SPORT</span>
                                <span
                                    style="font-size: 0.8rem; font-weight: 600; color: #333; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?php echo htmlspecialchars($p['programme_sport']); ?>
                                </span>
                            </div>
                        </div>

                        <div style="margin-top: 15px; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 15px;">
                            <span
                                style="display: block; font-size: 0.6rem; font-weight: 800; color: #888; text-transform: uppercase; margin-bottom: 8px;">Calendrier
                                Sport</span>
                            <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                <?php
                                $heures = isset($p['heures_semaine']) && $p['heures_semaine'] ? json_decode($p['heures_semaine'], true) : [];
                                if ($heures && is_array($heures)) {
                                    foreach ($heures as $j => $h) {
                                        $col = ($h === 'Rest-day' || !$h) ? '#aaa' : 'var(--primary-dark)';
                                        $v = ($h === 'Rest-day' || !$h) ? '-' : $h;
                                        echo '<div style="font-size: 0.65rem; background: rgba(0,0,0,0.03); padding: 4px 6px; border-radius: 4px; border: 1px solid rgba(0,0,0,0.05);"><strong>' . substr($j, 0, 2) . '</strong> <span style="color:' . $col . '; font-weight: 800;">' . $v . '</span></div>';
                                    }
                                } else {
                                    echo '<span style="font-size: 0.7rem; color:#888;">Non défini</span>';
                                }
                                ?>
                            </div>
                        </div>

                    </div>

                    <a href="summary.php?id_regime=<?php echo $p['id_regime']; ?>" class="btn-premium"
                        style="border-radius: 0; padding: 15px; text-align: center; text-decoration: none; font-size: 0.9rem;">DÉTAILS
                        DU PROGRAMME →</a>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align: center; margin-top: 60px;">
            <a href="mode_selection.php" class="btn-premium"
                style="width: auto; padding: 12px 40px; background: #000;">← RETOUR AU MENU</a>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('searchInput');
        const typeFilter = document.getElementById('typeFilter');
        const cards = document.querySelectorAll('.planning-item');

        function filterPlannings() {
            const term = searchInput.value.toLowerCase();
            const selectedType = typeFilter.value;

            cards.forEach(card => {
                const title = card.getAttribute('data-title').toLowerCase();
                const type = card.getAttribute('data-type');

                const matchesSearch = title.includes(term);
                const matchesType = (selectedType === 'all' || type === selectedType);

                if (matchesSearch && matchesType) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        searchInput.addEventListener('input', filterPlannings);
        typeFilter.addEventListener('change', filterPlannings);
    </script>
</body>

</html>