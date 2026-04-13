<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/Echange.php';
require_once __DIR__ . '/../model/OffreIngredient.php';

// Initialisation
$database = new Database();
$db = $database->getConnection();
$echangeModel = new Echange($db);
$offreModel = new OffreIngredient($db);

$id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : 1;
$action = isset($_GET['action']) ? $_GET['action'] : '';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add') {
        $id_offre_demandeur = $_POST['id_offre_demandeur'] ?? '';
        $id_offre_offreur = $_POST['id_offre_offreur'] ?? '';
        $message = trim($_POST['message'] ?? '');
        $date_proposee = $_POST['date_proposee'] ?? '';

        if (empty($id_offre_demandeur) || empty($id_offre_offreur) || empty($date_proposee)) {
            $error = "Veuillez sélectionner votre offre et l'offre ciblée.";
        } else {
            $offre_cible = $offreModel->getOffreById($id_offre_offreur);
            if ($offre_cible) {
                try {
                    $echangeModel->setIdOffreDemandeur($id_offre_demandeur);
                    $echangeModel->setIdOffreOffreur($id_offre_offreur);
                    $echangeModel->setIdDemandeur($id_user);
                    $echangeModel->setIdOffreur($offre_cible['id_user']);
                    $echangeModel->setMessage($message);
                    $echangeModel->setStatut('en_attente');
                    
                    $echangeModel->addEchange();
                    $success = "Demande d'échange envoyée avec succès.";
                } catch (Exception $e) {
                    $error = $e->getMessage();
                }
            } else {
                $error = "Offre cible introuvable.";
            }
        }
    }
    elseif ($action === 'traiter') {
        $id_echange = $_POST['id_echange'] ?? '';
        $id_echange = str_ireplace('ECH-', '', $id_echange);
        $decision = $_POST['decision'] ?? '';
        $source = $_GET['source'] ?? 'front';

        if (empty($id_echange) || empty($decision)) {
            $error = "Veuillez spécifier l'ID et la décision.";
        } elseif (!is_numeric($id_echange)) {
            $error = "L'ID de l'échange doit être un nombre.";
        } else {
            try {
                if ($echangeModel->updateDecision($id_echange, $decision)) {
                    $success = "L'échange " . htmlspecialchars($id_echange) . " est maintenant : " . htmlspecialchars($decision);
                } else {
                    $error = "Erreur : Cet échange n'existe pas ou l'ID est incorrect.";
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    }
    elseif ($action === 'delete') {
        $id_annuler = $_POST['id_annuler'] ?? $_POST['id_echange'] ?? '';
        $id_annuler = str_ireplace('ECH-', '', $id_annuler);
        
        if (empty($id_annuler)) {
            $error = "Veuillez fournir l'ID de l'échange.";
        } elseif (!is_numeric($id_annuler)) {
            $error = "L'ID doit être un nombre.";
        } else {
            try {
                if ($echangeModel->delete($id_annuler)) {
                    $success = "L'échange ID : " . htmlspecialchars($id_annuler) . " a été supprimé.";
                } else {
                    $error = "Erreur : Cet échange n'existe pas ou l'ID est incorrect.";
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    }
}

// Préparation vue
$source = $_GET['source'] ?? 'front';

if ($source === 'admin') {
    $f_etat = $_GET['filtre_etat'] ?? 'Toutes';
    $f_type = $_GET['filtre_type'] ?? 'Tous';
    $offres = $offreModel->readAll($f_etat, $f_type);
    $echanges = $echangeModel->getAllEchanges();
    $stats = [
        'actives' => $offreModel->countByEtat('disponible'),
        'dons' => $offreModel->countByType('don'),
        'bloquees' => $offreModel->countByEtat('bloqué'),
        'attente' => $echangeModel->countByStatut('en_attente')
    ];
    $active_tab = 'echanges';
    require_once __DIR__ . '/../view/backoffice/dashboard_admin.php';
} else {
    $offres = $offreModel->getOffresByUser($id_user);
    $echanges = $echangeModel->getEchangesByUser($id_user);
    $mes_offres_actives = $offreModel->getActiveOffresByUser($id_user);
    $offres_disponibles = $offreModel->readAll();
    
    $view = $_GET['view'] ?? 'dashboard';
    if ($view === 'echanges') {
        require_once __DIR__ . '/../view/frontoffice/mes_echanges.php';
    } else {
        require_once __DIR__ . '/../view/frontoffice/dashboard_user.php';
    }
}
?>
