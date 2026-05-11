<?php
require_once 'c:/xampp/htdocs/integ/config/config.php';
try {
    $db = config::getConnexion();
    echo "Connection successful!";
    $res = $db->query("SELECT COUNT(*) FROM produit")->fetchColumn();
    echo " Products count: " . $res;
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
