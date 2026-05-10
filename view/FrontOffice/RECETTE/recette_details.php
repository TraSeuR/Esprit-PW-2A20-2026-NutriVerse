<?php
include("../../../controller/recetteC.php");
include("../../../controller/ingredientC.php");

$ingredientC = new ingredientC();
$ingredients = $ingredientC->getIngredientsByRecette($_GET['id'] ?? 0);

$recetteC = new recetteC();
$recette = [];
if (isset($_GET['id'])) {
    $recette = $recetteC->getrecetteD($_GET['id']);
}

if (!$recette) {
    die("Recette non trouvée.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($recette['nom']) ?> - NutriVerse</title>
    <link rel="stylesheet" href="../assets/recette_details.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    
<?php 
$rel = "../";
include '../header.php'; 
?>

<div class="details-container">
    <h1 class="details-title">
        <?= htmlspecialchars($recette['nom']) ?>
    </h1>

    <div class="details-grid">
        <div class="details-info">
            <div class="details-section">
                <h3>Description</h3>
                <p><?= nl2br(htmlspecialchars($recette['description'])) ?></p>
            </div>

            <div class="details-section">
                <h3>Étapes de préparation</h3>
                <ul class="details-steps">
                <?php
                $etapes = explode("\n", $recette['etapes']);
                foreach ($etapes as $etape) {
                    $etape = trim($etape);
                    if ($etape != "") {
                        echo "<li>" . htmlspecialchars($etape) . "</li>";
                    }
                }
                ?>
                </ul>
            </div>

            <div class="details-section">
                <h3>Ingrédients</h3>
                <ul class="details-steps">
                <?php
                if (!empty($ingredients)) {
                    foreach ($ingredients as $ing) {
                        echo "<li>" . htmlspecialchars($ing['nom']) . " (" . htmlspecialchars($ing['quantite']) . " " . htmlspecialchars($ing['unite']) . ")</li>";
                    }
                } else {
                    echo "<li>Aucun ingrédient répertorié</li>";
                }
                ?>
                </ul>
            </div>

            <div class="details-section">
                <h3>Temps de préparation</h3>
                <p>⏱️ <?= htmlspecialchars($recette['temps_preparation']) ?></p>
            </div>

            <div class="details-section">
                <h3>Catégorie</h3>
                <span class="tag"><?= htmlspecialchars($recette['categorie']) ?></span>
            </div>

            <div style="display: flex; gap: 15px; margin-top: 30px;">
                <a href="recettes.php" class="btn-retour" style="text-decoration: none;">
                    ← Retour aux recettes
                </a>
                <button onclick="exportPDF()" class="btn-export">
                    📄 Exporter PDF
                </button>
            </div>
        </div>
      
        <div class="details-visual">
            <img src="../../BackOffice/RECETTE/displayImage.php?id=<?= $recette['id_recette'] ?>" alt="<?= htmlspecialchars($recette['nom']) ?>" class="details-image">
        </div>
    </div>
</div>

<script>
function exportPDF() {
    window.print();
}
</script>
</body>
</html>
