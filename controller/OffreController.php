<?php
if(session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/OffreIngredient.php';
require_once __DIR__ . '/../model/Echange.php';

class OffreC {

    public function getAllOffres() {
        try {
            $pdo = config::getConnexion();
            $req = $pdo->query("SELECT * FROM offreingredient ORDER BY id_offre DESC");
            return $req->fetchAll();
        } catch (PDOException $e) { return []; }
    }

    public function getOffresByUser($id_user) {
        try {
            $pdo = config::getConnexion();
            $req = $pdo->prepare("SELECT * FROM offreingredient WHERE id_user = :id_user AND etat != 'bloqué' ORDER BY id_offre DESC");
            $req->execute(['id_user' => $id_user]);
            return $req->fetchAll();
        } catch (PDOException $e) { return []; }
    }
    
    public function getActiveOffresByUser($id_user) {
        try {
            $pdo = config::getConnexion();
            $req = $pdo->prepare("SELECT * FROM offreingredient WHERE id_user = :id_user AND etat = 'disponible' AND type_offre = 'échange' AND etat != 'bloqué' ORDER BY id_offre DESC");
            $req->execute(['id_user' => $id_user]);
            return $req->fetchAll();
        } catch (PDOException $e) { return []; }
    }

    public function getOffreById($id_offre) {
        try {
            $pdo = config::getConnexion();
            $req = $pdo->prepare("SELECT * FROM offreingredient WHERE id_offre = :id_offre");
            $req->execute(['id_offre' => $id_offre]);
            return $req->fetch();
        } catch (PDOException $e) { return null; }
    }

    public function readAll($filtre_etat = null, $filtre_type = null) {
        try {
            $pdo = config::getConnexion();
            $sql = "SELECT * FROM offreingredient WHERE 1=1";
            $params = [];
            
            // Si pas de filtre (Vue utilisateur), on ne montre que le disponible
            if ($filtre_etat === null) {
                $sql .= " AND etat = 'disponible'";
            } elseif ($filtre_etat !== 'Toutes') {
                $sql .= " AND etat = :etat";
                $params['etat'] = $filtre_etat;
            }
            
            if ($filtre_type && $filtre_type !== 'Tous') {
                $sql .= " AND type_offre = :type";
                $params['type'] = $filtre_type;
            }
            $sql .= " ORDER BY id_offre DESC";
            $req = $pdo->prepare($sql);
            $req->execute($params);
            return $req->fetchAll();
        } catch (PDOException $e) { return []; }
    }
    
    public function countByEtat($etat) {
        try {
            $pdo = config::getConnexion();
            $req = $pdo->prepare("SELECT COUNT(*) as total FROM offreingredient WHERE etat = :etat");
            $req->execute(['etat' => $etat]);
            $res = $req->fetch();
            return $res['total'];
        } catch (PDOException $e) { return 0; }
    }

    public function countByType($type) {
        try {
            $pdo = config::getConnexion();
            $req = $pdo->prepare("SELECT COUNT(*) as total FROM offreingredient WHERE type_offre = :type");
            $req->execute(['type' => $type]);
            $res = $req->fetch();
            return $res['total'];
        } catch (PDOException $e) { return 0; }
    }

    /**
     * MÉTIER INNOVANT : Enregistrement d'une offre avec géolocalisation.
     * Cette méthode permet de sauvegarder non seulement les données classiques (ingrédient, quantité)
     * mais aussi les coordonnées GPS calculées par l'API de géocodage en front-end.
     */
    public function ajouterOffre($obj) {
        try {
            $pdo = config::getConnexion();
            $sql = "INSERT INTO offreingredient (id_user, ingredient, categorie, quantite, unite_mesure, localisation, date_publication, etat, type_offre, description, latitude, longitude) 
                    VALUES (:id_user, :ingredient, :categorie, :quantite, :unite_mesure, :localisation, CURDATE(), :etat, :type_offre, :description, :latitude, :longitude)";
            $req = $pdo->prepare($sql);
            return $req->execute([
                'id_user' => $obj->getIdUser(),
                'ingredient' => $obj->getIngredient(),
                'categorie' => $obj->getCategorie(),
                'quantite' => $obj->getQuantite(),
                'unite_mesure' => $obj->getUniteMesure(),
                'localisation' => $obj->getLocalisation(),
                'etat' => $obj->getEtat(),
                'type_offre' => $obj->getTypeOffre(),
                'description' => $obj->getDescription(),
                // Les coordonnées GPS sont stockées ici pour permettre la visualisation sur carte
                'latitude' => $obj->getLatitude(),
                'longitude' => $obj->getLongitude()
            ]);
        } catch (PDOException $e) { die('Erreur: ' . $e->getMessage()); }
    }

    /**
     * MISE À JOUR GÉOLOCALISÉE : 
     * Permet à l'utilisateur de modifier la quantité mais aussi de repositionner son offre
     * si son lieu de stockage ou de rencontre a changé.
     */
    public function modifierOffre($id_offre, $quantite, $etat, $localisation = null, $latitude = null, $longitude = null) {
        try {
            $pdo = config::getConnexion();
            // Construction dynamique de la requête de mise à jour
            $sql = "UPDATE offreingredient SET quantite = :quantite, etat = :etat";
            $params = ['quantite' => $quantite, 'etat' => $etat, 'id_offre' => $id_offre];
            
            // Si une nouvelle localisation est fournie, on met à jour les coordonnées GPS
            if ($localisation !== null) {
                $sql .= ", localisation = :loc, latitude = :lat, longitude = :lng";
                $params['loc'] = $localisation;
                $params['lat'] = $latitude;
                $params['lng'] = $longitude;
            }
            
            $sql .= " WHERE id_offre = :id_offre";
            $req = $pdo->prepare($sql);
            return $req->execute($params);
        } catch (PDOException $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function updateStatus($id_offre, $etat) {
        try {
            $pdo = config::getConnexion();
            $sql = "UPDATE offreingredient SET etat = :etat WHERE id_offre = :id_offre";
            $req = $pdo->prepare($sql);
            return $req->execute(['etat' => $etat, 'id_offre' => $id_offre]);
        } catch (PDOException $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function supprimerOffre($id_offre) {
        try {
            $pdo = config::getConnexion();
            $sql = "DELETE FROM offreingredient WHERE id_offre = :id_offre";
            $req = $pdo->prepare($sql);
            return $req->execute(['id_offre' => $id_offre]);
        } catch (PDOException $e) { die('Erreur: ' . $e->getMessage()); }
    }
    public function ajouterNote($id_echange, $note) {
        try {
            $pdo = config::getConnexion();
            $id = str_ireplace('ECH-', '', $id_echange);
            $id_user = $_SESSION['id_user'] ?? 1;

            // 1. RÉCUPÉRATION ET VÉRIFICATION (LE DÉCLENCHEUR)
            $stmt = $pdo->prepare("SELECT * FROM echange WHERE id_echange = :id");
            $stmt->execute(['id' => $id]);
            $ech = $stmt->fetch();

            if (!$ech) return "Échange introuvable.";
            if ($ech['statut'] !== 'accepte') return "Vous ne pouvez noter qu'un échange accepté.";

            // 2. LA CIBLE (QUI NOTE QUI ?)
            // Si je suis le demandeur, je donne la note_demandeur (note donnée PAR le demandeur)
            // Si je suis l'offreur, je donne la note_offreur (note donnée PAR l'offreur)
            $column = ($id_user == $ech['id_demandeur']) ? 'note_demandeur' : 'note_offreur';
            
            // On vérifie que l'utilisateur fait bien partie de l'échange
            if ($id_user != $ech['id_demandeur'] && $id_user != $ech['id_offreur']) {
                return "Vous ne faites pas partie de cet échange.";
            }

            $sql = "UPDATE echange SET $column = :note WHERE id_echange = :id";
            $req = $pdo->prepare($sql);
            $req->execute(['note' => $note, 'id' => $id]);
            return true;
        } catch (PDOException $e) { return "Erreur: " . $e->getMessage(); }
    }

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

    public function handleRequest() {
        $id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : 1;
        $action = isset($_GET['action']) ? $_GET['action'] : 'front_list';
        $error = $_GET['error'] ?? ''; 
        $success = $_GET['success'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($action === 'add') {
                $ingredient = trim($_POST['ingredient'] ?? '');
                $quantite = trim($_POST['quantite'] ?? '');
                $ville = trim($_POST['ville'] ?? '');
                
                if (empty($ingredient) || empty($quantite) || empty($ville)) {
                    $error = "Champs requis manquants.";
                } elseif (!preg_match("/^[a-zA-ZÀ-ÿ\s'-]+$/", $ingredient)) {
                    $error = "L'ingrédient ne doit contenir que des lettres.";
                } elseif (!is_numeric($quantite) || $quantite <= 0) {
                    $error = "La quantité doit être un nombre positif.";
                } else {
                    $lat = $_POST['latitude'] ?? null;
                    $lng = $_POST['longitude'] ?? null;
                    $offre = new OffreIngredient($id_user, $ingredient, 'Autre', $quantite, 'kg', $ville, 'disponible', 'échange', null, null, $lat, $lng);
                    $this->ajouterOffre($offre);
                    $success = "Offre ajoutée.";
                    header("Location: OffreController.php?action=front_list&success=" . urlencode($success));
                    exit;
                }
            } elseif ($action === 'update') {
                $id_offre = str_ireplace('OFF-', '', $_POST['id_offre'] ?? '');
                $nq = trim($_POST['nouvelle_quantite'] ?? '');
                $loc = $_POST['localisation'] ?? null;
                $lat = !empty($_POST['latitude']) ? $_POST['latitude'] : null;
                $lng = !empty($_POST['longitude']) ? $_POST['longitude'] : null;
                if ($id_offre && $nq) {
                    $this->modifierOffre($id_offre, $nq, 'disponible', $loc, $lat, $lng);
                    $success = "Offre mise à jour.";
                    if (isset($_GET['ajax'])) { echo json_encode(['status' => 'success', 'message' => $success]); exit; }
                    header("Location: OffreController.php?action=front_list&success=" . urlencode($success));
                    exit;
                }
            } elseif ($action === 'delete') {
                $id_offre = str_ireplace('OFF-', '', $_POST['id_offre'] ?? '');
                if ($id_offre) {
                    $this->supprimerOffre($id_offre);
                    $success = "Offre supprimée.";
                    if (isset($_GET['ajax'])) { echo json_encode(['status' => 'success', 'message' => $success]); exit; }
                    header("Location: OffreController.php?action=front_list&success=" . urlencode($success));
                    exit;
                }
            } elseif ($action === 'evaluer') {
                $id_echange = $_POST['id_echange'] ?? '';
                $note = (int)($_POST['note'] ?? 0);

                if (empty($id_echange) || $note < 1 || $note > 5) {
                    $error = "Veuillez fournir un ID d'échange et une note valide (1-5).";
                } else {
                    $res = $this->ajouterNote($id_echange, $note);
                    if ($res === true) {
                        $success = "Votre évaluation a été enregistrée.";
                    } else {
                        $error = $res;
                    }
                    header("Location: OffreController.php?action=front_list&success=" . urlencode($success) . "&error=" . urlencode($error));
                    exit;
                }
            } elseif ($action === 'update_etat') {
                $id = str_ireplace('OFF-', '', $_POST['id_offre'] ?? '');
                $etat = $_POST['nouvel_etat'] ?? 'disponible';
                if ($id) {
                    $this->updateStatus($id, $etat);
                    $success = "État de l'offre mis à jour ($etat).";
                    if (isset($_GET['ajax'])) { echo json_encode(['status' => 'success', 'message' => $success]); exit; }
                    header("Location: OffreController.php?action=front_list&success=" . urlencode($success));
                    exit;
                }
            } elseif ($action === 'block_offre') {
                $id = $_POST['id_offre'] ?? '';
                if ($id) { $this->updateStatus($id, 'bloqué'); $success = "Offre bloquée."; }
                header("Location: OffreController.php?action=admin_list&success=" . urlencode($success)); exit;
            } elseif ($action === 'admin_toggle_block') {
                $id = $_POST['id_offre'] ?? '';
                $etat = $_POST['nouvel_etat'] ?? 'bloqué';
                if ($id) {
                    $this->updateStatus($id, $etat);
                    $success = "Offre mise à jour (" . $etat . ").";
                    header("Location: OffreController.php?action=admin_list&success=" . urlencode($success));
                    exit;
                }
            } elseif ($action === 'admin_delete') {
                $id = $_POST['id_offre'] ?? '';
                if ($id) {
                    $this->supprimerOffre($id);
                    $success = "Offre supprimée définitivement.";
                    header("Location: OffreController.php?action=admin_list&success=" . urlencode($success));
                    exit;
                }
            }
        }

        if ($action === 'admin_list') {
            $f_etat = $_GET['filtre_etat'] ?? 'Toutes';
            $f_type = $_GET['filtre_type'] ?? 'Tous';
            $offres = $this->readAll($f_etat, $f_type);
            require_once __DIR__ . '/EchangeController.php';
            $echCtrl = new EchangeC();
            $echanges = $echCtrl->listeEchanges();
            
            $stats = [
                'actives' => $this->countByEtat('disponible'),
                'dons' => $this->countByType('don'),
                'bloquees' => $this->countByEtat('bloqué'),
                'attente' => $echCtrl->countByStatut('en_attente')
            ];
            require_once __DIR__ . '/../view/backoffice/dashboard_admin.php';
            return;
        }

        // View Rendering
        $offres = $this->getOffresByUser($id_user);
        require_once __DIR__ . '/EchangeController.php';
        $echCtrl = new EchangeC();
        $echanges = $echCtrl->afficheEchangesByUser($id_user); // //jointure : Récupération des ingrédients liés aux échanges
        $mes_offres_actives = $this->getActiveOffresByUser($id_user);
        $offres_disponibles = $this->readAll();
        
        require_once __DIR__ . '/../view/frontoffice/dashboard_user.php';
    }
}

if (basename($_SERVER['PHP_SELF']) == 'OffreController.php') {
    $controller = new OffreC();
    $controller->handleRequest();
}
?>
