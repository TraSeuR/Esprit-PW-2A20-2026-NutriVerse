<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/Echange.php';
require_once __DIR__ . '/../model/OffreIngredient.php';

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
        $id_echange = $_POST['id_echange'] ?? '';
        $note = (int)($_POST['note'] ?? 0);
        $role = $_POST['role'] ?? 'demandeur';

        if (empty($id_echange) || $note < 1 || $note > 5) {
            $error = "Veuillez fournir un ID d'échange et une note valide de 1 à 5.";
        } else {
            if ($role === 'demandeur') {
                $status = $echangeModel->addNoteDemandeur($id_echange, $note);
            } else {
                $status = $echangeModel->addNoteOffreur($id_echange, $note);
            }

            if ($status) {
                $success = "Votre note a été enregistrée.";
            } else {
                $error = "Erreur : ID d'échange incorrect ou introuvable.";
            }
        }
    }
}

// Redirection
$offres = $offreModel->getOffresByUser($id_user);
$echanges = $echangeModel->getEchangesByUser($id_user);
$mes_offres_actives = $offreModel->getActiveOffresByUser($id_user);
require_once __DIR__ . '/../view/frontoffice/dashboard_user.php';
exit();
?>
