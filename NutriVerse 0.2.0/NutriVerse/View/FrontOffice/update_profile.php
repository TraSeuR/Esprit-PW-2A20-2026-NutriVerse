<?php
// ─────────────────────────────────────────────────────────────
// ACTION: Update profile (Front Office) — delegate to controller
// ─────────────────────────────────────────────────────────────

// Auth guard: checks session + sends no-cache headers
require_once __DIR__ . "/../../Controller/auth_check.php";

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: edit_profile.php");
    exit();
}

require_once __DIR__ . "/../../Controller/userC.php";

$userC = new userC();
$userC->processUpdateProfile();

