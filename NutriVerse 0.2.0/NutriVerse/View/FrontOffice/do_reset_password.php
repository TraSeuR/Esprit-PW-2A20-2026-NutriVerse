<?php
// ─────────────────────────────────────────────────────────────
// ACTION: Reset password — delegate to controller
// ─────────────────────────────────────────────────────────────

// No-cache headers + session start
require_once __DIR__ . "/../../Controller/no_cache.php";

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: forgot_password.php");
    exit();
}

require_once __DIR__ . "/../../Controller/userC.php";

$userC = new userC();
$userC->processResetPassword();

