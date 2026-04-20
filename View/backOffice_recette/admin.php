<?php
include("../../Controller/recetteC.php");
include("../../Controller/ingredientC.php");


$recetteC = new recetteC();
//par defaut lmode add
$mode = "ajouter";
$recette_edit = null;
$ingredients_edit = []; 

//si lurl fih id recette a modf
if (isset($_GET['edit'])) {

    $id_edit = $_GET['edit'];

    $recette_edit = $recetteC->getRecette($id_edit);

    
    $ingredientC = new ingredientC();
    $ingredients_edit = $ingredientC->getIngredientsByRecette($id_edit);

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
<div class="form-container">

    <button type="button" class="btn-reset" onclick="goHome()">←</button>

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

<form id="recetteForm" method="POST"
action="<?php echo ($mode == 'modifier') ? 'update.php' : 'add.php'; ?>"
enctype="multipart/form-data">

<?php if ($mode == "modifier") { ?>
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
<span id="descMsg" class="msg"></span>
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

<select id="categorie" name="categorie">
    <option value="">-- Choisir catégorie --</option>

    <option value="Healthy"
        <?= (isset($recette_edit['categorie']) && $recette_edit['categorie'] == 'Healthy') ? 'selected' : '' ?>>
        Healthy
    </option>

    <option value="Vegan"
        <?= (isset($recette_edit['categorie']) && $recette_edit['categorie'] == 'Vegan') ? 'selected' : '' ?>>
        Vegan
    </option>

    <option value="Cuisine Durable"
        <?= (isset($recette_edit['categorie']) && $recette_edit['categorie'] == 'Cuisine Durable') ? 'selected' : '' ?>>
        Cuisine Durable
    </option>

</select>
<span id="catMsg" class="msg"></span>
</div>

<div class="form-group">
    <label>Image</label>
    <input type="file" id="image" name="image">
    <span id="imgMsg" class="msg"></span>

    <?php if ($mode == "modifier") { ?>
        <br>
        <img src="displayImage.php?id=<?= $recette_edit['id_recette'] ?>" width="120">
    <?php } ?>
</div>

<hr>

<h2 class="title">Partie Ingrédients</h2>
<div id="ingredients-container">

<?php if ($mode == "modifier" && !empty($ingredients_edit)) { ?>

    <?php foreach ($ingredients_edit as $ing) { ?>
        <div class="ingredient-row">

            <div class="ing-field">
                <input type="text" name="ingredient_nom[]" value="<?= $ing['nom'] ?>">
                <span class="msg"></span>
            </div>

            <div class="ing-field">
                <input type="text" name="ingredient_qte[]" value="<?= $ing['quantite'] ?>">
                <span class="msg"></span>
            </div>

            <div class="ing-field">
                <input type="text" name="ingredient_unite[]" value="<?= $ing['unite'] ?>">
                <span class="msg"></span>
            </div>

            <button type="button" class="btn-remove">✖</button>

        </div>
    <?php } ?>

<?php } else { ?>

    <div class="ingredient-row">

        <div class="ing-field">
            <input type="text" name="ingredient_nom[]" placeholder="Nom">
            <span class="msg"></span>
        </div>

        <div class="ing-field">
            <input type="text" name="ingredient_qte[]" placeholder="Quantité">
            <span class="msg"></span>
        </div>

        <div class="ing-field">
            <input type="text" name="ingredient_unite[]" placeholder="Unité">
            <span class="msg"></span>
        </div>

    </div>

<?php } ?>

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
</div>

</form>

</div>

<div class="right-panel">

<?php include("liste.php"); ?>

<div class="image-card">
<h3>Image recette sélectionnée</h3>

<?php if ($mode == "modifier") { ?>

<img src="displayImage.php?id=<?= $recette_edit['id_recette'] ?>" width="280">

<?php } else { ?>

<img src="https://via.placeholder.com/300" width="280">

<?php } ?>
</div>

</div>

</div>

<div id="confirmBox" class="popup hidden">
    <div class="popup-content">
        <p>Voulez-vous supprimer cette recette ?</p>
        <div class="popup-buttons">
            <button id="confirmYes" class="btn-submit">Oui</button>
            <button id="confirmNo" class="btn-cancel">Non</button>
        </div>
    </div>
</div>

<div id="successBox" class="popup hidden success-msg">
    <div class="popup-content">
        <p id="successText"></p>
    </div>
</div>

<?php if (isset($_GET['msg'])) { ?>

<script>
document.addEventListener("DOMContentLoaded", function () { // add event : attendre un événement et reagit 

let text = "";
//depend de php njmch nhotou fl js 
if ("<?= $_GET['msg'] ?>" == "ajout") text = "Recette ajoutée ✔";
if ("<?= $_GET['msg'] ?>" == "update") text = "Recette mise à jour ✔";
if ("<?= $_GET['msg'] ?>" == "delete") text = "Recette supprimée ✔";

let box = document.getElementById("successBox");
document.getElementById("successText").innerText = text;

box.classList.remove("hidden");

setTimeout(() => {
    box.classList.add("hidden"); //taafichi el boite de mssg 
}, 2000);

});
</script>

<?php } ?>

<script src="/recette_vf/View/backOffice_recette/recette.js"></script>
<script src="ingredient.js"></script>

</body>
</html>