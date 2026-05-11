<?php
// -------------------------------------------------------------
// ACTION (Back Office): List users à delegate to controller
// -------------------------------------------------------------

// Admin guard: checks session + role + sends no-cache headers
require_once __DIR__ . "/../../../Controller/auth_check_admin.php";

require_once __DIR__ . "/../../../Controller/userC.php";
require_once __DIR__ . "/../../../Controller/profileC.php";

$userC    = new userC();
$profileC = new profileC();
$list     = $userC->listUser();
?>

