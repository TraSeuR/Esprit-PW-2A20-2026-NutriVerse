<?php
require_once "../../Controller/userC.php";
require_once "../../Model/user.php";
require_once "../../Controller/profileC.php";
require_once "../../Model/profile.php";
require_once "../../config.php";

$uC = new userC();
$hashed_password = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
$user = new user($_POST['nom'], $_POST['prenom'], $_POST['email'], $hashed_password, $_POST['role'] ?? "utilisateur", "null", $_POST['etat_compte'] ?? "actif");
$uC->addUser($user);

// Fetch the auto-incremented ID for the new user
$db = config::getConnexion();
$last_id = (int) $db->lastInsertId();

$pC = new profileC();
$profile = new profile(
    $_POST['telephone'] ?? '',
    $_POST['date_naissance'] ?? '',
    $_POST['sexe'] ?? '',
    isset($_POST['poids']) && $_POST['poids'] !== '' ? (float) $_POST['poids'] : 0.0,
    isset($_POST['taille']) && $_POST['taille'] !== '' ? (float) $_POST['taille'] : 0.0,
    $_POST['objectif_nutritionnel'] ?? '',
    $_POST['preference_alimentaire'] ?? '',
    $_POST['allergies'] ?? '',
    $last_id
);
$pC->addProfile($profile);

header("Location: back.php");
?>