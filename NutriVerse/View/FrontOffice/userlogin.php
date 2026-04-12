<?php
session_start();
include "../../Controller/userC.php";

$userC = new userC();
$user = $userC->userLogin($_POST['email'], $_POST['mot_de_passe']);

if ($user) {
    $_SESSION['email'] = $user['email'];
    $_SESSION['id'] = $user['id'];
    header("Location: index.php");
    exit();
} else {
    header("Location: login.php?error=invalid_credentials");
    exit();
}
