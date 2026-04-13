<?php


$recetteC = new recetteC();

$recettes = $recetteC->listeRecette();

?>


<div class="table-card">

<h3>Liste des recettes</h3>

<div class="table-wrapper">

<table class="recipe-table">
<!--entete du tab-->
<tr>
<th>ID</th>
<th>Nom</th>
<th>Description</th>
<th>Étapes</th>
<th>Temps</th>
<th>Catégorie</th>
<th>Image</th>
<th>Actions</th>
</tr>

<?php foreach ($recettes as $r) { ?>

<tr>

<td><?= $r['id_recette'] ?></td> <!--affiche lid-->

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
<img src="images/<?= $r['images'] ?>" width="60">
</td>

<td>

<a class="btn-action"
href="admin.php?edit=<?= $r['id_recette'] ?>">
Modifier
</a>

<a class="btn-action" 
href="delete.php?id=<?= $r['id_recette'] ?>">
Supprimer
</a>
<!--yekhou style les btn action (css)-->
</td>

</tr>

<?php } ?>

</table>

</div>

</div>