<?php
/**
 * auth_check.php
 * ─────────────────────────────────────────────────────
 * Reusable authentication guard for ALL protected pages.
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
// Detect relative path to login page based on current file location.
// Works for both FrontOffice and BackOffice pages.
if (!isset($_SESSION['id_user'])) {
    // Determine which login page to redirect to
    $currentFile = $_SERVER['SCRIPT_FILENAME'] ?? '';

    if (strpos($currentFile, 'BackOffice') !== false) {
        header("Location: ../FrontOffice/login.php");
    } else {
        header("Location: login.php");
    }
    exit();
}
