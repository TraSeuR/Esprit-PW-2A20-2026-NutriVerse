<?php
require_once __DIR__ . '/../../../controller/RerégimeC.php';

$id_rerégime = isset($_GET['id_rerégime']) ? $_GET['id_rerégime'] : null;

if ($id_rerégime) {
    $controller = new RerégimeC();
    if ($controller->deleteRerégime($id_rerégime)) {
        // Redirection aprÃƒÂ¨s suppression rÃƒÂ©ussie
        header("Location: view_ready_plannings.php?msg=deleted");
        exit();
    } else {
        echo "Erreur lors de la suppression.";
    }
} else {
    echo "ID manquant.";
}
?>
