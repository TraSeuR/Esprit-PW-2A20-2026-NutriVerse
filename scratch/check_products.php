<?php
require_once 'c:/xampp/htdocs/integ/config/config.php';
$db = config::getConnexion();
$res = $db->query("SELECT * FROM produit")->fetchAll();
print_r($res);
?>
