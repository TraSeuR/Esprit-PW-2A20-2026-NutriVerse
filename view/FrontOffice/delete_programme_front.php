<?php
require_once __DIR__ . '/../../controller/RegimeController.php';

$id_regime = isset($_GET['id_regime']) ? $_GET['id_regime'] : null;

if ($id_regime) {
    $controller = new RegimeController();
    if ($controller->deleteRegime($id_regime)) {
        // Redirection après suppression réussie
        header("Location: view_ready_plannings.php?msg=deleted");
        exit();
    } else {
        echo "Erreur lors de la suppression.";
    }
} else {
    echo "ID manquant.";
}
?>
