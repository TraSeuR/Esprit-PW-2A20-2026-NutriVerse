<?php
/**
 * auth_check_admin.php
 * ─────────────────────────────────────────────────────
 * Reusable authentication guard for ALL BackOffice pages.
 * Requires the user to be logged in AND have role = 'admin'.
 *
 * HOW TO USE:
 *   require_once __DIR__ . '/../../Controller/auth_check_admin.php';
 *
 * What it does:
 *   1. Starts the session (if not already started)
 *   2. Sends no-cache headers to block the browser BACK button
 *      from showing a protected page after logout
 *   3. Redirects non-admins to login
 */

// ── 1. Start session ──────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── 2. No-cache headers ───────────────────────────────
// These stop the browser from caching the page so that
// pressing BACK after logout always hits the server.
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");

// ── 3. Block access if not logged in or not an admin ──
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../FrontOffice/login.php");
    exit();
}
