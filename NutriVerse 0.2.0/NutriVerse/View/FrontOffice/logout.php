<?php
/**
 * logout.php
 * ─────────────────────────────────────────────────────
 * Destroys the user session and all related cookies,
 * then redirects to the login page.
 *
 * After logout, the browser BACK button will always
 * redirect to login because:
 *   1. The session is fully destroyed here.
 *   2. Protected pages send no-cache headers (via auth_check.php),
 *      so the browser never serves a cached copy.
 */

// ── 1. Start session so we can destroy it ────────────
session_start();

// ── 2. No-cache headers (prevent caching this page too) ──
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");

// ── 3. Clear all session variables ───────────────────
session_unset();

// ── 4. Delete the session cookie from the browser ────
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),        // cookie name (usually PHPSESSID)
        '',                    // empty value
        time() - 42000,        // expire in the past = delete it
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// ── 5. Delete "remember me" cookie (if set) ──────────
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', [
        'expires'  => time() - 3600,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

// ── 6. Destroy the session on the server ─────────────
session_destroy();

// ── 7. Redirect to login ──────────────────────────────
header("Location: login.php");
exit();
