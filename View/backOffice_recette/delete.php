<?php

include ("../../Controller/recetteC.php");

if (isset($_GET['id'])) {

    $recetteC = new recetteC();
    $recetteC->deleteRecette($_GET['id']);

}

header('Location: admin.php');


?>