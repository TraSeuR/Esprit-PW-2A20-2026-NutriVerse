<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

require_once "../../Controller/userC.php";
require_once "../../Controller/profileC.php";
require_once "../../Model/user.php";
require_once "../../Model/profile.php";

$userC = new userC();
$profileC = new profileC();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_user = $_SESSION['id_user'];

    $currentUser = $userC->getUserById($id_user);

    // Update User data
    $nom = $_POST['nom'] ?? $currentUser['nom'];
    $prenom = $_POST['prenom'] ?? $currentUser['prenom'];
    $email = $_POST['email'] ?? $currentUser['email'];
    $role = $currentUser['role'];
    $etat_compte = $currentUser['etat_compte'];
    $remember_me = $currentUser['remember_me'] ?? 0;

    $mot_de_passe = !empty($_POST['mot_de_passe']) ? password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT) : $currentUser['mot_de_passe'];

    $updatedUser = new user($nom, $prenom, $email, $mot_de_passe, $role, $remember_me, $etat_compte);
    $userC->updateUser($updatedUser, $id_user);

    // Update Session
    $_SESSION['nom'] = $nom;
    $_SESSION['prenom'] = $prenom;
    $_SESSION['email'] = $email;

    // Check if profile exists
    $existingProfile = $profileC->getProfileById($id_user);

    // Create new profile object
    $telephone = $_POST['telephone'] ?? '';
    $date_naissance = $_POST['date_naissance'] ?? '';
    $sexe = $_POST['sexe'] ?? '';
    $poids = !empty($_POST['poids']) ? (float) $_POST['poids'] : 0.0;
    $taille = !empty($_POST['taille']) ? (float) $_POST['taille'] : 0.0;
    $objectif_nutritionnel = $_POST['objectif_nutritionnel'] ?? '';
    $preference_alimentaire = $_POST['preference_alimentaire'] ?? '';
    $allergies = $_POST['allergies'] ?? '';

    $newProfile = new profile($telephone, $date_naissance, $sexe, $poids, $taille, $objectif_nutritionnel, $preference_alimentaire, $allergies, $id_user);

    if ($existingProfile) {
        $profileC->updateProfile($newProfile, $id_user);
    } else {
        $profileC->addProfile($newProfile);
    }

    header("Location: edit_profile.php?success=1");
    exit();
}
