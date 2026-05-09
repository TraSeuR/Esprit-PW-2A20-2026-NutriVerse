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
    <link rel="stylesheet" href="assets/front.css">
    <link rel="stylesheet" href="../assets/style.css?v=1.3">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/technical_front.css">
</head>
<body style="background: linear-gradient(135deg, #59b84d 0%, #a8dba0 45%, #ffffff 100%); min-height: 100vh; font-family: 'Poppins', sans-serif;">
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

    <?php include 'coach_widget.php'; ?>
    <script src="assets/front_validation.js"></script>
</body>
</html>
