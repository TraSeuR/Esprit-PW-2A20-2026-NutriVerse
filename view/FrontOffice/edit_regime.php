<?php
require_once __DIR__ . '/../../controller/RegimeController.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: list_programmes.php");
    exit();
}

$controller = new RegimeController();
$regime = $controller->getRegime($id);

if (!$regime) {
    header("Location: list_programmes.php");
    exit();
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($controller->updateRegime($id, $_POST)) {
        header("Location: list_programmes.php?success=update");
        exit();
    } else {
        $message = "Une erreur est survenue lors de la mise à jour.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriVerse - Modifier le Régime</title>
    <link rel="stylesheet" href="../../../view/front/assets/front.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .form-section { padding: 60px 0; background: transparent; }
        .form-container { 
            max-width: 800px; 
            margin: 0 auto; 
            background: var(--white); 
            padding: 40px; 
            border-radius: var(--radius); 
            box-shadow: var(--shadow);
        }
        .form-group { margin-bottom: 25px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; padding: 12px 18px; border-radius: 12px; border: 1.5px solid #e5ebe4; font-family: inherit;
        }
        .error-msg { color: #e74c3c; font-size: 0.85rem; margin-top: 5px; display: none; }
    </style>
</head>
<body style="background: linear-gradient(135deg, #59b84d 0%, #a8dba0 45%, #ffffff 100%); min-height: 100vh; font-family: 'Poppins', sans-serif;">
    <header class="header">
        <div class="container nav">
            <div class="logo">
                <img src="../../../view/front/images/logo.png" alt="Logo NutriVerse" class="logo-img">
            </div>
            <nav class="navbar">
                <a href="../../../view/front/front.php">Accueil</a>
                <a href="list_programmes.php">Programmes</a>
            </nav>
        </div>
    </header>

    <section class="form-section">
        <div class="container">
            <div class="form-container">
                <div class="section-header">
                    <span class="section-tag">Modification</span>
                    <h2>Modifier votre régime</h2>
                    <p>Mettez à jour vos informations nutritionnelles.</p>
                </div>

                <?php if ($message): ?>
                    <p class="error-msg" style="display:block;"><?php echo $message; ?></p>
                <?php endif; ?>

                <form id="editForm" method="POST" novalidate>
                    <div class="form-group">
                        <label for="nom">Nom du Régime</label>
                        <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($regime->getNom()); ?>">
                        <span class="error-msg" id="error-nom">Veuillez entrer un nom valide.</span>
                    </div>

                    <div class="form-group">
                        <label for="type">Type de Régime</label>
                        <select id="type" name="type">
                            <option value="perte_poids" <?php if($regime->getType() == 'perte_poids') echo 'selected'; ?>>Perte de poids</option>
                            <option value="prise_masse" <?php if($regime->getType() == 'prise_masse') echo 'selected'; ?>>Prise de masse</option>
                            <option value="equilibre" <?php if($regime->getType() == 'equilibre') echo 'selected'; ?>>Équilibre & Santé</option>
                            <option value="performance" <?php if($regime->getType() == 'performance') echo 'selected'; ?>>Performance Sportive</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="calorie_jour">Calories / jour</label>
                        <input type="text" id="calorie_jour" name="calorie_jour" value="<?php echo htmlspecialchars($regime->getCalorieJour()); ?>">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Protéines</label>
                            <input type="text" name="proteine" id="proteine" value="<?php echo htmlspecialchars($regime->getProteine()); ?>">
                        </div>
                        <div class="form-group">
                            <label>Glucides</label>
                            <input type="text" name="glucide" id="glucide" value="<?php echo htmlspecialchars($regime->getGlucide()); ?>">
                        </div>
                        <div class="form-group">
                            <label>Lipides</label>
                            <input type="text" name="lipides" id="lipides" value="<?php echo htmlspecialchars($regime->getLipides()); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="3"><?php echo htmlspecialchars($regime->getDescription()); ?></textarea>
                    </div>

                    <button type="submit" class="btn-primary large" style="width:100%">Enregistrer les modifications</button>
                    <a href="list_programmes.php" style="display: block; text-align: center; margin-top: 15px; color: var(--muted);">Annuler</a>
                </form>
            </div>
        </div>
    </section>

    <script src="assets/front_validation.js"></script>
</body>
</html>
