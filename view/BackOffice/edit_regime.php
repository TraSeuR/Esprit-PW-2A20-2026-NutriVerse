<?php
require_once __DIR__ . '/../../controller/RegimeController.php';

$controller = new RegimeController();
$regime = null;

if (isset($_GET['id'])) {
    $regime = $controller->getRegime($_GET['id']);
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $regime) {
    if ($controller->updateRegime($regime->getIdRegime(), $_POST)) {
        header("Location: admin_dashboard.php?success=1");
        exit();
    } else {
        $message = "Une erreur est survenue lors de la modification.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Régime - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body style="background: linear-gradient(135deg, #59b84d 0%, #a8dba0 45%, #ffffff 100%); min-height: 100vh; font-family: 'Poppins', sans-serif;">

    <header class="header">
        <h1>Admin : Modification</h1>
    </header>

    <div class="nav-bar">
        <a href="admin_dashboard.php">← Retour Dashboard</a>
    </div>

    <div class="container fade-in">
        <div class="form-box">
            <h2 class="form-title">Modifier Régime #<?php echo $regime ? $regime->getIdRegime() : ''; ?></h2>
            
            <?php if (!$regime): ?>
                <p>Régime non trouvé.</p>
            <?php else: ?>
                <form id="regimeForm" method="POST" novalidate>
                    <div class="form-group">
                        <label for="nom">Nom du Régime</label>
                        <input type="text" name="nom" id="nom" value="<?php echo htmlspecialchars($regime->getNom()); ?>">
                        <span id="error-nom" class="error-msg"></span>
                    </div>

                    <div class="form-group">
                        <label for="type">Type d'objectif</label>
                        <select name="type" id="type">
                            <option value="perte_poids" <?php if($regime->getType() == 'perte_poids') echo 'selected'; ?>>Perte de poids</option>
                            <option value="prise_masse" <?php if($regime->getType() == 'prise_masse') echo 'selected'; ?>>Prise de masse</option>
                            <option value="equilibre" <?php if($regime->getType() == 'equilibre') echo 'selected'; ?>>Équilibre</option>
                        </select>
                        <span id="error-type" class="error-msg"></span>
                    </div>

                    <div class="form-group">
                        <label for="calorie_jour">Calories journalières</label>
                        <input type="text" name="calorie_jour" id="calorie_jour" value="<?php echo $regime->getCalorieJour(); ?>">
                        <span id="error-calorie" class="error-msg"></span>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label>Protéines (g)</label>
                            <input type="text" name="proteine" id="proteine" value="<?php echo $regime->getProteine(); ?>">
                            <span id="error-proteine" class="error-msg"></span>
                        </div>
                        <div class="form-group">
                            <label>Glucides (g)</label>
                            <input type="text" name="glucide" id="glucide" value="<?php echo $regime->getGlucide(); ?>">
                            <span id="error-glucide" class="error-msg"></span>
                        </div>
                        <div class="form-group">
                            <label>Lipides (g)</label>
                            <input type="text" name="lipides" id="lipides" value="<?php echo $regime->getLipides(); ?>">
                            <span id="error-lipides" class="error-msg"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" rows="4"><?php echo htmlspecialchars($regime->getDescription()); ?></textarea>
                        <span id="error-description" class="error-msg"></span>
                    </div>

                    <button type="submit" class="btn-primary">Enregistrer les modifications</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script src="assets/back_validation.js"></script>
</body>
</html>
