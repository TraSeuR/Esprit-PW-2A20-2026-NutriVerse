<?php
require_once __DIR__ . '/../../controller/RegimeController.php';
require_once __DIR__ . '/../../controller/PlanningController.php';

$id_regime = isset($_GET['id_regime']) ? $_GET['id_regime'] : null;
$rCtrl = new RegimeController();
$pCtrl = new PlanningController();

$regime = $rCtrl->getRegime($id_regime);
$myPlanning = $pCtrl->getPlanningByRegime($id_regime);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mise à jour du régime
    $rCtrl->updateRegime($id_regime, $_POST);

    // Mise à jour du planning
    if ($myPlanning) {
        $pCtrl->updatePlanning($myPlanning->getIdPlanning(), [
            'programme_sport' => $_POST['programme_sport'],
            'sommeil' => $_POST['sommeil'],
            'titre_planning' => $_POST['titre_planning'],
            'description' => $_POST['description_planning'] ?? ''
        ]);
    }

    header("Location: summary.php?id_regime=" . $id_regime);
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier mon Programme</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="assets/front.css">
    <link rel="stylesheet" href="../assets/style.css?v=1.3">
</head>

<body
    style="background: linear-gradient(135deg, #59b84d 0%, #a8dba0 45%, #ffffff 100%); min-height: 100vh; font-family: 'Poppins', sans-serif;">

    <?php include 'global_header.php'; ?>

    <!-- HERO VERT -->
    <section class="recipe-header fade-up">
        <div class="icons">
            <span>🥑</span>
            <span>🥕</span>
            <span>🥦</span>
            <span>🍎</span>
            <span>🍇</span>
            <span>🥬</span>
            <span>🍅</span>
            <span>🍌</span>
            <span>🍓</span>
            <span>🥒</span>
            <span>🌽</span>
            <span>🍍</span>
            <span>🥭</span>
            <span>🍉</span>
            <span>🥔</span>
        </div>
        <div class="header-content">
            <h1 style="margin-bottom: 0;">NutriVerse</h1>
            <h2 style="font-size: 2rem; opacity: 0.9; font-weight: 700; margin: 10px 0; color: white;">Modification</h2>
        </div>
    </section>

    <div class="container fade-up" style="padding-bottom: 100px;">
        <div class="glass-card" style="max-width: 900px; background: rgba(255,255,255,0.96);">

            <form id="editForm" method="POST" novalidate>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">

                    <!-- SECTION RÉGIME -->
                    <div>
                        <h3
                            style="margin-bottom: 25px; border-bottom: 2px solid var(--primary); padding-bottom: 10px; color: var(--primary);">
                            1. VOS DONNÉES NUTRITION</h3>

                        <div class="form-group">
                            <label>Nom du Régime</label>
                            <input type="text" name="nom" value="<?php echo htmlspecialchars($regime->getNom()); ?>">
                        </div>

                        <div class="form-group">
                            <label>Type d'objectif</label>
                            <select name="type">
                                <option value="perte_poids" <?php if ($regime->getType() == 'perte_poids')
                                    echo 'selected'; ?>>Perte de poids</option>
                                <option value="prise_masse" <?php if ($regime->getType() == 'prise_masse')
                                    echo 'selected'; ?>>Prise de masse</option>
                                <option value="equilibre" <?php if ($regime->getType() == 'equilibre')
                                    echo 'selected'; ?>>
                                    Équilibre Santé</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Calories/Jour</label>
                            <input type="text" name="calorie_jour" value="<?php echo $regime->getCalorieJour(); ?>">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">
                            <div class="form-group">
                                <label>Prot (g)</label>
                                <input type="text" name="proteine" value="<?php echo $regime->getProteine(); ?>">
                            </div>
                            <div class="form-group">
                                <label>Glu (g)</label>
                                <input type="text" name="glucide" value="<?php echo $regime->getGlucide(); ?>">
                            </div>
                            <div class="form-group">
                                <label>Lip (g)</label>
                                <input type="text" name="lipides" value="<?php echo $regime->getLipides(); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- SECTION PLANNING -->
                    <div>
                        <h3
                            style="margin-bottom: 25px; border-bottom: 2px solid #59b84d; padding-bottom: 10px; color: #1b4332; font-family: 'Playfair Display';">
                            2. VOS DONNÉES SPORT</h3>

                        <div class="form-group">
                            <label>Titre du Planning</label>
                            <input type="text" name="titre_planning"
                                value="<?php echo $myPlanning ? htmlspecialchars($myPlanning->getTitrePlanning()) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label>Programme d'activité</label>
                            <textarea name="programme_sport"
                                rows="5"><?php echo $myPlanning ? htmlspecialchars($myPlanning->getProgrammeSport()) : ''; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Sommeil Cible</label>
                            <select name="sommeil">
                                <option value="6-7h" <?php if ($myPlanning && $myPlanning->getSommeil() == '6-7h')
                                    echo 'selected'; ?>>6-7 heures</option>
                                <option value="7-8h" <?php if ($myPlanning && $myPlanning->getSommeil() == '7-8h')
                                    echo 'selected'; ?>>7-8 heures</option>
                                <option value="8-9h" <?php if ($myPlanning && $myPlanning->getSommeil() == '8-9h')
                                    echo 'selected'; ?>>8-9 heures</option>
                            </select>
                        </div>

                        <div class="form-group"
                            style="background: rgba(255,255,255,0.7); padding: 15px; border-radius: 12px; margin-top: 20px; border: 1px solid rgba(0,200,83,0.3);">
                            <label style="font-family: 'Playfair Display'; font-weight: 900; color: #1b4332;">Calendrier
                                sport</label>
                            <p style="font-size: 0.75rem; color: #666; margin-bottom: 10px; font-family: 'Poppins';">
                                Ajustez vos horaires ou choisissez de vous reposer.</p>
                            <div
                                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 10px;">
                                <?php
                                $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
                                $saved_heures = $regime->getHeuresSemaine() ? json_decode($regime->getHeuresSemaine(), true) : [];
                                foreach ($jours as $jour):
                                    $val = isset($saved_heures[$jour]) ? $saved_heures[$jour] : '';
                                    ?>
                                    <div
                                        style="background: #fff; padding: 10px; border-radius: 8px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                                        <label
                                            style="font-size: 0.8rem; font-weight: 700; margin-bottom: 5px; display: block;"><?php echo $jour; ?></label>
                                        <select name="heures_semaine[<?php echo $jour; ?>]"
                                            style="padding: 5px; width: 100%; border-radius: 6px; border: 1px solid #ccc; font-size: 0.8rem; text-align: center;">
                                            <option value="Rest-day" <?php if ($val === 'Rest-day')
                                                echo 'selected'; ?>>
                                                Rest-day</option>
                                            <?php for ($h = 5; $h <= 23; $h++): ?>
                                                <?php $t1 = sprintf('%02d:00', $h); ?>
                                                <option value="<?php echo $t1; ?>" <?php if ($val === $t1)
                                                       echo 'selected'; ?>>
                                              <?php echo $t1; ?></option>
                                                <?php $t2 = sprintf('%02d:30', $h); ?>
                                                <option value="<?php echo $t2; ?>" <?php if ($val === $t2)
                                                       echo 'selected'; ?>>
                                             <?php echo $t2; ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    </div>

                </div>

                <div style="margin-top: 40px; display: flex; gap: 20px;">
                    <a href="summary.php?id_regime=<?php echo $id_regime; ?>" class="btn-premium"
                        style="background: #fff; color: #000; text-decoration: none; text-align: center; border: 1px solid #ddd;">ANNULER</a>
                    <button type="submit" class="btn-premium">ENREGISTRER LES MODIFICATIONS →</button>
                </div>

            </form>
        </div>
    </div>

    <?php include 'coach_widget.php'; ?>
    <script src="assets/front_validation.js"></script>
</body>

</html>