<?php

include("../../Controller/recetteC.php");
include("../../Controller/ingredientC.php");

$ingredientC = new ingredientC();

$ingredients = $ingredientC->getIngredientsByRecette($_GET['id']);

$recetteC = new recetteC();

// recup  recette
if (isset($_GET['id'])) {
    $recette = $recetteC->getrecetteD($_GET['id']);
}
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<title>Détails Recette</title>
<link rel="stylesheet" href="assets/recette_details.css">

</head>

<body>
    
<div class="header">
  <div class="icons">
     <span>🥑</span><span>🥕</span><span>🥦</span><span>🍎</span>
     <span>🍇</span><span>🥬</span><span>🍅</span><span>🍌</span>
     <span>🍓</span><span>🥒</span><span>🌽</span><span>🍍</span>
     <span>🥭</span><span>🍉</span><span>🥔</span>
  </div>

  <div class="header-content">
    <h1>NutriVerse</h1>
    <p>Découvrez des recettes saines, gourmandes et durables</p>
  </div>
</div>

<div class="details-container">

    <h1 class="details-title">
        <?= $recette['nom'] ?>
    </h1>

    <div class="details-grid">

     
        <div class="details-info">

            <div class="details-section">
                <h3>Description</h3>
                <p><?= $recette['description'] ?></p>
            </div>

            <div class="details-section">
                <h3>Étapes</h3>

                <ul class="details-steps">
                <?php
                $etapes = explode("\n", $recette['etapes']);

                foreach ($etapes as $etape) {
                    $etape = trim($etape);
                    if ($etape != "") {
                        echo "<li>$etape</li>";
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
                        echo "<li>" . $ing['nom'] . " (" . $ing['quantite'] . " " . $ing['unite'] . ")</li>";
                    }
                } else {
                    echo "<li>Aucun ingrédient</li>";
                }
                ?>
                </ul>
            </div>

            <div class="details-section">
                <h3>Temps de préparation</h3>
                <?= $recette['temps_preparation'] ?>
            </div>

          
            <div class="details-section">
                <h3>Catégorie</h3>
                <p><?= $recette['categorie'] ?></p>
            </div>

            <a href="recettes.php" class="btn-retour">
                Retour aux recettes
            </a>
            <button onclick="exportPDF()" class="btn-export">
    Exporter PDF
</button>
        </div>
      
        <img 
    src="../backOffice_recette/displayImage.php?id=<?= $recette['id_recette'] ?>" 
    class="details-image"
>

    </div>

</div>
<script>
function exportPDF() {
    window.print();
}
</script>
</body>
</html>