<?php
// ─────────────────────────────────────────────────────────────
// ACTION: Process login form — delegate to controller
// ─────────────────────────────────────────────────────────────

// No-cache headers + session start
require_once __DIR__ . "/../../Controller/no_cache.php";

// Redirect already logged-in users
if (isset($_SESSION['id_user'])) {
    header("Location: index.php");
    exit();
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/../../Controller/userC.php";

$userC = new userC();
$userC->processLogin();

