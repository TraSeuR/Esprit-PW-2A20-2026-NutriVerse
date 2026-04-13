<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/OffreIngredient.php';

$database = new Database();
$db = $database->getConnection();
$offreModel = new OffreIngredient($db);

$id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : 1;
$action = isset($_GET['action']) ? $_GET['action'] : '';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add') {
        $description = trim($_POST['description'] ?? '');
        $don_solidaire = $_POST['don_solidaire'] ?? '';

        if (empty($description) || empty($don_solidaire)) {
            $error = "Veuillez fournir une description et cocher la case don solidaire.";
        } else {
            try {
                $offreModel->setIdUser($id_user);
                $offreModel->setIngredient('Don Solidaire');
                $offreModel->setCategorie('Autre');
                $offreModel->setQuantite(0);
                $offreModel->setUniteMesure('N/A');
                $offreModel->setLocalisation('N/A');
                $offreModel->setEtat('disponible');
                $offreModel->setTypeOffre('don');
                $offreModel->setDescription($description);
                
                $offreModel->addOffre();
                $success = "Don solidaire publié avec succès.";
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    }
}

// DonController redirige toujours vers le dashboard consolidé
$offres = $offreModel->getOffresByUser($id_user);
require_once __DIR__ . '/../model/Echange.php';
$echangeModel = new Echange($db);
$echanges = $echangeModel->getEchangesByUser($id_user);
$mes_offres_actives = $offreModel->getActiveOffresByUser($id_user);
$offres_disponibles = $offreModel->readAll();

require_once __DIR__ . '/../view/frontoffice/dashboard_user.php';
exit();
?>
