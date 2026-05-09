<?php
require_once __DIR__ . '/../../../controller/RerégimeC.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$id = $_GET['id'] ?? null;
if ($id) {
    $controller = new RerégimeC();
    $controller->deleteRerégime($id);
}

if (isset($_SESSION['last_id_rerégime'])) {
    unset($_SESSION['last_id_rerégime']);
}

$redirect = $_GET['redirect'] ?? 'list_programmes.php';
header("Location: " . $redirect);
exit();
?>
