<?php
require_once "../../Controller/userC.php";
require_once "../../Model/user.php";
require_once "../../Controller/profileC.php";
require_once "../../Model/profile.php";
require_once "../../config.php";

$id_user = (int) $_POST['id_user'];

$uC = new userC();
$user = new user($_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['mot_de_passe'], $_POST['role'] ?? "utilisateur", "null", $_POST['etat_compte'] ?? "actif");
$uC->updateUser($user, $id_user);

$pC = new profileC();

// Check if profile exists, if not maybe we should add it instead of update.
$existingProfile = $pC->getProfileById($id_user);

$profile = new profile(
    $_POST['telephone'] ?? '',
    $_POST['date_naissance'] ?? '',
    $_POST['sexe'] ?? '',
    isset($_POST['poids']) && $_POST['poids'] !== '' ? (float) $_POST['poids'] : 0.0,
    isset($_POST['taille']) && $_POST['taille'] !== '' ? (float) $_POST['taille'] : 0.0,
    $_POST['objectif_nutritionnel'] ?? '',
    $_POST['preference_alimentaire'] ?? '',
    $_POST['allergies'] ?? '',
    $id_user
);

if ($existingProfile) {
    $pC->updateProfile($profile, $id_user);
} else {
    $pC->addProfile($profile);
}

header("Location: back.php");
?>