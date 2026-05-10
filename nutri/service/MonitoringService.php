<?php
require_once __DIR__.'/../controller/ProduitController.php';

class MonitoringService {
    public static function checkAll(): void {
        $produitController = new ProduitController();
        $produitController->runMonitoring();
    }
}
?>
