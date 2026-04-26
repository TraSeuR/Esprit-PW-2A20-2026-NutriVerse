<?php
require_once __DIR__ . '/../../controller/RegimeController.php';
require_once __DIR__ . '/../../controller/PlanningController.php';

$rCtrl = new RegimeController();
$pCtrl = new PlanningController();

$id_planning = $_GET['id_planning'] ?? null;
$planning = null;
$regime = null;

if ($id_planning) {
    $planning = $pCtrl->getPlanningById($id_planning);
    if ($planning) {
        $regime = $rCtrl->getRegime($planning->getIdRegime());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($id_planning && $planning && $regime) {
        // --- MODE MODIFICATION ---
        $rCtrl->updateRegime($planning->getIdRegime(), [
            'nom' => $_POST['nom'],
            'type' => $_POST['type'],
            'calorie_jour' => $_POST['calorie_jour'],
            'proteine' => $_POST['proteine'],
            'glucide' => $_POST['glucide'],
            'lipides' => $_POST['lipides'],
            'description' => $_POST['description_regime'],
            'heures_semaine' => $_POST['heures_semaine'] ?? null
        ]);

        $pCtrl->updatePlanning($id_planning, [
            'programme_sport' => $_POST['programme_sport'],
            'sommeil' => $_POST['sommeil'],
            'titre_planning' => $_POST['titre_planning'],
            'description' => $_POST['description_planning'] ?? ''
        ]);

        header("Location: admin_dashboard.php?msg=updated");
        exit();
    } else {
        // --- MODE CRÉATION ---
        $id_regime = $rCtrl->createRegime([
            'nom' => $_POST['nom'],
            'type' => $_POST['type'],
            'calorie_jour' => $_POST['calorie_jour'],
            'proteine' => $_POST['proteine'],
            'glucide' => $_POST['glucide'],
            'lipides' => $_POST['lipides'],
            'description' => $_POST['description_regime'],
            'heures_semaine' => $_POST['heures_semaine'] ?? null
        ]);

        if ($id_regime) {
            $pCtrl->createPlanningAdmin([
                'id_regime' => $id_regime,
                'titre_planning' => $_POST['titre_planning'],
                'programme_sport' => $_POST['programme_sport'],
                'sommeil' => $_POST['sommeil'],
                'description' => $_POST['description_planning'] ?? ''
            ]);

            header("Location: admin_dashboard.php?msg=created");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Ajouter un Planning </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body
    style="background: linear-gradient(135deg, #59b84d 0%, #a8dba0 45%, #ffffff 100%); min-height: 100vh; font-family: 'Poppins', sans-serif;">

    <div class="container fade-up"
        style="max-width: 1100px; margin: 0 auto; padding: 60px 20px; position: relative; z-index: 1;">

        <header style="margin-bottom: 40px; text-align: center;">
            <h1 style="color: #1b4332; font-family: 'Playfair Display'; font-size: 2.8rem;">
                <?php echo $planning ? 'Modifier le Planning Expert' : 'Ajouter un Planning'; ?>
            </h1>

        </header>

        <div class="glass-card" style="padding: 50px; background: rgba(255, 255, 255, 0.95);">
            <form id="adminForm"
                action="add_programme_back.php<?php echo $id_planning ? '?id_planning=' . $id_planning : ''; ?>"
                method="POST" novalidate>

                <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 50px;">

                    <!-- SECTION 1 : NUTRITION -->
                    <div>
                        <h3
                            style="margin-bottom: 25px; border-bottom: 3px solid var(--primary); padding-bottom: 10px; color: var(--primary-dark); font-family: 'Playfair Display';">
                            PROFIL NUTRITIONNEL</h3>

                        <div class="form-group">
                            <label style="font-family: 'Poppins'; font-weight: 700;">Nom du régime</label>
                            <input type="text" name="nom" id="nom" placeholder="Ex: Jean Dupont - Performance"
                                value="<?php echo $regime ? htmlspecialchars($regime->getNom()) : ''; ?>">
                            <span id="error-nom" class="error-text"
                                style="color: #e63946; font-size: 0.75rem; font-weight: 600; margin-top: 5px; display: block;"></span>
                        </div>

                        <div class="form-group">
                            <label style="font-family: 'Poppins'; font-weight: 700;">Objectif Principal</label>
                            <select name="type" id="type">
                                <option value="perte_poids" <?php echo ($regime && $regime->getType() == 'perte_poids') ? 'selected' : ''; ?>>Perte de poids</option>
                                <option value="prise_masse" <?php echo ($regime && $regime->getType() == 'prise_masse') ? 'selected' : ''; ?>>Prise de masse</option>
                                <option value="equilibre" <?php echo ($regime && $regime->getType() == 'equilibre') ? 'selected' : ''; ?>>Équilibre & Santé</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label style="font-family: 'Poppins'; font-weight: 700;">Cible Calorique (Kcal)</label>
                            <input type="text" name="calorie_jour" id="calorie_jour" placeholder="2200"
                                value="<?php echo $regime ? htmlspecialchars($regime->getCalorieJour()) : ''; ?>">
                            <span id="error-calorie_jour" class="error-text"
                                style="color: #e63946; font-size: 0.75rem; font-weight: 600; margin-top: 5px; display: block;"></span>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                            <div class="form-group"><label>Prot (g)</label><input type="text" name="proteine"
                                    id="proteine"
                                    value="<?php echo $regime ? htmlspecialchars($regime->getProteine()) : ''; ?>"><span
                                    id="error-proteine" class="error-text"
                                    style="color: #e63946; font-size: 0.7rem;"></span></div>
                            <div class="form-group"><label>Gluc (g)</label><input type="text" name="glucide"
                                    id="glucide"
                                    value="<?php echo $regime ? htmlspecialchars($regime->getGlucide()) : ''; ?>"><span
                                    id="error-glucide" class="error-text"
                                    style="color: #e63946; font-size: 0.7rem;"></span></div>
                            <div class="form-group"><label>Lip (g)</label><input type="text" name="lipides" id="lipides"
                                    value="<?php echo $regime ? htmlspecialchars($regime->getLipides()) : ''; ?>"><span
                                    id="error-lipides" class="error-text"
                                    style="color: #e63946; font-size: 0.7rem;"></span></div>
                        </div>

                        <div class="form-group" style="margin-top: 10px;">
                            <label>Analyse Nutritionnelle </label>
                            <textarea name="description_regime" id="description_regime"
                                rows="4"><?php echo $regime ? htmlspecialchars($regime->getDescription()) : ''; ?></textarea>
                        </div>

                        <div class="form-group"
                            style="background: rgba(255,255,255,0.7); padding: 15px; border-radius: 12px; border: 1px solid rgba(0,200,83,0.3); margin-top: 20px;">
                            <label
                                style="font-family: 'Playfair Display'; font-weight: 900; font-size: 1.1rem; color: var(--primary-dark); margin-bottom: 15px; display: block; border-bottom: 2px solid var(--primary-color); padding-bottom: 5px;">
                                Calendrier sport
                            </label>
                            <div
                                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(80px, 1fr)); gap: 10px;">
                                <?php
                                $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
                                $saved_hours = [];
                                if ($regime && $regime->getHeuresSemaine()) {
                                    $saved_hours = json_decode($regime->getHeuresSemaine(), true);
                                }
                                foreach ($jours as $jour):
                                    $current_val = $saved_hours[$jour] ?? 'Rest';
                                    ?>
                                    <div
                                        style="background: #fff; padding: 10px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-align: center;">
                                        <label
                                            style="font-family: 'Poppins'; font-weight: 700; font-size: 0.75rem; color: #444; display: block; margin-bottom: 5px;"><?php echo mb_substr($jour, 0, 3); ?>.</label>
                                        <select name="heures_semaine[<?php echo $jour; ?>]"
                                            style="padding: 5px; border-radius: 6px; border: 1px solid #ccc; width: 100%; font-family: 'Poppins'; text-align: center; font-size: 0.75rem;">
                                            <option value="Rest" <?php echo ($current_val == 'Rest' || $current_val == 'Rest-day') ? 'selected' : ''; ?>>Rest</option>
                                            <?php for ($h = 5; $h <= 23; $h++):
                                                $h1 = sprintf('%02d:00', $h);
                                                $h2 = sprintf('%02d:30', $h);
                                                ?>
                                                <option value="<?php echo $h1; ?>" <?php echo ($current_val == $h1) ? 'selected' : ''; ?>><?php echo $h1; ?></option>
                                                <option value="<?php echo $h2; ?>" <?php echo ($current_val == $h2) ? 'selected' : ''; ?>><?php echo $h2; ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2 : PERFORMANCE -->
                    <div>
                        <h3
                            style="margin-bottom: 25px; border-bottom: 3px solid #59b84d; padding-bottom: 10px; color: #1b4332; font-family: 'Playfair Display';">
                            PLAN DE PERFORMANCE</h3>

                        <div class="form-group">
                            <label style="font-family: 'Poppins'; font-weight: 700;">Titre du Planning Sportif</label>
                            <input type="text" name="titre_planning" id="titre_planning"
                                placeholder="Ex: Routine Athlétique"
                                value="<?php echo $planning ? htmlspecialchars($planning->getTitrePlanning()) : ''; ?>">
                            <span id="error-titre_planning" class="error-text"
                                style="color: #e63946; font-size: 0.75rem; font-weight: 600; margin-top: 5px; display: block;"></span>
                        </div>

                        <div class="form-group">
                            <label style="font-family: 'Poppins'; font-weight: 700;">Cycle de Sommeil Cible</label>
                            <select name="sommeil" id="sommeil">
                                <option value="6-7h" <?php echo ($planning && $planning->getSommeil() == '6-7h') ? 'selected' : ''; ?>>6-7 heures</option>
                                <option value="7-8h" <?php echo (!$planning || $planning->getSommeil() == '7-8h') ? 'selected' : ''; ?>>7-8 heures</option>
                                <option value="8-9h" <?php echo ($planning && $planning->getSommeil() == '8-9h') ? 'selected' : ''; ?>>8-9 heures</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label style="font-family: 'Poppins'; font-weight: 700;">Directives Sportives
                                Journalières</label>

                            <div style="margin-bottom: 15px; display: flex; gap: 10px; flex-wrap: wrap;">
                                <button type="button" class="btn-premium"
                                    style="width: auto; padding: 8px 15px; font-size: 0.7rem; background: #eee; color: #333;"
                                    onclick="insertDay('Lundi')">LUNDI</button>
                                <button type="button" class="btn-premium"
                                    style="width: auto; padding: 8px 15px; font-size: 0.7rem; background: #eee; color: #333;"
                                    onclick="insertDay('Mardi')">MARDI</button>
                                <button type="button" class="btn-premium"
                                    style="width: auto; padding: 8px 15px; font-size: 0.7rem; background: #eee; color: #333;"
                                    onclick="insertDay('Mercredi')">MERCREDI</button>
                                <button type="button" class="btn-premium"
                                    style="width: auto; padding: 8px 15px; font-size: 0.7rem; background: #eee; color: #333;"
                                    onclick="insertDay('Jeudi')">JEUDI</button>
                                <button type="button" class="btn-premium"
                                    style="width: auto; padding: 8px 15px; font-size: 0.7rem; background: #eee; color: #333;"
                                    onclick="insertDay('Vendredi')">VENDREDI</button>
                                <button type="button" class="btn-premium"
                                    style="width: auto; padding: 8px 15px; font-size: 0.7rem; background: #eee; color: #333;"
                                    onclick="insertDay('Samedi')">SAMEDI</button>
                                <button type="button" class="btn-premium"
                                    style="width: auto; padding: 8px 15px; font-size: 0.7rem; background: #eee; color: #333;"
                                    onclick="insertDay('Dimanche')">DIMANCHE</button>
                            </div>

                            <textarea name="programme_sport" id="programme_sport" rows="11"
                                placeholder="Votre programme pour cette semaine"><?php echo $planning ? htmlspecialchars($planning->getProgrammeSport()) : ''; ?></textarea>
                            <span id="error-programme_sport" class="error-text"
                                style="color: #e63946; font-size: 0.75rem; font-weight: 600; margin-top: 5px; display: block;"></span>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 40px; display: flex; gap: 20px; justify-content: flex-end;">
                    <a href="admin_dashboard.php" class="btn-premium"
                        style="width: auto; background: #fff; color: #333; border: 1px solid #ddd; text-decoration: none;">ANNULER</a>
                    <button type="submit" class="btn-premium"
                        style="width: auto; padding: 15px 50px; font-weight: 700; border-radius: 12px; background: #59b84d;">
                        <?php echo $planning ? 'ENREGISTRER LES MODIFICATIONS' : 'AJOUTER LE PLANNING COMPLET'; ?>
                    </button>
                </div>

            </form>
        </div>
    </div>

    <script src="assets/back_validation.js?v=<?php echo time(); ?>"></script>
</body>

</html>