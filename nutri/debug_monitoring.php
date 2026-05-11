<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__.'/controller/ProduitController.php';
require_once __DIR__.'/service/MonitoringService.php';

echo "<h2>Debug Monitoring</h2>";

// Reset session check for debugging
session_start();
unset($_SESSION['last_monitoring_check']);

$pc = new ProduitController();
echo "Running monitoring...<br>";
$pc->runMonitoring();

echo "Checking 'ouef' specifically...<br>";
$db = config::getConnexion();
$q = $db->query("SELECT * FROM produit WHERE nom LIKE '%ouef%'");
$prod = $q->fetch();

if ($prod) {
    echo "Product: " . $prod['nom'] . "<br>";
    echo "Price: " . $prod['prix'] . "<br>";
    echo "Original Price: " . ($prod['prix_original'] ?? 'NULL') . "<br>";
    echo "Expiration: " . $prod['date_expiration'] . "<br>";
    
    $today = new DateTime();
    $expDate = new DateTime($prod['date_expiration']);
    $interval = $today->diff($expDate);
    $daysLeft = $interval->invert ? -$interval->days : $interval->days;
    echo "Days left: " . $daysLeft . "<br>";
} else {
    echo "Product 'ouef' not found.<br>";
}

echo "Done debug.";
?>
