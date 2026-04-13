<?php

include("../../Controller/recetteC.php");

$recetteC = new recetteC();

// récupérer la recette
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
                <ol class="details-steps">
                    <?= $recette['etapes'] ?>
                </ol>
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

        </div>

      
        <img 
            src="../backOffice_recette/images/<?= $recette['images'] ?>" 
            class="details-image"
        >

    </div>

</div>

</body>
</html>