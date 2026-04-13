<?php
include("../../Controller/recetteC.php");
include("../../Model/recette.php");

if (
    isset($_POST['nom']) &&
    isset($_POST['description']) &&
    isset($_POST['etapes']) &&
    isset($_POST['temps']) &&
    isset($_POST['categorie'])
) {

    $image = "";

    // nhotou taswira
    if (isset($_FILES['image']) && $_FILES['image']['name'] != "") {

        $image = $_FILES['image']['name'];

       move_uploaded_file(
    $_FILES['image']['tmp_name'],
    "images/" . $image
);
    }

    $recette = new recette(
        $_POST['nom'],
        $_POST['description'],
        $_POST['etapes'],
        $_POST['temps'],
        $_POST['categorie'],
        $image
    );

    $recetteC = new recetteC();
    $recetteC->addRecette($recette);

    header('Location: admin.php');
   
}

?>