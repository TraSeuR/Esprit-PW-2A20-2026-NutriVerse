<?php
include("../../Controller/recetteC.php");

if (isset($_POST['query'])) {

    $recetteC = new recetteC();
    $search = $_POST['query'];

    $resultats = $recetteC->rechercherRecette($search);

    if (!empty($resultats)) {
        foreach ($resultats as $recette) {

           echo '
<a href="recette_details.php?id=' . $recette['id_recette'] . '" class="card-link">

    <div class="card">

        <img src="../backOffice_recette/displayImage.php?id=' . $recette['id_recette'] . '" 
        alt="' . $recette['nom'] . '">

        <div class="card-content">

            <div class="tags">
                <span class="tag">' . $recette['categorie'] . '</span>
            </div>

            <h3>' . $recette['nom'] . '</h3>

        </div>

    </div>

</a>
';
        }
    } else {
        echo "<p>Aucune recette trouvée</p>";
    }
}
?>