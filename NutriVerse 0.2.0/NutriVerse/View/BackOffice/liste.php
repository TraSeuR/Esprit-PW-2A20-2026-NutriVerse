<?php
// ─────────────────────────────────────────────────────────────
// ACTION (Back Office): List users — delegate to controller
// ─────────────────────────────────────────────────────────────

// Admin guard: checks session + role + sends no-cache headers
require_once __DIR__ . "/../../Controller/auth_check_admin.php";

require_once "../../Controller/userC.php";
require_once "../../Controller/profileC.php";

$userC    = new userC();
$profileC = new profileC();
$list     = $userC->listUser();
?>
