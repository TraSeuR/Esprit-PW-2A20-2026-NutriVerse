<?php
/**
 * no_cache.php
 * ─────────────────────────────────────────────────────
 * Sends no-cache headers for sensitive flow pages
 * that are NOT behind a login wall but should NOT be
 * cached (OTP, registerP, new_password, etc.).
 *
 * HOW TO USE:
 *   require_once __DIR__ . '/../../Controller/no_cache.php';
 */

// ── Start session ─────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── No-cache headers ──────────────────────────────────
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
