<?php
include("../../../controller/recetteC.php");
include("../../../controller/ingredientC.php");
require_once "../../../Controller/rbac_guard.php";
rbac_check(['Responsable recette']);


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
  <link rel="stylesheet" href="../assets/back.css">
  <link rel="stylesheet" href="../assets/admin.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/feather-icons"></script>


<style>
    /* Fixed Sidebar Layout Support */
    .main-content-recette {
        margin-left: 255px !important;
        width: calc(100% - 255px) !important;
        min-height: 100vh;
        background: #f7f7f7;
        position: relative;
    }

    .recettes-admin-wrapper {
        display: flex;
        gap: 20px;
        padding: 30px;
        align-items: flex-start;
    }

    /* Restore Original Popup Styles from admin.css just in case */
    .popup.hidden { display: none; }
</style>
</head>

<body>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/integ/view/BackOffice/sidebar.php'; ?>

<div class="main-content-recette">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/integ/view/BackOffice/topbar.php'; ?>

    <div class="recettes-admin-wrapper">
        
        <!-- RESTORED ORIGINAL FORM CONTAINER -->
        <div class="form-container">
            <button type="button" class="btn-reset" onclick="goHome()">←</button>

            <div class="form-card" style="width: 350px; min-width: 350px;">
                <h2 class="title">
                <?php
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
                    <option value="Healthy" <?= (isset($recette_edit['categorie']) && $recette_edit['categorie'] == 'Healthy') ? 'selected' : '' ?>>Healthy</option>
                    <option value="Vegan" <?= (isset($recette_edit['categorie']) && $recette_edit['categorie'] == 'Vegan') ? 'selected' : '' ?>>Vegan</option>
                    <option value="Cuisine Durable" <?= (isset($recette_edit['categorie']) && $recette_edit['categorie'] == 'Cuisine Durable') ? 'selected' : '' ?>>Cuisine Durable</option>
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

                <button type="button" class="btn-add">+ Ajouter Ingrédient</button>

                <div class="form-buttons">
                <?php if ($mode == "modifier") { ?>
                    <button type="submit" class="btn-submit">Mettre à jour</button>
                    <a href="admin.php" class="btn-cancel">Annuler</a>
                <?php } else { ?>
                    <button type="submit" class="btn-submit">Ajouter Recette</button>
                    <button type="reset" class="btn-cancel">Annuler</button>
                <?php } ?>
                </div>
                </form>
            </div>
        </div>

        <!-- RIGHT PANEL -->
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>

<?php if (isset($_GET['msg'])) { ?>
<script>
document.addEventListener("DOMContentLoaded", function () {
    let text = "";
    if ("<?= $_GET['msg'] ?>" == "ajout") text = "Recette ajoutée ✔";
    if ("<?= $_GET['msg'] ?>" == "update") text = "Recette mise à jour ✔";
    if ("<?= $_GET['msg'] ?>" == "delete") text = "Recette supprimée ✔";

    let box = document.getElementById("successBox");
    document.getElementById("successText").innerText = text;

    box.classList.remove("hidden");
    setTimeout(() => { box.classList.add("hidden"); }, 2000);
});
</script>
<?php } ?>

<script src="recette.js"></script>
<script src="ingredient.js"></script>

</body>
</html>
