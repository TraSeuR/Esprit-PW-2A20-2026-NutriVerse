<?php
/**
 * complete_profile_action.php  (View/FrontOffice)
 * ──────────────────────────────────────────────────────
 * Handles the profile completion form submission
 * for new Google users.
 */

require_once __DIR__ . '/../../Controller/auth_check.php';
require_once __DIR__ . '/../../Controller/csrf.php';

// Must come from complete_profile.php
if (!isset($_SESSION['google_new_user'])) {
    header('Location: index.php');
    exit();
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: complete_profile.php');
    exit();
}

csrf_verify();

require_once __DIR__ . '/../../Controller/profileC.php';

// ── Collect form fields ───────────────────────────────
$telephone              = trim($_POST['telephone'] ?? '');
$date_naissance         = trim($_POST['date_naissance'] ?? '');
$sexe                   = trim($_POST['sexe'] ?? '');
$poids                  = isset($_POST['poids'])  && $_POST['poids']  !== '' ? (float) $_POST['poids']  : 0.0;
$taille                 = isset($_POST['taille']) && $_POST['taille'] !== '' ? (float) $_POST['taille'] : 0.0;
$objectif_nutritionnel  = trim($_POST['objectif_nutritionnel'] ?? '');
$preference_alimentaire = trim($_POST['preference_alimentaire'] ?? '');
$allergies              = trim($_POST['allergies'] ?? '');

// ── Validate required fields ──────────────────────────
$errors = [];
if (empty($telephone))    $errors[] = 'phone_required';
if (empty($date_naissance)) $errors[] = 'birthdate_required';
if (empty($sexe))         $errors[] = 'gender_required';

if (!empty($errors)) {
    header('Location: complete_profile.php?errors=' . implode(',', $errors));
    exit();
}

// ── Save profile to database ──────────────────────────
require_once __DIR__ . '/../../Model/profile.php';

$pC      = new profileC();
$profile = new profile(
    $telephone,
    $date_naissance,
    $sexe,
    $poids,
    $taille,
    $objectif_nutritionnel,
    $preference_alimentaire,
    $allergies,
    (int) $_SESSION['id_user']   // link profile to logged-in user
);

$pC->addProfile($profile);

// ── Clean up session flag + redirect ─────────────────
unset($_SESSION['google_new_user']);

header('Location: index.php?welcome=1');
exit();
