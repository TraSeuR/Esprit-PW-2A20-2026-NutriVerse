<?php
/**
 * auth_check.php
 * ─────────────────────────────────────────────────────
 * Reusable authentication guard for ALL protected FrontOffice pages.
 *
 * HOW TO USE:
 *   require_once __DIR__ . '/../../Controller/auth_check.php';
 *
 * What it does:
 *   1. Starts the session (if not already started)
 *   2. Sends no-cache headers so the browser never serves
 *      a cached version of a protected page after logout
 *   3. Redirects to login if the user is not logged in
 */

// ── 1. Start session ──────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── 2. No-cache headers ───────────────────────────────
// These prevent the browser from showing a cached protected
// page when the user presses the BACK button after logout.
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");

// ── 3. Redirect if not logged in ──────────────────────
if (!isset($_SESSION['id_user'])) {
    /*
     * The HTTP Location header path is relative to the CALLER's URL directory.
     *
     * All current callers of auth_check.php live inside:
     *   FrontOffice/utilisateur/   (edit_profile, update_profile,
     *                               complete_profile, logout, etc.)
     *
     * login.php is in the same folder → "login.php" is always correct.
     *
     * Depth detection is kept as a safety net in case this guard
     * is reused from other locations in the future.
     */
    $callerPath = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME'] ?? '');

    if (strpos($callerPath, '/BackOffice/utilisateur/') !== false) {
        // Caller is in BackOffice/utilisateur/ (2 levels from View/)
        header("Location: /integ/View/FrontOffice/utilisateur/login.php");
    } elseif (strpos($callerPath, '/BackOffice/') !== false) {
        // Caller is at BackOffice root level
        header("Location: /integ/View/FrontOffice/utilisateur/login.php");
    } elseif (strpos($callerPath, '/FrontOffice/utilisateur/') !== false) {
        // Caller is in FrontOffice/utilisateur/ — login.php is in the same dir
        header("Location: login.php");
    } else {
        // Caller is at FrontOffice root level (e.g. index.php)
        header("Location: utilisateur/login.php");
    }
    exit();
}
