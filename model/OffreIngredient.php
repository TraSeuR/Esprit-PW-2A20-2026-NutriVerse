<?php
require_once __DIR__ . '/../config/database.php';

class OffreIngredient {
    private $conn;
    private $table = "offreingredient";

    // Propriétés
    private $id_offre;
    private $id_user;
    private $ingredient;
    private $categorie;
    private $quantite;
    private $unite_mesure;
    private $localisation;
    private $date_publication;
    private $etat;
    private $type_offre;
    private $description;

    /**
     * Constructeur - Reçoit uniquement la connexion PDO
     * @param PDO $db
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Destructeur - Ferme la connexion
     */
    public function __destruct() {
        $this->conn = null;
    }

    // --- Getters & Setters ---
    public function setIdUser($val) { $this->id_user = $val; }
    public function setIngredient($val) { $this->ingredient = $val; }
    public function setCategorie($val) { $this->categorie = $val; }
    public function setQuantite($val) { $this->quantite = $val; }
    public function setUniteMesure($val) { $this->unite_mesure = $val; }
    public function setLocalisation($val) { $this->localisation = $val; }
    public function setEtat($val) { $this->etat = $val; }
    public function setTypeOffre($val) { $this->type_offre = $val; }
    public function setDescription($val) { $this->description = $val; }

    // --- Méthodes d'Instance ---

    public function getAllOffres() {
        $req = $this->conn->query("SELECT * FROM " . $this->table . " ORDER BY id_offre DESC");
        return $req->fetchAll();
    }

    public function getOffresByUser($id_user) {
        $req = $this->conn->prepare("SELECT * FROM " . $this->table . " WHERE id_user = :id_user ORDER BY id_offre DESC");
        $req->execute(['id_user' => $id_user]);
        return $req->fetchAll();
    }
    
    public function getActiveOffresByUser($id_user) {
        $req = $this->conn->prepare("SELECT * FROM " . $this->table . " WHERE id_user = :id_user AND etat = 'disponible' AND type_offre = 'échange' ORDER BY id_offre DESC");
        $req->execute(['id_user' => $id_user]);
        return $req->fetchAll();
    }

    public function getOffreById($id_offre) {
        $req = $this->conn->prepare("SELECT * FROM " . $this->table . " WHERE id_offre = :id_offre");
        $req->execute(['id_offre' => $id_offre]);
        return $req->fetch();
    }

    public function readAll($filtre_etat = null, $filtre_type = null) {
        $sql = "SELECT * FROM " . $this->table . " WHERE 1=1";
        $params = [];
        
        if ($filtre_etat && $filtre_etat !== 'Toutes') {
            $sql .= " AND etat = :etat";
            $params['etat'] = $filtre_etat;
        }
        if ($filtre_type && $filtre_type !== 'Tous') {
            $sql .= " AND type_offre = :type";
            $params['type'] = $filtre_type;
        }
        
        $sql .= " ORDER BY id_offre DESC";
        $req = $this->conn->prepare($sql);
        $req->execute($params);
        return $req->fetchAll();
    }
    
    public function countByEtat($etat) {
        $req = $this->conn->prepare("SELECT COUNT(*) as total FROM " . $this->table . " WHERE etat = :etat");
        $req->execute(['etat' => $etat]);
        $row = $req->fetch();
        return $row ? $row['total'] : 0;
    }

    public function countByType($type) {
        $req = $this->conn->prepare("SELECT COUNT(*) as total FROM " . $this->table . " WHERE type_offre = :type");
        $req->execute(['type' => $type]);
        $row = $req->fetch();
        return $row ? $row['total'] : 0;
    }

    public function addOffre() {
        $sql = "INSERT INTO " . $this->table . " (id_user, ingredient, categorie, quantite, unite_mesure, localisation, date_publication, etat, type_offre, description) 
                VALUES (:id_user, :ingredient, :categorie, :quantite, :unite_mesure, :localisation, CURDATE(), :etat, :type_offre, :description)";
        $req = $this->conn->prepare($sql);
        try {
            $req->execute([
                'id_user' => $this->id_user,
                'ingredient' => $this->ingredient,
                'categorie' => $this->categorie,
                'quantite' => $this->quantite,
                'unite_mesure' => $this->unite_mesure,
                'localisation' => $this->localisation,
                'etat' => $this->etat,
                'type_offre' => $this->type_offre,
                'description' => $this->description
            ]);
            return true;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de l'ajout de l'offre : " . $e->getMessage());
        }
    }

    public function updateOffre($id_offre, $quantite, $etat) {
        $sql = "UPDATE " . $this->table . " SET quantite = :quantite, etat = :etat WHERE id_offre = :id_offre";
        $req = $this->conn->prepare($sql);
        try {
            $req->execute([
                'quantite' => $quantite,
                'etat' => $etat,
                'id_offre' => $id_offre
            ]);
            return $req->rowCount() > 0;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la mise à jour de l'offre : " . $e->getMessage());
        }
    }

    public function updateStatus($id_offre, $etat) {
        $sql = "UPDATE " . $this->table . " SET etat = :etat WHERE id_offre = :id_offre";
        $req = $this->conn->prepare($sql);
        try {
            $req->execute(['etat' => $etat, 'id_offre' => $id_offre]);
            return $req->rowCount() > 0;
        } catch (Exception $e) {
            throw new Exception("Erreur statut : " . $e->getMessage());
        }
    }

    public function deleteOffre($id_offre) {
        $sql = "DELETE FROM " . $this->table . " WHERE id_offre = :id_offre";
        $req = $this->conn->prepare($sql);
        try {
            $req->execute(['id_offre' => $id_offre]);
            return $req->rowCount() > 0;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la suppression de l'offre : " . $e->getMessage());
        }
    }
}
?>
