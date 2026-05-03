<?php
/**
 * google_callback.php  (View/FrontOffice)
 * ──────────────────────────────────────────────────────
 * Google redirects here after the user grants permission.
 * This page handles the full OAuth flow:
 *   1. Validate the state (CSRF protection)
 *   2. Exchange the code for an access token
 *   3. Get the user's profile from Google
 *   4. Find existing user OR create new account
 *   5. Set session and redirect
 */

require_once __DIR__ . '/../../Controller/no_cache.php';
require_once __DIR__ . '/../../Controller/GoogleAuthC.php';

// ── Step 1: Check for errors from Google ─────────────
if (isset($_GET['error'])) {
    // User cancelled or something went wrong
    header('Location: login.php?errors=google_cancelled');
    exit();
}

// ── Step 2: Validate state (CSRF protection) ─────────
$state         = $_GET['state'] ?? '';
$session_state = $_SESSION['google_oauth_state'] ?? '';

if (empty($state) || !hash_equals($session_state, $state)) {
    // State mismatch — possible CSRF attack
    header('Location: login.php?errors=google_invalid_state');
    exit();
}
unset($_SESSION['google_oauth_state']); // clean up

// ── Step 3: Exchange authorization code for token ────
$code = $_GET['code'] ?? '';
if (empty($code)) {
    header('Location: login.php?errors=google_no_code');
    exit();
}

$access_token = google_exchange_code($code);
if (!$access_token) {
    header('Location: login.php?errors=google_token_failed');
    exit();
}

// ── Step 4: Get user profile from Google ─────────────
$google_user = google_get_user_info($access_token);
if (!$google_user || empty($google_user['email'])) {
    header('Location: login.php?errors=google_no_email');
    exit();
}

$google_id = $google_user['sub'];            // Google's unique user ID
$email     = $google_user['email'];
$prenom    = $google_user['given_name']  ?? '';
$nom       = $google_user['family_name'] ?? '';

// ── Step 5: Find or create user in DB ────────────────
$user = google_find_or_create_user($google_id, $email, $prenom, $nom);
if (!$user) {
    header('Location: login.php?errors=google_db_error');
    exit();
}

// ── Step 6: Set session (same as normal login) ────────
$_SESSION['id_user'] = $user['id_user'];
$_SESSION['email']   = $user['email'];
$_SESSION['role']    = $user['role'];
$_SESSION['nom']     = $user['nom'];
$_SESSION['prenom']  = $user['prenom'];
$_SESSION['avatar']  = $user['avatar'] ?? 'avatar1.png';

// ── Step 7: Redirect based on profile status ──────────
// Admins → BackOffice
if ($user['role'] === 'admin') {
    header('Location: ../BackOffice/back.php');
    exit();
}

// New Google users → profile completion page
if (!google_has_profile($user['id_user'])) {
    $_SESSION['google_new_user'] = true; // flag for complete_profile.php
    header('Location: complete_profile.php');
    exit();
}

// Existing users → homepage
header('Location: index.php');
exit();
