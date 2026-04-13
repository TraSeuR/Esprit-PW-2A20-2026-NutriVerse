<?php
include("../../Controller/recetteC.php");

$recetteC = new recetteC();

$categorie = $_GET['categorie'] ?? '';   
$search = $_GET['search'] ?? '';
 
$recettes = $recetteC->listes($categorie, $search);
?>
<!--nekhdou la categorie chois/recherche fl lurl-->
<!--naytou l  listes mta  classe recetteC>