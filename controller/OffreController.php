<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/OffreIngredient.php';
require_once __DIR__ . '/../model/Echange.php';

// Initialisation de la base de données et des modèles (Injection de dépendance)
$database = new Database();
$db = $database->getConnection();
$offreModel = new OffreIngredient($db);
$echangeModel = new Echange($db);

// Utilisateur par défaut
$id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : 1;
$action = isset($_GET['action']) ? $_GET['action'] : 'front_list';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add') {
        $ingredient = trim($_POST['ingredient'] ?? '');
        $quantite = $_POST['quantite'] ?? '';
        $ville = trim($_POST['ville'] ?? '');
        
        if (empty($ingredient) || empty($quantite) || empty($ville)) {
            $error = "Veuillez remplir tous les champs.";
        } elseif (!is_numeric($quantite) || $quantite < 0) {
            $error = "La quantité doit être un nombre positif.";
        } elseif (!preg_match("/^[a-zA-ZÀ-ÿ\s'-]+$/", $ingredient)) {
            $error = "Le nom de l'ingrédient ne doit contenir que des lettres.";
        } elseif (!preg_match("/^[a-zA-ZÀ-ÿ\s'-]+$/", $ville)) {
            $error = "Le nom de la ville ne doit contenir que des lettres.";
        } else {
            try {
                $offreModel->setIdUser($id_user);
                $offreModel->setIngredient($ingredient);
                $offreModel->setCategorie('Autre');
                $offreModel->setQuantite($quantite);
                $offreModel->setUniteMesure('kg');
                $offreModel->setLocalisation($ville);
                $offreModel->setEtat('disponible');
                $offreModel->setTypeOffre('échange');
                
                $offreModel->addOffre();
                $success = "Offre ajoutée avec succès.";
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        $action = 'front_list';
    } 
    elseif ($action === 'update') {
        $id_offre = $_POST['id_offre'] ?? '';
        $id_offre = str_ireplace('OFF-', '', $id_offre);
        $nouvelle_quantite = $_POST['nouvelle_quantite'] ?? '';
        
        if (empty($id_offre) || empty($nouvelle_quantite)) {
            $error = "Veuillez remplir les champs de modification.";
        } else {
            try {
                $updated = $offreModel->updateOffre($id_offre, $nouvelle_quantite, 'disponible');
                if ($updated) {
                    $success = "Offre mise à jour.";
                } else {
                    $error = "Erreur : Cette offre n'existe pas ou l'ID est incorrect.";
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        $action = 'front_list';
    }
    elseif ($action === 'delete') {
        $id_offre = $_POST['id_offre'] ?? '';
        $id_offre = str_ireplace('OFF-', '', $id_offre);
        if (!empty($id_offre)) {
            try {
                $deleted = $offreModel->deleteOffre($id_offre);
                if ($deleted) {
                    $success = "Offre supprimée.";
                } else {
                    $error = "Erreur : Cette offre n'existe pas ou l'ID est incorrect.";
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        $action = 'front_list';
    }
}

// Préparation des données pour la vue
$active_tab = 'offres'; 

if ($action === 'front_list' || $action === 'front_add_view') {
    $offres = $offreModel->getOffresByUser($id_user);
    $echanges = $echangeModel->getEchangesByUser($id_user);
    $mes_offres_actives = $offreModel->getActiveOffresByUser($id_user);
    $offres_disponibles = $offreModel->readAll();
    
    // Une seule vue consolidée comme demandé
    require_once __DIR__ . '/../view/frontoffice/dashboard_user.php';
} 
elseif (strpos($action, 'admin') === 0) {
    // Gestion Admin
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($action === 'admin_toggle_block') {
            $id = $_POST['id_offre'] ?? '';
            $nouvel_etat = $_POST['nouvel_etat'] ?? 'bloqué';
            if ($id) $offreModel->updateStatus($id, $nouvel_etat);
            $success = ($nouvel_etat == 'bloqué') ? "Offre bloquée." : "Offre débloquée (disponible).";
        }
        elseif ($action === 'admin_delete') {
            $id = $_POST['id_offre'] ?? '';
            if ($id) $offreModel->deleteOffre($id);
            $success = "Offre supprimée par l'admin.";
        }
    }

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

    require_once __DIR__ . '/../view/backoffice/dashboard_admin.php';
}
?>
