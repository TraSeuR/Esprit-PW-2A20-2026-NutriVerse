<?php
// ─────────────────────────────────────────────────────────────
// ACTION (Back Office): Add user — delegate to controller
// ─────────────────────────────────────────────────────────────

// Admin guard: checks session + role + sends no-cache headers
require_once __DIR__ . "/../../Controller/auth_check_admin.php";

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: back.php");
    exit();
}

require_once __DIR__ . "/../../Controller/userC.php";

$userC = new userC();
$userC->processBackAddUser();
