<?php
require_once __DIR__ . '/../../controller/RegimeController.php';
require_once __DIR__ . '/../../controller/PlanningController.php';

$id_regime = isset($_GET['id_regime']) ? $_GET['id_regime'] : null;
$rCtrl = new RegimeController();
$pCtrl = new PlanningController();

$regime     = $rCtrl->getRegime($id_regime);
$myPlanning = $pCtrl->getPlanningByRegime($id_regime);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Admin Update
    $rCtrl->updateRegime($id_regime, $_POST);
    if ($myPlanning) {
        $pCtrl->updatePlanning($myPlanning->getIdPlanning(), [
            'programme_sport' => $_POST['programme_sport'],
            'sommeil'         => $_POST['sommeil'],
            'titre_planning'  => $_POST['titre_planning'],
            'description'     => $_POST['description_planning'] ?? '',
            'statut'          => $_POST['statut']
        ]);
        // Mise à jour du statut via le contrôleur
        $pCtrl->updateStatut($myPlanning->getIdPlanning(), $_POST['statut']);
    }
    header("Location: admin_dashboard.php?msg=updated");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Modifier Planning</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body style="background: linear-gradient(135deg, #59b84d 0%, #a8dba0 45%, #ffffff 100%); min-height: 100vh; font-family: 'Poppins', sans-serif;">

    <div class="container fade-up" style="max-width: 1100px; margin: 0 auto; padding: 60px 20px; position: relative; z-index: 1;">
        
        <header style="margin-bottom: 40px; text-align: center;">
            <h1 style="color: white; font-family: 'Playfair Display'; font-size: 2.8rem;">Modifier le Planning</h1>
            <p style="color: rgba(255,255,255,0.7); font-family: 'Poppins'; font-weight: 500;">Édition administrative du Dossier #<?php echo $id_regime; ?></p>
        </header>

        <div class="glass-card" style="padding: 50px; background: rgba(255, 255, 255, 0.95);">
            <form id="editAdminForm" method="POST" novalidate>
                
                <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 50px;">
                    
                    <!-- SECTION 1 : NUTRITION -->
                    <div>
                        <h3 style="margin-bottom: 25px; border-bottom: 3px solid #59b84d; padding-bottom: 10px; color: #1b4332; font-family: 'Playfair Display';">PARTIE NUTRITION</h3>
                        
                        <div class="form-group">
                            <label style="font-family: 'Poppins'; font-weight: 700;">Nom du Planning</label>
                            <input type="text" name="nom" id="nom" value="<?php echo htmlspecialchars($regime->getNom()); ?>">
                            <span id="error-nom" class="error-text" style="color: #e63946; font-size: 0.75rem; font-weight: 600; margin-top: 5px; display: block;"></span>
                        </div>

                        <div class="form-group">
                            <label style="font-family: 'Poppins'; font-weight: 700;">Apport Calorique</label>
                            <input type="text" name="calorie_jour" id="calorie_jour" value="<?php echo $regime->getCalorieJour(); ?>">
                            <span id="error-calorie_jour" class="error-text" style="color: #e63946; font-size: 0.75rem; font-weight: 600; margin-top: 5px; display: block;"></span>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                            <div class="form-group"><label>Prot (g)</label><input type="text" name="proteine" id="proteine" value="<?php echo $regime->getProteine(); ?>"><span id="error-proteine" class="error-text" style="color: #e63946; font-size: 0.7rem;"></span></div>
                            <div class="form-group"><label>Gluc (g)</label><input type="text" name="glucide" id="glucide" value="<?php echo $regime->getGlucide(); ?>"><span id="error-glucide" class="error-text" style="color: #e63946; font-size: 0.7rem;"></span></div>
                            <div class="form-group"><label>Lip (g)</label><input type="text" name="lipides" id="lipides" value="<?php echo $regime->getLipides(); ?>"><span id="error-lipides" class="error-text" style="color: #e63946; font-size: 0.7rem;"></span></div>
                        </div>

                        <div class="form-group" style="margin-top: 20px;">
                            <label style="font-family: 'Poppins'; font-weight: 700;">Statut de Validation</label>
                            <select name="statut" style="background: #fff; border: 1px solid #59b84d;">
                                <option value="en_attente" <?php if($myPlanning && $myPlanning->getStatut() == 'en_attente') echo 'selected'; ?>>EN ATTENTE</option>
                                <option value="accepte" <?php if($myPlanning && $myPlanning->getStatut() == 'accepte') echo 'selected'; ?>>ACCEPTÉ</option>
                                <option value="refuse" <?php if($myPlanning && $myPlanning->getStatut() == 'refuse') echo 'selected'; ?>>REFUSÉ</option>
                            </select>
                        </div>
                    </div>

                    <!-- SECTION 2 : PERFORMANCE -->
                    <div>
                        <h3 style="margin-bottom: 25px; border-bottom: 3px solid #59b84d; padding-bottom: 10px; color: #1b4332; font-family: 'Playfair Display';">PARTIE PERFORMANCE</h3>
                        
                        <div class="form-group">
                            <label style="font-family: 'Poppins'; font-weight: 700;">Titre du Planning Sportif</label>
                            <input type="text" name="titre_planning" id="titre_planning" value="<?php echo $myPlanning ? htmlspecialchars($myPlanning->getTitrePlanning()) : ''; ?>">
                            <span id="error-titre_planning" class="error-text" style="color: #e63946; font-size: 0.75rem; font-weight: 600; margin-top: 5px; display: block;"></span>
                        </div>

                        <div class="form-group">
                            <label style="font-family: 'Poppins'; font-weight: 700;">Objectif Sommeil</label>
                            <select name="sommeil" id="sommeil">
                                <option value="6-7h" <?php if($myPlanning && $myPlanning->getSommeil() == '6-7h') echo 'selected'; ?>>6-7 heures</option>
                                <option value="7-8h" <?php if($myPlanning && $myPlanning->getSommeil() == '7-8h') echo 'selected'; ?>>7-8 heures</option>
                                <option value="8-9h" <?php if($myPlanning && $myPlanning->getSommeil() == '8-9h') echo 'selected'; ?>>8-9 heures</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label style="font-family: 'Poppins'; font-weight: 700;">Détails de l'Activité (Smart Assistant)</label>
                            <textarea name="programme_sport" id="programme_sport" rows="11"><?php echo $myPlanning ? htmlspecialchars($myPlanning->getProgrammeSport()) : ''; ?></textarea>
                            <span id="error-programme_sport" class="error-text" style="color: #e63946; font-size: 0.75rem; font-weight: 600; margin-top: 5px; display: block;"></span>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 40px; display: flex; gap: 20px; justify-content: flex-end;">
                    <a href="admin_dashboard.php" class="btn-premium" style="width: auto; background: #fff; color: #333; border: 1px solid #ddd; text-decoration: none;">ANNULER</a>
                    <button type="submit" class="btn-premium" style="width: auto; padding: 15px 50px; font-weight: 700; border-radius: 12px; background: #59b84d;">ENREGISTRER LES MODIFICATIONS</button>
                </div>

            </form>
        </div>
    </div>

    <script src="assets/back_validation.js"></script>
</body>
</html>
