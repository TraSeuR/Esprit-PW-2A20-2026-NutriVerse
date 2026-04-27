<?php
if(session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/Echange.php';
require_once __DIR__ . '/../model/OffreIngredient.php';

class EchangeC {

    public function getUserRating($id_user) {
        try {
            $pdo = config::getConnexion();
            // On calcule la moyenne des notes reçues par cet utilisateur
            // Règle d'or : La note REÇUE par un OFFREUR est stockée dans 'note_demandeur'
            //              La note REÇUE par un DEMANDEUR est stockée dans 'note_offreur'
            $sql = "SELECT AVG(n) as moyenne FROM (
                        SELECT note_demandeur as n FROM echange WHERE id_offreur = :id1 AND note_demandeur > 0
                        UNION ALL
                        SELECT note_offreur as n FROM echange WHERE id_demandeur = :id2 AND note_offreur > 0
                    ) as t";
            $req = $pdo->prepare($sql);
            $req->execute(['id1' => $id_user, 'id2' => $id_user]);
            $res = $req->fetch();
            return $res['moyenne'] ? round($res['moyenne'], 1) : 0;
        } catch (PDOException $e) { return 0; }
    }

    // --- LA JOINTURE (Jointure entre Echange et OffreIngredient) ---
    // On respecte l'exemple avec try/catch et config::getConnexion()
    
    public function listeEchanges() {
        try {
            $pdo = config::getConnexion();
            // //jointure : Liaison entre echange et offreingredient (Demandeur et Offreur)
            $sql = "SELECT e.*, 
                    od.ingredient as ing_donne, od.quantite as qte_donne,
                    oo.ingredient as ing_recu, oo.quantite as qte_recu
                    FROM echange e
                    LEFT JOIN offreingredient od ON e.id_offre_demandeur = od.id_offre
                    LEFT JOIN offreingredient oo ON e.id_offre_offreur = oo.id_offre
                    ORDER BY e.id_echange DESC"; //jointure
            $query = $pdo->prepare($sql);
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo 'Erreur: ' . $e->getMessage();
            return [];
        }
    }

    public function afficheEchangesByUser($id_user) {
        try {
            $pdo = config::getConnexion();
            // //jointure : Récupération des ingrédients liés aux échanges de l'utilisateur
            $sql = "SELECT e.*, 
                    od.ingredient as ing_donne, od.quantite as qte_donne,
                    oo.ingredient as ing_recu, oo.quantite as qte_recu
                    FROM echange e
                    LEFT JOIN offreingredient od ON e.id_offre_demandeur = od.id_offre
                    LEFT JOIN offreingredient oo ON e.id_offre_offreur = oo.id_offre
                    WHERE (e.id_demandeur = :id_user OR e.id_offreur = :id_user) 
                    AND e.statut != 'bloqué'
                    ORDER BY e.id_echange DESC"; //jointure
            $query = $pdo->prepare($sql);
            $query->execute(['id_user' => $id_user]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo 'Erreur: ' . $e->getMessage();
            return [];
        }
    }

    public function countByStatut($statut) {
        try {
            $pdo = config::getConnexion();
            $query = $pdo->prepare("SELECT COUNT(*) as total FROM echange WHERE statut = :statut");
            $query->execute(['statut' => $statut]);
            $res = $query->fetch();
            return $res['total'];
        } catch (PDOException $e) { return 0; }
    }

    public function getEchangeById($id) {
        try {
            $pdo = config::getConnexion();
            $query = $pdo->prepare("SELECT * FROM echange WHERE id_echange = :id");
            $query->execute(['id' => $id]);
            return $query->fetch();
        } catch (PDOException $e) { return null; }
    }

    public function ajouterEchange($obj) {
        try {
            $pdo = config::getConnexion();
            $sql = "INSERT INTO echange (id_offre_demandeur, id_offre_offreur, id_demandeur, id_offreur, message, date_demande, statut) 
                    VALUES (:id_od, :id_oo, :id_d, :id_o, :msg, :date_d, :statut)";
            $query = $pdo->prepare($sql);
            return $query->execute([
                'id_od' => $obj->getIdOffreDemandeur(),
                'id_oo' => $obj->getIdOffreOffreur(),
                'id_d' => $obj->getIdDemandeur(),
                'id_o' => $obj->getIdOffreur(),
                'msg' => $obj->getMessage(),
                'date_d' => $obj->getDateDemande(),
                'statut' => $obj->getStatut()
            ]);
        } catch (PDOException $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function modifierEchange($id, $date, $message, $id_od, $id_oo) {
        try {
            $pdo = config::getConnexion();
            $sql = "UPDATE echange SET date_demande = :date, message = :message, id_offre_demandeur = :od, id_offre_offreur = :oo WHERE id_echange = :id";
            $query = $pdo->prepare($sql);
            return $query->execute(['date' => $date, 'message' => $message, 'od' => $id_od, 'oo' => $id_oo, 'id' => $id]);
        } catch (PDOException $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function updateDecision($id, $decision) {
        try {
            $pdo = config::getConnexion();
            $query = $pdo->prepare("UPDATE echange SET statut = :decision WHERE id_echange = :id");
            return $query->execute(['decision' => $decision, 'id' => $id]);
        } catch (PDOException $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function supprimerEchange($id) {
        try {
            $pdo = config::getConnexion();
            $query = $pdo->prepare("DELETE FROM echange WHERE id_echange = :id");
            return $query->execute(['id' => $id]);
        } catch (PDOException $e) { die('Erreur: ' . $e->getMessage()); }
    }

    // --- Controller Actions ---

    public function handleRequest() {
        $id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : 1;
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        $error = ''; $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($action === 'add') {
                $id_od = $_POST['id_offre_demandeur'] ?? '';
                $id_oo = $_POST['id_offre_offreur'] ?? '';
                $msg = trim($_POST['message'] ?? '');
                $date_p = $_POST['date_proposee'] ?? date('Y-m-d');
                
                if (empty($id_od) || empty($id_oo)) {
                    $error = "Veuillez sélectionner les offres.";
                } else {
                    require_once __DIR__ . '/OffreController.php';
                    $offCtrl = new OffreC();
                    $offre_cible = $offCtrl->getOffreById($id_oo);
                    if ($offre_cible) {
                        $ech = new Echange($id_od, $id_oo, $id_user, $offre_cible['id_user'], $msg);
                        $ech->setDateDemande($date_p);
                        $this->ajouterEchange($ech);
                        $success = "Demande d'échange envoyée.";
                    }
                }
            } elseif ($action === 'add_don') {
                $ingredient = trim($_POST['ingredient'] ?? '');
                $quantite = trim($_POST['quantite'] ?? '');
                $ville = trim($_POST['ville'] ?? '');
                $msg = trim($_POST['message'] ?? '');

                if (empty($ingredient) || empty($quantite) || empty($ville)) {
                    $error = "Champs obligatoires manquants.";
                } else {
                    $offre = new OffreIngredient($id_user, $ingredient, 'Autre', $quantite, 'kg', $ville, 'disponible', 'don', $msg);
                    require_once __DIR__ . '/OffreController.php';
                    $offCtrl = new OffreC();
                    $offCtrl->ajouterOffre($offre);
                    $success = "Don publié !";
                    if (isset($_GET['ajax'])) { echo json_encode(['status' => 'success', 'message' => $success]); exit; }
                }
                if (isset($_GET['ajax']) && !empty($error)) { echo json_encode(['status' => 'error', 'message' => $error]); exit; }
            } elseif ($action === 'traiter') {
                $id = str_ireplace('ECH-', '', $_POST['id_echange'] ?? '');
                $decision = $_POST['decision'] ?? '';
                if ($id && $decision) {
                    $this->updateDecision($id, $decision);
                    $success = "Statut mis à jour.";
                    if (isset($_GET['ajax'])) { echo json_encode(['status' => 'success', 'message' => $success, 'new_statut' => $decision]); exit; }
                }
            } elseif ($action === 'update') {
                $id = str_ireplace('ECH-', '', $_POST['id_echange'] ?? '');
                $date = $_POST['date_proposee'] ?? '';
                $msg = $_POST['message'] ?? '';
                $id_od = $_POST['id_offre_demandeur'] ?? '';
                $id_oo = $_POST['id_offre_offreur'] ?? '';
                if ($id) {
                    $this->modifierEchange($id, $date, $msg, $id_od, $id_oo);
                    $success = "Échange mis à jour.";
                    if (isset($_GET['ajax'])) { echo json_encode(['status' => 'success', 'message' => $success]); exit; }
                }
            } elseif ($action === 'delete') {
                $id = str_ireplace('ECH-', '', $_POST['id_echange'] ?? '');
                if ($id) {
                    $this->supprimerEchange($id);
                    $success = "Échange supprimé.";
                    if (isset($_GET['ajax'])) { echo json_encode(['status' => 'success', 'message' => $success]); exit; }
                }
            }
        }

        // Views
        require_once __DIR__ . '/OffreController.php';
        $offCtrl = new OffreC();
        $offres = $offCtrl->getOffresByUser($id_user);
        $echanges = $this->afficheEchangesByUser($id_user);
        $mes_offres_actives = $offCtrl->getActiveOffresByUser($id_user);
        $offres_disponibles = $offCtrl->readAll();
        
        if (($_GET['source'] ?? '') === 'admin') {
            header('Location: OffreController.php?action=admin_list&success=' . urlencode($success));
        } else {
            header('Location: OffreController.php?action=front_list&success=' . urlencode($success));
        }
        exit;
    }
}

if (basename($_SERVER['PHP_SELF']) == 'EchangeController.php') {
    $controller = new EchangeC();
    $controller->handleRequest();
}
?>
