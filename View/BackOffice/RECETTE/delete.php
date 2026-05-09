<?php

include("../../../controller/recetteC.php");

if (isset($_GET['id'])) {

    $recetteC = new recetteC();
    $recetteC->deleteRecette($_GET['id']);

}

header('Location: admin.php?msg=delete');


?>