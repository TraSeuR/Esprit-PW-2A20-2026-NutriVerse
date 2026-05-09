<?php
require_once __DIR__ . '/../../controller/RegimeController.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$id = $_GET['id'] ?? null;
if ($id) {
    $controller = new RegimeController();
    $controller->deleteRegime($id);
}

if (isset($_SESSION['last_id_regime'])) {
    unset($_SESSION['last_id_regime']);
}

$redirect = $_GET['redirect'] ?? 'list_programmes.php';
header("Location: " . $redirect);
exit();
?>
