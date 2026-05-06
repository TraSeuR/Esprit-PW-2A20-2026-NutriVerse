<?php


$ingredientC = new ingredientC();
$recetteC = new recetteC();

$recettes = $recetteC->listeRecette();
?>

<div class="table-card">

<h3>Liste des recettes</h3>

<div class="table-wrapper">

<table class="recipe-table">

<tr>
<th>ID</th>
<th>Nom</th>
<th>Description</th>
<th>Étapes</th>
<th>Temps</th>
<th>Catégorie</th>
<th>Image</th>
<th>Ingrédients</th>
<th>Actions</th>
</tr>

<?php $i = 1; ?>
<?php foreach ($recettes as $r) { ?>

<tr>

<td><?= $i++ ?></td>

<td><?= $r['nom'] ?></td>

<td>
<div class="scroll-cell">
<?= $r['description'] ?>
</div>
</td>

<td>
<div class="scroll-cell">
<?= $r['etapes'] ?>
</div>
</td>

<td><?= $r['temps_preparation'] ?></td>

<td><?= $r['categorie'] ?></td>

<td>
<img src="displayImage.php?id=<?= $r['id_recette'] ?>" width="60">
</td>

<td>
<div class="scroll-cell">
<?php
$ings = $ingredientC->getIngredientsByRecette($r['id_recette']);

if (!empty($ings)) {
    foreach ($ings as $ing) {
        echo $ing['nom'] . " (" . $ing['quantite'] . " " . $ing['unite'] . ")<br>";
    }
} else {
    echo "Aucun ingrédient";
}
?>
</div>
</td>

<td>

<a class="btn-action"
href="admin.php?edit=<?= $r['id_recette'] ?>">
Modifier
</a>

<a class="btn-action"
href="#"
onclick="confirmDelete(<?= $r['id_recette'] ?>); return false;">
Supprimer
</a>

</td>

</tr>

<?php } ?>

</table>

</div>
</div>