<?php
session_start();
include "../../Controller/userC.php";

$userC = new userC();
$user = $userC->userLogin($_POST['email'], $_POST['mot_de_passe']);

if ($user) {
    $_SESSION['email'] = $user['email'];
    $_SESSION['id_user'] = $user['id_user'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['nom'] = $user['nom'];
    $_SESSION['prenom'] = $user['prenom'];

    if ($user['role'] === 'admin') {
        header("Location: ../BackOffice/back.php");
    } else {
        header("Location: index.php");
    }
    exit();
} else {
    header("Location: login.php?error=invalid_credentials");
    exit();
}
