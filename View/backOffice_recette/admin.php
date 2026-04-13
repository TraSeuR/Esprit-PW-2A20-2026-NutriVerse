<?php
include("../../Controller/recetteC.php");

$recetteC = new recetteC();
//par defaut lmode add
$mode = "ajouter";
$recette_edit = null;
//si lurl fih id recette a modf
if (isset($_GET['edit'])) {

    $id_edit = $_GET['edit'];

    $recette_edit = $recetteC->getRecette($id_edit);
//chngmt lpage twali feha form modf
    $mode = "modifier";
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Admin Recettes</title>

<link rel="stylesheet" href="assets/admin.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

</head>

<body>

<!-- eli aal jnb mta site -->
<aside class="sidebar">
    <div class="sidebar-top">
        <div class="brand">
            <img src="images/logo.png" class="brand-logo">
            <div>
                <h2>NutriVerse</h2>
                <p>Back Office</p>
            </div>
        </div>
    </div>

    <nav class="sidebar-menu">
        <a href="#" class="menu-item">Dashboard</a>
        <a href="#" class="menu-item active">Recettes</a>
        <a href="#" class="menu-item">Utilisateurs</a>
        <a href="#" class="menu-item">Produits</a>
        <a href="#" class="menu-item">Commandes</a>
        <a href="#" class="menu-item">Suivi Santé</a>
        <a href="#" class="menu-item">Programmes</a>
        <a href="#" class="menu-item">Paramètres</a>
    </nav>

    <div class="sidebar-footer">
        <p>© 2026 NutriVerse</p>
    </div>
</aside>


<div class="main">

<!-- formlr -->
<div class="form-card">

<h2 class="title">
<?php
//titre yetbdl hasb el mode ken ajout w el modf
if ($mode == "modifier")
    echo "Modifier Recette";
else
    echo "Partie Recette";
?>
</h2>

<!-- formlr 2 faces--> <!--ken modf yaabth lupdate snn add-->
<form id="recetteForm" method="POST"
action="<?php echo ($mode == 'modifier') ? 'update.php' : 'add.php'; ?>"
enctype="multipart/form-data">

<?php if ($mode == "modifier") { ?>
<!--maybench id fl formulr-->
<input type="hidden" name="id" value="<?= $recette_edit['id_recette'] ?>">
<?php } ?>

<div class="form-group">
<label>Nom</label>
<input type="text" id="nom" name="nom"
value="<?= $recette_edit['nom'] ?? '' ?>">
<span id="nomMsg" class="msg "></span>

</div>

<div class="form-group">
<label>Description</label>
<textarea id="description" name="description"><?= $recette_edit['description'] ?? '' ?></textarea>
<span id="descMsg" class="msg"></span> <!--al contrl-->
</div>

<div class="form-group">
<label>Étapes</label>
<textarea id="etapes" name="etapes"><?= $recette_edit['etapes'] ?? '' ?></textarea>
<span id="etapesMsg" class="msg"></span>
</div>

<div class="form-group">
<label>Temps de préparation</label>
<input type="text" id="temps" name="temps"
value="<?= $recette_edit['temps_preparation'] ?? '' ?>">
<span id="tempsMsg" class="msg"></span>
</div>

<div class="form-group">
<label>Catégorie</label>
<input type="text" id="categorie" name="categorie"
value="<?= $recette_edit['categorie'] ?? '' ?>">
<span id="catMsg" class="msg"></span>
</div>

<div class="form-group">
<label>Image</label>
<input type="file" id="image" name="image">
<span id="imgMsg" class="msg"></span>
<?php if ($mode == "modifier") { ?>
    <input type="hidden" name="ancienne_image" value="<?= $recette_edit['images'] ?? '' ?>">
<?php } ?>
</div>

<hr>

<h2 class="title">Partie Ingrédients</h2>

<div class="ingredient-row">
<input type="text" name="ingredient_nom[]" placeholder="Nom">
<input type="text" name="ingredient_qte[]" placeholder="Quantité">
<input type="text" name="ingredient_unite[]" placeholder="Unité">
</div>

<button type="button" class="btn-add">
+ Ajouter Ingrédient
</button>

<div class="form-buttons">

<?php if ($mode == "modifier") { ?>

<button type="submit" class="btn-submit">
Mettre à jour
</button>

<a href="admin.php" class="btn-cancel">
Annuler
</a>

<?php } else { ?>

<button type="submit" class="btn-submit">
Ajouter Recette
</button>

<button type="reset" class="btn-cancel">
Annuler
</button>

<?php } ?>

</div>

</form>

</div>


<div class="right-panel">

<!-- tab -->
<?php include("liste.php"); ?>

<!-- el apercu taht el tab -->
<div class="image-card">
<h3>Image recette sélectionnée</h3>

<?php if ($mode == "modifier" && !empty($recette_edit['images'])) { ?>

<img src="images/<?= $recette_edit['images'] ?>" width="280">

<?php } else { ?>
<!--el logo eli yben awel mnhelou ki yabda vide -->
<img src="https://via.placeholder.com/300" width="280">

<?php } ?>

</div>

</div>

</div>

<script src="recette.js"></script>
</body>
</html>