<?php
//nekhdou les donnes ml url 
//explode trodhom fi tabl 

$nom = $_GET['nom'];
$categorie = $_GET['categorie'];
$description = $_GET['description'];
$temps = $_GET['temps'];
$ingredients = explode(",", $_GET['ingredients']);
$etapes = explode("|", $_GET['etapes']);
$conseils = explode("|", $_GET['conseils']);
$image = $_GET['image'];
?>

<div class="details-container">
    <link rel="stylesheet" href="assets/recette_details.css">

    <h1 class="details-title"><?= $nom ?></h1>

    <div class="details-grid">

        <div class="details-info">

            <div class="details-section">
                <h3>Description</h3>
                <p><?= $description ?></p>
            </div>

            <div class="details-section">
                <h3>Étapes</h3>
                <ol class="details-steps">
                    <?php foreach ($etapes as $e) echo "<li>$e</li>"; ?>
                </ol>
            </div>

            <div class="details-section">
                <h3>Ingrédients</h3>
                <ul class="details-steps">
                    <?php foreach ($ingredients as $i) echo "<li>$i</li>"; ?>
                </ul>
            </div>

            <div class="details-section">
                <h3>Temps de préparation</h3>
                <p><?= $temps ?></p>
            </div>

            <div class="details-section">
                <h3>Catégorie</h3>
                <p><?= $categorie ?></p>
            </div>

            <div class="details-section">
                <h3>Conseils</h3>
                <ul>
                    <?php foreach ($conseils as $c) echo "<li>$c</li>"; ?>
                </ul>
            </div>

            <a href="recettes.php" class="btn-retour">Retour</a>

        </div>

        <img src="<?= $image ?>" class="details-image">

    </div>

</div>