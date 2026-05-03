<?php
// ─────────────────────────────────────────────────────────────
// ACTION: Register step 2 — create user + profile in the DB
// (Only runs after step 1 stored data in $_SESSION['pending_user'])
// ─────────────────────────────────────────────────────────────

// No-cache headers + session start
require_once __DIR__ . "/../../Controller/no_cache.php";

// Guard: must come from the register → registerP.php flow
if (empty($_SESSION['pending_user'])) {
    header("Location: register.php");
    exit();
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: registerP.php");
    exit();
}

require_once __DIR__ . "/../../Controller/profileC.php";

$pC = new profileC();
$pC->processAddProfile();
