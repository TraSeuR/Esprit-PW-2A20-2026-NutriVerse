<?php
require_once "../../Controller/userC.php";
require_once "../../Controller/profileC.php";
$userC = new userC();
$profileC = new profileC();
$list = $userC->listUser();
?>