<?php
require_once __DIR__ . '/../../../controller/RegimeC.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$id = $_GET['id'] ?? null;
if ($id) {
    $controller = new RegimeC();
    $controller->deleteRegime($id);
}

if (isset($_SESSION['last_id_regime'])) {
    unset($_SESSION['last_id_regime']);
}

$redirect = $_GET['redirect'] ?? 'list_programmes.php';
header("Location: " . $redirect);
exit();
?>
