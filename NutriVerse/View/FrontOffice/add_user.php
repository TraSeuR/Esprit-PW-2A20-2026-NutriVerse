<?php

require_once "../../Controller/userC.php";
require_once "../../Model/user.php";
require_once "../../config.php";

$uC = new userC();
$user = new user($_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['mot_de_passe'], "utilisateur", "null", "actif");
$uC->addUser($user);

$db = config::getConnexion();
$last_id = $db->lastInsertId();

header("Location: registerP.php?id_user=" . $last_id);
?>