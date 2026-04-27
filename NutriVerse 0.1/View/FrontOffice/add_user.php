<?php

require_once "../../Controller/userC.php";
require_once "../../Model/user.php";
require_once "../../config.php";

$uC = new userC();
$hashed_password = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
$user = new user($_POST['nom'], $_POST['prenom'], $_POST['email'], $hashed_password, "utilisateur", "null", "actif");
$uC->addUser($user);

$db = config::getConnexion();
$last_id = $db->lastInsertId();

header("Location: registerP.php?id_user=" . $last_id);
?>