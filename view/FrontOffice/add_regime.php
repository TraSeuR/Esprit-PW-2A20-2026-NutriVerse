<?php
require_once __DIR__ . '/../../controller/RegimeController.php';

$id_regime = $_GET['id_regime'] ?? null;
$source = $_GET['source'] ?? null;
$action = $_GET['action'] ?? null;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($action === 'new') {
    unset($_SESSION['last_id_regime']);
    $id_regime = null;
}

$controller = new RegimeController();

// Le contrôleur gère la logique (POST et chargement des données)
$regime = $controller->handleRequest($id_regime, $source);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriVerse - Votre Planning</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body style="background: linear-gradient(135deg, #59b84d 0%, #a8dba0 45%, #ffffff 100%); min-height: 100vh;">

    <header class="premium-header fade-up">
        <h1 style="font-family: 'Playfair Display';">Étape 1</h1>

    </header>

    <div class="container fade-up" style="animation-delay: 0.2s; padding-bottom: 80px;">
        <div class="glass-card">
            <h2 class="form-title" style="font-family: 'Playfair Display';">Mon régime</h2>

            <!-- NOVAlIDATE pour désactiver HTML5 -->
            <?php
            $actionUrl = "add_regime.php";
            $queryParams = [];
            if ($id_regime)
                $queryParams[] = "id_regime=" . $id_regime;
            if ($source)
                $queryParams[] = "source=" . $source;
            if (!empty($queryParams))
                $actionUrl .= "?" . implode("&", $queryParams);
            ?>
            <form id="regimeForm" action="<?php echo $actionUrl; ?>" method="POST" novalidate>
                <div class="form-group">
                    <label style="font-family: 'Poppins'; font-weight: 700;">Nom du Planning</label>
                    <input type="text" name="nom" id="nom" placeholder="Ex: Ma Cure Détox"
                        value="<?php echo $regime ? htmlspecialchars($regime->getNom()) : ''; ?>">
                    <span id="error-nom" class="error-text"
                        style="color: #e63946; font-size: 0.75rem; font-weight: 600; margin-top: 5px; display: block;"></span>
                </div>

                <div class="form-group">
                    <label style="font-family: 'Poppins'; font-weight: 700;">Type d'objectif</label>
                    <select name="type" id="type">
                        <option value="">Sélectionnez un objectif</option>
                        <option value="perte_poids" <?php echo ($regime && $regime->getType() == 'perte_poids') ? 'selected' : ''; ?>>Perte de poids</option>
                        <option value="prise_masse" <?php echo ($regime && $regime->getType() == 'prise_masse') ? 'selected' : ''; ?>>Prise de masse</option>
                        <option value="equilibre" <?php echo ($regime && $regime->getType() == 'equilibre') ? 'selected' : ''; ?>>Équilibre Santé</option>
                    </select>
                    <span id="error-type" class="error-text"
                        style="color: #e63946; font-size: 0.75rem; font-weight: 600; margin-top: 5px; display: block;"></span>
                </div>

                <div class="form-group">
                    <label style="font-family: 'Poppins'; font-weight: 700;">Calories par jour (Kcal)</label>
                    <input type="text" name="calorie_jour" id="calorie_jour" placeholder="Ex: 2000"
                        value="<?php echo $regime ? htmlspecialchars($regime->getCalorieJour()) : ''; ?>">
                    <span id="error-calorie_jour" class="error-text"
                        style="color: #e63946; font-size: 0.75rem; font-weight: 600; margin-top: 5px; display: block;"></span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Protéines (g)</label>
                        <input type="text" name="proteine" id="proteine"
                            value="<?php echo $regime ? htmlspecialchars($regime->getProteine()) : ''; ?>">
                        <span id="error-proteine" class="error-text"
                            style="color: #e63946; font-size: 0.75rem; font-weight: 600; margin-top: 5px; display: block;"></span>
                    </div>
                    <div class="form-group">
                        <label>Glucides (g)</label>
                        <input type="text" name="glucide" id="glucide"
                            value="<?php echo $regime ? htmlspecialchars($regime->getGlucide()) : ''; ?>">
                        <span id="error-glucide" class="error-text"
                            style="color: #e63946; font-size: 0.75rem; font-weight: 600; margin-top: 5px; display: block;"></span>
                    </div>
                    <div class="form-group">
                        <label>Lipides (g)</label>
                        <input type="text" name="lipides" id="lipides"
                            value="<?php echo $regime ? htmlspecialchars($regime->getLipides()) : ''; ?>">
                        <span id="error-lipides" class="error-text"
                            style="color: #e63946; font-size: 0.75rem; font-weight: 600; margin-top: 5px; display: block;"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label>Note d'intention</label>
                    <textarea name="description" id="description"
                        rows="3"><?php echo $regime ? htmlspecialchars($regime->getDescription()) : ''; ?></textarea>
                </div>

                <div class="form-group"
                    style="background: rgba(255,255,255,0.7); padding: 15px; border-radius: 12px; border: 1px solid rgba(0,200,83,0.3); margin-top: 20px;">
                    <label
                        style="font-family: 'Playfair Display'; font-weight: 900; font-size: 1.1rem; color: var(--primary-dark); margin-bottom: 15px; display: block; border-bottom: 2px solid var(--primary-color); padding-bottom: 5px;">
                        Calendrier sport
                    </label>
                    <p style="font-size: 0.8rem; font-family: 'Poppins'; color: #555; margin-bottom: 15px;">Sélectionnez
                        l'heure de votre séance de sport pour chaque jour de la semaine. Laissez vide si c'est un jour
                        de repos.</p>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 15px;">
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
                                style="background: #fff; padding: 10px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center;">
                                <label
                                    style="font-family: 'Poppins'; font-weight: 700; font-size: 0.85rem; color: #444; display: block; margin-bottom: 8px;"><?php echo $jour; ?></label>
                                <select name="heures_semaine[<?php echo $jour; ?>]"
                                    style="padding: 5px; border-radius: 6px; border: 1px solid #ccc; width: 100%; font-family: 'Poppins'; text-align: center; font-size: 0.85rem;">
                                    <option value="Rest-day" <?php echo ($current_val == 'Rest-day') ? 'selected' : ''; ?>>
                                        Rest-day</option>
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

                <div id="action-buttons-1" style="display: block; margin-top: 20px;">
                    <button type="button" id="btn-terminer" class="btn-premium"
                        style="border-radius: 12px; font-weight: 700; background: var(--primary); color: #fff;">TERMINER
                        LA SAISIE</button>
                </div>

                <div id="action-buttons-2" style="display: none; gap: 15px; margin-top: 20px; flex-wrap: wrap;">
                    <button type="button" id="btn-modifier" class="btn-premium"
                        style="width: auto; border-radius: 12px; font-weight: 700; background: #f59e0b; flex: 1; margin: 0;">MODIFIER</button>
                    <button type="button" id="btn-supprimer" class="btn-premium"
                        style="width: auto; border-radius: 12px; font-weight: 700; background: #b91c1c; flex: 0.8; margin: 0; padding: 10px 15px; font-size: 0.85rem;">SUPPRIMER</button>
                    <button type="submit" id="btn-enregistrer" class="btn-premium"
                        style="width: auto; border-radius: 12px; font-weight: 700; background: var(--primary); color: #fff; flex: 2; margin: 0;">
                        <?php echo ($source === 'back') ? 'ENREGISTRER' : 'ENREGISTRER ET SUIVANT'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/front_validation.js"></script>
    <script>
        const btnTerminer = document.getElementById('btn-terminer');
        const btnModifier = document.getElementById('btn-modifier');
        const btnSupprimer = document.getElementById('btn-supprimer');
        const action1 = document.getElementById('action-buttons-1');
        const action2 = document.getElementById('action-buttons-2');
        const regimeForm = document.getElementById('regimeForm');

        function setFieldsReadOnly(isReadOnly) {
            const elements = regimeForm.querySelectorAll('input, select, textarea');
            elements.forEach(el => {
                if (isReadOnly) {
                    el.style.pointerEvents = 'none';
                    el.style.backgroundColor = '#f8f9fa';
                    el.style.color = '#333';
                    if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') el.readOnly = true;
                } else {
                    el.style.pointerEvents = 'auto';
                    el.style.backgroundColor = '#fff';
                    el.style.color = 'inherit';
                    if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') el.readOnly = false;
                }
            });
        }

        btnTerminer.addEventListener('click', () => {
            setFieldsReadOnly(true);
            action1.style.display = 'none';
            action2.style.display = 'flex';
        });

        btnModifier.addEventListener('click', () => {
            setFieldsReadOnly(false);
            action1.style.display = 'block';
            action2.style.display = 'none';
        });

        btnSupprimer.addEventListener('click', () => {
            if (confirm('Êtes-vous sûr de vouloir supprimer votre saisie ?')) {
                <?php if ($id_regime): ?>
                    // Si un ID existe, on supprime en base et on revient sur un formulaire vide
                    window.location.href = 'delete_regime.php?id=<?php echo $id_regime; ?>&redirect=add_regime.php';
                <?php else: ?>
                    regimeForm.reset();
                    setFieldsReadOnly(false);
                    action1.style.display = 'block';
                    action2.style.display = 'none';
                <?php endif; ?>
            }
        });

        regimeForm.addEventListener('submit', () => {
            setFieldsReadOnly(false);
        });

        <?php if ($regime): ?>
            // Si on est en mode édition (retour étape 2), on affiche directement le résumé "verrouillé"
            document.addEventListener('DOMContentLoaded', () => {
                setFieldsReadOnly(true);
                action1.style.display = 'none';
                action2.style.display = 'flex';
            });
        <?php endif; ?>
    </script>
</body>

</html>