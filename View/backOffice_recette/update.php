<?php
include("../../Controller/recetteC.php");
include("../../Model/recette.php");

$recetteC = new recetteC();
if (isset($_POST['id'])) {

    // ken mbdlnch el pic
    $image = $_POST['ancienne_image'];

    // ken bdlna el pic
    if (
        isset($_FILES['image']) &&
        $_FILES['image']['name'] != ""
    ) {

        $image = $_FILES['image']['name'];

       move_uploaded_file(
    $_FILES['image']['tmp_name'],
    "../backOffice_recette/images/" . $image
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
//update fl base 
    $recetteC->updateRecette(
        $_POST['id'],
        $recette
    );

    header('Location: admin.php');
    exit();
}

//naffichiw fl forulr

if (isset($_GET['id_recette'])) {

    $recette = $recetteC->getRecette(
        $_GET['id_recette']
    );

} else {

    header('Location: admin.php');
    
}

?>