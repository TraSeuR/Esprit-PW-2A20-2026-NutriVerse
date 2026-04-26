<?php
require_once __DIR__ . '/../../controller/RegimeController.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_regime = $_GET['id_regime'] ?? null;

if (!isset($_GET['id_regime'])) {
    unset($_SESSION['last_id_regime']);
    $id_regime = null;
}

$controller = new RegimeController();

// Use the controller; indicate source='back' so it redirects to dashboard on save
$regime = $controller->handleRequest($id_regime, 'back');
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Ajouter un Régime</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body style="background: linear-gradient(135deg, #59b84d 0%, #a8dba0 45%, #ffffff 100%); min-height: 100vh;">

    <div class="container fade-up"
        style="max-width: 900px; margin: 0 auto; padding: 60px 20px; position: relative; z-index: 1;">

        <header style="margin-bottom: 40px; text-align: center;">
            <h1 style="color: white; font-family: 'Playfair Display'; font-size: 2.8rem;">
                <?php echo $regime ? "Modifier le régime" : "Ajouter un Régime "; ?>
            </h1>
            <p style="color: rgba(255,255,255,0.7); font-family: 'Poppins'; font-weight: 500;"></p>
        </header>

        <div class="glass-card" style="padding: 50px; background: rgba(255, 255, 255, 0.95);">
            <form id="regimeFormAdmin"
                action="add_regime.php<?php echo $id_regime ? '?id_regime=' . $id_regime : ''; ?>" method="POST"
                novalidate>

                <h3
                    style="margin-bottom: 25px; border-bottom: 3px solid var(--primary); padding-bottom: 10px; color: var(--primary-dark); font-family: 'Playfair Display';">
                    Formulaire</h3>

                <div class="form-group">
                    <label style="font-family: 'Poppins'; font-weight: 700;">Nom du Régime</label>
                    <input type="text" name="nom" id="nom" placeholder="Ex: Jean Dupont - Performance"
                        value="<?php echo $regime ? htmlspecialchars($regime->getNom()) : ''; ?>">
                    <span id="error-nom" class="error-text"
                        style="color: #e63946; font-size: 0.75rem; font-weight: 600; margin-top: 5px; display: block;"></span>
                </div>

                <div class="form-group">
                    <label style="font-family: 'Poppins'; font-weight: 700;">Objectif Principal</label>
                    <select name="type" id="type">
                        <option value="">Sélectionnez un objectif</option>
                        <option value="perte_poids" <?php echo ($regime && $regime->getType() == 'perte_poids') ? 'selected' : ''; ?>>Perte de poids</option>
                        <option value="prise_masse" <?php echo ($regime && $regime->getType() == 'prise_masse') ? 'selected' : ''; ?>>Prise de masse</option>
                        <option value="equilibre" <?php echo ($regime && $regime->getType() == 'equilibre') ? 'selected' : ''; ?>>Équilibre & Santé</option>
                    </select>
                    <span id="error-type" class="error-text"
                        style="color: #e63946; font-size: 0.75rem; font-weight: 600; margin-top: 5px; display: block;"></span>
                </div>

                <div class="form-group">
                    <label style="font-family: 'Poppins'; font-weight: 700;"> Calories (Kcal)</label>
                    <input type="text" name="calorie_jour" id="calorie_jour" placeholder="Ex: 2200"
                        value="<?php echo $regime ? htmlspecialchars($regime->getCalorieJour()) : ''; ?>">
                    <span id="error-calorie_jour" class="error-text"
                        style="color: #e63946; font-size: 0.75rem; font-weight: 600; margin-top: 5px; display: block;"></span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Prot (g)</label>
                        <input type="text" name="proteine" id="proteine"
                            value="<?php echo $regime ? htmlspecialchars($regime->getProteine()) : ''; ?>">
                        <span id="error-proteine" class="error-text"
                            style="color: #e63946; font-size: 0.7rem; font-weight: 600; margin-top: 5px; display: block;"></span>
                    </div>
                    <div class="form-group">
                        <label>Gluc (g)</label>
                        <input type="text" name="glucide" id="glucide"
                            value="<?php echo $regime ? htmlspecialchars($regime->getGlucide()) : ''; ?>">
                        <span id="error-glucide" class="error-text"
                            style="color: #e63946; font-size: 0.7rem; font-weight: 600; margin-top: 5px; display: block;"></span>
                    </div>
                    <div class="form-group">
                        <label>Lip (g)</label>
                        <input type="text" name="lipides" id="lipides"
                            value="<?php echo $regime ? htmlspecialchars($regime->getLipides()) : ''; ?>">
                        <span id="error-lipides" class="error-text"
                            style="color: #e63946; font-size: 0.7rem; font-weight: 600; margin-top: 5px; display: block;"></span>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 10px;">
                    <label>Note d'intention (Analyse Nutritionnelle )</label>
                    <textarea name="description" id="description"
                        rows="4"><?php echo $regime ? htmlspecialchars($regime->getDescription()) : ''; ?></textarea>
                </div>

                <div class="form-group"
                    style="background: rgba(255,255,255,0.7); padding: 15px; border-radius: 12px; border: 1px solid rgba(0,200,83,0.3); margin-top: 20px;">
                    <label
                        style="font-family: 'Playfair Display'; font-weight: 900; font-size: 1.1rem; color: var(--primary-dark); margin-bottom: 15px; display: block; border-bottom: 2px solid var(--primary-color); padding-bottom: 5px;">
                        Calendrier sport
                    </label>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(80px, 1fr)); gap: 10px;">
                        <?php
                        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
                        $saved_hours = [];
                        if ($regime && $regime->getHeuresSemaine()) {
                            $saved_hours = json_decode($regime->getHeuresSemaine(), true);
                        }
                        foreach ($jours as $jour):
                            $current_val = $saved_hours[$jour] ?? 'Rest-day';
                            ?>
                            <div
                                style="background: #fff; padding: 10px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-align: center;">
                                <label
                                    style="font-family: 'Poppins'; font-weight: 700; font-size: 0.75rem; color: #444; display: block; margin-bottom: 5px;"><?php echo mb_substr($jour, 0, 3); ?>.</label>
                                <select name="heures_semaine[<?php echo $jour; ?>]"
                                    style="padding: 5px; border-radius: 6px; border: 1px solid #ccc; width: 100%; font-family: 'Poppins'; text-align: center; font-size: 0.75rem;">
                                    <option value="Rest-day" <?php echo ($current_val == 'Rest-day') ? 'selected' : ''; ?>>
                                        Rest</option>
                                    <?php for ($h = 5; $h <= 23; $h++):
                                        $h1 = sprintf('%02d:00', $h);
                                        $h2 = sprintf('%02d:30', $h);
                                        ?>
                                        <option value="<?php echo $h1; ?>" <?php echo ($current_val == $h1) ? 'selected' : ''; ?>>
                                            <?php echo $h1; ?>
                                        </option>
                                        <option value="<?php echo $h2; ?>" <?php echo ($current_val == $h2) ? 'selected' : ''; ?>>
                                            <?php echo $h2; ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div style="margin-top: 40px; display: flex; gap: 20px; justify-content: flex-end;">
                    <a href="admin_dashboard.php" class="btn-premium"
                        style="width: auto; background: #fff; color: #333; border: 1px solid #ddd; text-decoration: none;">ANNULER</a>
                    <button type="submit" class="btn-premium"
                        style="width: auto; padding: 15px 50px; font-weight: 700; border-radius: 12px; background: #59b84d;">ENREGISTRER
                        LE RÉGIME</button>
                </div>

            </form>
        </div>
    </div>

    <!-- On utilise la même logique de validation que le Front car c'est le même formulaire -->
    <script src="../FrontOffice/assets/front_validation.js"></script>
    <script>
        // Adapter le script de validation car l'ID du formulaire est différent si besoin
        // Le front_validation ecoute "regimeForm", ici on a mis id="regimeFormAdmin"
        // Faisons marcher le front_validation dessus:
        document.getElementById('regimeFormAdmin').id = 'regimeForm';
    </script>
</body>

</html>