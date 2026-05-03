<?php
/**
 * google_auth.php  (View/FrontOffice)
 * ──────────────────────────────────────────────────────
 * Entry point: user clicks "Login with Google"
 * → This page generates the Google auth URL
 * → Redirects the user to Google's consent screen.
 */

require_once __DIR__ . '/../../Controller/no_cache.php';
require_once __DIR__ . '/../../Controller/GoogleAuthC.php';

// Redirect logged-in users away
if (isset($_SESSION['id_user'])) {
    header('Location: index.php');
    exit();
}

// Build Google login URL and redirect
$url = google_get_auth_url();
header('Location: ' . $url);
exit();
