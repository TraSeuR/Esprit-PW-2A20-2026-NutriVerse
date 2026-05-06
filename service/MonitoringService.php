<?php
require_once __DIR__.'/../Controller/ProduitController.php';

class MonitoringService {
    public static function checkAll(): void {
        $produitController = new ProduitController();
        $produitController->runMonitoring();
    }
}
?>
