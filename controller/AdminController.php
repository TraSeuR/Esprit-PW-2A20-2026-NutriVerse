<?php
if(session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../model/OffreIngredient.php';
require_once __DIR__ . '/../model/Echange.php';
require_once __DIR__ . '/OffreController.php';
require_once __DIR__ . '/EchangeController.php';

class AdminC {
    private $offCtrl;
    private $echCtrl;

    public function __construct() {
        $this->offCtrl = new OffreC();
        $this->echCtrl = new EchangeC();
    }

    public function handleRequest() {
        $action = $_GET['action'] ?? 'list';
        $error = ''; $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if ($action === 'block_offre') {
                    $id = $_POST['id_offre'] ?? '';
                    if ($id) { $this->offCtrl->updateStatus($id, 'bloqué'); $success = "Offre bloquée."; }
                } elseif ($action === 'delete_offre') {
                    $id = $_POST['id_offre'] ?? '';
                    if ($id) { $this->offCtrl->supprimerOffre($id); $success = "Offre supprimée."; }
                } elseif ($action === 'traiter_echange') {
                    $id = $_POST['id_echange'] ?? '';
                    $decision = $_POST['decision'] ?? '';
                    if ($id && $decision) { $this->echCtrl->updateDecision($id, $decision); $success = "Échange mis à jour."; }
                } elseif ($action === 'delete_echange') {
                    $id = $_POST['id_echange'] ?? '';
                    if ($id) { $this->echCtrl->supprimerEchange($id); $success = "Échange supprimé."; }
                }
            } catch (Exception $e) { $error = $e->getMessage(); }
        }

        // Action: list (Default)
        $f_etat = $_GET['filtre_etat'] ?? 'Toutes';
        $f_type = $_GET['filtre_type'] ?? 'Tous';
        $offres = $this->offCtrl->readAll($f_etat, $f_type);
        $echanges = $this->echCtrl->listeEchanges(); // //jointure : Appel de la méthode avec jointure pour le back-office
        
        $stats = [
            'actives' => $this->offCtrl->countByEtat('disponible'),
            'dons' => $this->offCtrl->countByType('don'),
            'bloquees' => $this->offCtrl->countByEtat('bloqué'),
            'attente' => $this->echCtrl->countByStatut('en_attente')
        ];

        require_once __DIR__ . '/../view/backoffice/dashboard_admin.php';
    }
}

// Router
if (basename($_SERVER['PHP_SELF']) == 'AdminController.php') {
    $controller = new AdminC();
    $controller->handleRequest();
}
?>
