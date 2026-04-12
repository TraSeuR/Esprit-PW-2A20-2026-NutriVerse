<?php
require_once "../../config.php";
require_once "../../Controller/profileC.php";
require_once "../../Model/profile.php";

$pC = new profileC();
$profile = new profile(
    $_POST['telephone'] ?? '',
    $_POST['date_naissance'] ?? '',
    $_POST['sexe'] ?? '',
    isset($_POST['poids']) ? (float) $_POST['poids'] : 0.0,
    isset($_POST['taille']) ? (float) $_POST['taille'] : 0.0,
    $_POST['objectif_nutritionnel'] ?? '',
    $_POST['preference_alimentaire'] ?? '',
    $_POST['allergies'] ?? '',
    isset($_POST['id_user']) && $_POST['id_user'] !== '' ? (int) $_POST['id_user'] : 0
);
$pC->addProfile($profile);
header("Location: index.php");
?>