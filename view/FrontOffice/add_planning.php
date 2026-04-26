<?php
require_once __DIR__ . '/../../controller/PlanningController.php';
require_once __DIR__ . '/../../controller/RegimeController.php';

$id_regime = $_GET['id_regime'] ?? null;

// Chargement du régime pour le récapitulatif
$rCtrl = new RegimeController();
$regime = $rCtrl->getRegime($id_regime);

// Le contrôleur gère la logique de création du planning (POST)
$pCtrl = new PlanningController();
$pCtrl->handleRequest($id_regime);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriVerse - Votre Planning</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body style="background: linear-gradient(135deg, #59b84d 0%, #a8dba0 45%, #ffffff 100%); min-height: 100vh;">

    <header class="premium-header fade-up">
        <h1 style="font-family: 'Playfair Display';">Étape 2</h1>
        <p style="font-family: 'Poppins';">Étape 2</p>
    </header>

    <div class="container fade-up" style="animation-delay: 0.2s; padding-bottom: 80px;">
        
        <!-- RÉCAPITULATIF RÉGIME (GLASS STYLE) -->
        <?php if ($regime): ?>
        <div class="glass-card" style="margin-bottom: 30px; padding: 25px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 15px;">
                <div>
                    <h3 style="font-family: 'Playfair Display'; font-weight: 900; color: var(--primary-dark); margin: 0; font-size: 1.3rem;">Planning : <?php echo htmlspecialchars($regime->getNom()); ?></h3>
                    <p style="font-size: 0.85rem; color: #555; margin: 6px 0 0; font-family: 'Poppins'; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">OBJECTIF : <?php echo str_replace('_', ' ', $regime->getType()); ?></p>
                </div>
                <div style="font-family: 'Playfair Display'; font-weight: 900; color: var(--primary-dark); font-size: 1.4rem;">
                    <?php echo $regime->getCalorieJour(); ?> <span style="font-size: 0.9rem; font-family: 'Poppins'; font-weight: 700;">Kcal/j</span>
                </div>
            </div>

            <?php 
            if ($regime && $regime->getHeuresSemaine()): 
                $heures = json_decode($regime->getHeuresSemaine(), true);
                if (is_array($heures)):
            ?>
            <div style="margin-top: 18px; border-top: 1px solid rgba(0,0,0,0.07); padding-top: 15px;">
                <div style="font-size: 0.72rem; font-family: 'Poppins'; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px;">Calendrier sport de la semaine</div>
                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                    <?php foreach ($heures as $jour => $heure): ?>
                        <?php if (!empty($heure)): ?>
                        <div style="background: rgba(0,200,83,0.1); border: 1px solid rgba(0,200,83,0.3); padding: 5px 12px; border-radius: 15px; display: flex; align-items: center; gap: 6px;">
                            <span style="font-family: 'Poppins'; font-weight: 700; font-size: 0.75rem; color: #444;"><?php echo mb_substr($jour, 0, 3); ?>.</span>
                            <span style="font-family: 'Playfair Display'; font-weight: 900; font-size: 0.9rem; color: var(--primary-dark);"><?php echo $heure; ?></span>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php 
                endif;
            endif; 
            ?>
        </div>
        <?php endif; ?>

        <div class="glass-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h2 class="form-title" style="font-family: 'Playfair Display'; margin-bottom: 0;">Planifier l'activité</h2>
                <a href="add_regime.php?id_regime=<?php echo htmlspecialchars($id_regime); ?>" class="btn-premium" style="width: auto; padding: 8px 20px; font-size: 0.8rem; background: var(--primary); color: #fff; text-decoration: none; border-radius: 12px; font-weight: 600;">REVENIR À L'ÉTAPE 1</a>
            </div>

            <form id="planningForm" method="POST" novalidate>
                <input type="hidden" name="id_regime" value="<?php echo htmlspecialchars($id_regime); ?>">

                <div class="form-group">
                    <label>Titre de l'Activité</label>
                    <input type="text" name="titre_planning" id="titre_planning" placeholder="Ex: Routine Vitalité Pro">
                    <span id="error-titre" class="error-text" style="color: #e63946; font-size: 0.75rem; font-weight: 600; margin-top: 5px; display: block;"></span>
                </div>

                <div class="form-group">
                    <label>Détails du Programme Sportif</label>
                    
                    <div style="margin-bottom: 15px; display: flex; gap: 10px; flex-wrap: wrap;">
                        <button type="button" class="btn-premium" style="width: auto; padding: 8px 15px; font-size: 0.7rem; background: #eee; color: #333;" onclick="insertDay('Lundi')">LUNDI</button>
                        <button type="button" class="btn-premium" style="width: auto; padding: 8px 15px; font-size: 0.7rem; background: #eee; color: #333;" onclick="insertDay('Mardi')">MARDI</button>
                        <button type="button" class="btn-premium" style="width: auto; padding: 8px 15px; font-size: 0.7rem; background: #eee; color: #333;" onclick="insertDay('Mercredi')">MERCREDI</button>
                        <button type="button" class="btn-premium" style="width: auto; padding: 8px 15px; font-size: 0.7rem; background: #eee; color: #333;" onclick="insertDay('Jeudi')">JEUDI</button>
                        <button type="button" class="btn-premium" style="width: auto; padding: 8px 15px; font-size: 0.7rem; background: #eee; color: #333;" onclick="insertDay('Vendredi')">VENDREDI</button>
                        <button type="button" class="btn-premium" style="width: auto; padding: 8px 15px; font-size: 0.7rem; background: #eee; color: #333;" onclick="insertDay('Samedi')">SAMEDI</button>
                        <button type="button" class="btn-premium" style="width: auto; padding: 8px 15px; font-size: 0.7rem; background: #eee; color: #333;" onclick="insertDay('Dimanche')">DIMANCHE</button>
                    </div>

                    <textarea name="programme_sport" id="programme_sport" rows="8" placeholder="Tapez votre sport. L'assistant vous aidera pour les jours suivants..."></textarea>
                    <span id="error-sport" class="error-text" style="color: #e63946; font-size: 0.75rem; font-weight: 600; margin-top: 5px; display: block;"></span>
                </div>

                <div class="form-group">
                    <label>Objectif Sommeil</label>
                    <select name="sommeil" id="sommeil">
                        <option value="">Sélectionnez un cycle...</option>
                        <option value="6-7h">6-7 heures</option>
                        <option value="7-8h">7-8 heures</option>
                        <option value="8-9h">8-9 heures</option>
                    </select>
                    <span id="error-som" class="error-text" style="color: #e63946; font-size: 0.75rem; font-weight: 600; margin-top: 5px; display: block;"></span>
                </div>

                <div style="margin-top: 20px;">
                    <button type="submit" class="btn-premium" style="border-radius: 12px; font-weight: 700; width: 100%; background: var(--primary); color: #fff;">ENREGISTRER LE PLANNING</button>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/front_validation.js"></script>
</body>
</html>
