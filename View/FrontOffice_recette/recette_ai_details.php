<?php

$nom = $_GET['nom'] ?? '';
$categorie = $_GET['categorie'] ?? '';
$description = $_GET['description'] ?? '';
$temps = $_GET['temps'] ?? '';
$image = $_GET['image'] ?? '';

$ingredients = explode(",", $_GET['ingredients'] ?? '');
$etapes = explode("|", $_GET['etapes'] ?? '');
$conseils = explode("|", $_GET['conseils'] ?? '');

?>

<link rel="stylesheet" href="/assets/recette_details.css">

<div class="details-container">

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
<?php foreach ($etapes as $e) { ?>
<li><?= $e ?></li>
<?php } ?>
</ol>
</div>

<div class="details-section">
<h3>Ingrédients</h3>
<ul class="details-steps">
<?php foreach ($ingredients as $i) { ?>
<li><?= $i ?></li>
<?php } ?>
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
<?php foreach ($conseils as $c) { ?>
<li><?= $c ?></li>
<?php } ?>
</ul>
</div>

<div class="details-actions">

<a href="recettes.php" class="btn-retour">
Retour aux recettes
</a>

<button type="button" class="btn-save" onclick="saveRecipe()">
Enregistrer
</button>

</div>

</div>

<img src="<?= $image ?>" class="details-image">

</div>

</div>

<script>
function saveRecipe() {
    window.print();
}
</script>