<?php
require_once __DIR__ . '/../config/database.php';

class Echange {
    private $conn;
    private $table = "echange";

    // Propriétés
    private $id_echange;
    private $id_offre_demandeur;
    private $id_offre_offreur;
    private $id_demandeur;
    private $id_offreur;
    private $message;
    private $date_demande;
    private $statut;
    private $decision;
    private $note_demandeur;
    private $note_offreur;

    /**
     * Constructeur - Injection de la connexion PDO
     * @param PDO $db
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Destructeur
     */
    public function __destruct() {
        $this->conn = null;
    }

    // Setters pour les données
    public function setIdOffreDemandeur($val) { $this->id_offre_demandeur = $val; }
    public function setIdOffreOffreur($val) { $this->id_offre_offreur = $val; }
    public function setIdDemandeur($val) { $this->id_demandeur = $val; }
    public function setIdOffreur($val) { $this->id_offreur = $val; }
    public function setMessage($val) { $this->message = $val; }
    public function setStatut($val) { $this->statut = $val; }

    // --- Méthodes d'Instance ---

    public function getAllEchanges() {
        $sql = "SELECT e.*, 
                od.ingredient as ing_demande, od.quantite as qte_demande, od.unite_mesure as u_demande,
                oo.ingredient as ing_offreur, oo.quantite as qte_offreur, oo.unite_mesure as u_offreur
                FROM " . $this->table . " e
                LEFT JOIN offreingredient od ON e.id_offre_demandeur = od.id_offre
                LEFT JOIN offreingredient oo ON e.id_offre_offreur = oo.id_offre
                ORDER BY e.id_echange DESC";
        $req = $this->conn->query($sql);
        return $req->fetchAll();
    }

    public function countByStatut($statut) {
        $req = $this->conn->prepare("SELECT COUNT(*) as total FROM " . $this->table . " WHERE statut = :statut");
        $req->execute(['statut' => $statut]);
        $row = $req->fetch();
        return $row ? $row['total'] : 0;
    }

    public function getEchangesByUser($id_user) {
        $sql = "SELECT e.*, 
                od.ingredient as ing_donne, od.quantite as qte_donne, od.unite_mesure as u_donne,
                oo.ingredient as ing_recu, oo.quantite as qte_recu, oo.unite_mesure as u_recu
                FROM " . $this->table . " e
                LEFT JOIN offreingredient od ON e.id_offre_demandeur = od.id_offre
                LEFT JOIN offreingredient oo ON e.id_offre_offreur = oo.id_offre
                WHERE e.id_demandeur = :id_user OR e.id_offreur = :id_user 
                ORDER BY e.id_echange DESC";
                
        $req = $this->conn->prepare($sql);
        $req->execute(['id_user' => $id_user]);
        return $req->fetchAll();
    }

    public function getEchangeById($id_echange) {
        $req = $this->conn->prepare("SELECT * FROM " . $this->table . " WHERE id_echange = :id_echange");
        $req->execute(['id_echange' => $id_echange]);
        return $req->fetch();
    }

    public function addEchange() {
        $sql = "INSERT INTO " . $this->table . " (id_offre_demandeur, id_offre_offreur, id_demandeur, id_offreur, message, date_demande, statut) 
                VALUES (:id_offre_demandeur, :id_offre_offreur, :id_demandeur, :id_offreur, :message, NOW(), :statut)";
        $req = $this->conn->prepare($sql);
        try {
            $req->execute([
                'id_offre_demandeur' => $this->id_offre_demandeur,
                'id_offre_offreur' => $this->id_offre_offreur,
                'id_demandeur' => $this->id_demandeur,
                'id_offreur' => $this->id_offreur,
                'message' => $this->message,
                'statut' => $this->statut
            ]);
            return true;
        } catch (Exception $e) {
            throw new Exception("Erreur DB lors de l'ajout d'échange : " . $e->getMessage());
        }
    }

    public function updateDecision($id, $decision) {
        try {
            $query = $this->conn->prepare("UPDATE " . $this->table . " SET statut = :decision WHERE id_echange = :id");
            $query->execute([
                'decision' => $decision,
                'id' => $id
            ]);
            return $query->rowCount() > 0;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la mise à jour : " . $e->getMessage());
        }
    }

    public function delete($id_echange) {
        try {
            $req = $this->conn->prepare("DELETE FROM " . $this->table . " WHERE id_echange = :id");
            $req->execute(['id' => $id_echange]);
            return $req->rowCount() > 0;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la suppression : " . $e->getMessage());
        }
    }
    
    public function addNoteDemandeur($id_echange, $note) {
        $query = $this->conn->prepare("UPDATE " . $this->table . " SET note_demandeur = :note WHERE id_echange = :id_echange");
        $query->execute(['note' => $note, 'id_echange' => $id_echange]);
        return $query->rowCount() > 0;
    }
    
    public function addNoteOffreur($id_echange, $note) {
        $query = $this->conn->prepare("UPDATE " . $this->table . " SET note_offreur = :note WHERE id_echange = :id_echange");
        $query->execute(['note' => $note, 'id_echange' => $id_echange]);
        return $query->rowCount() > 0;
    }
}
?>
