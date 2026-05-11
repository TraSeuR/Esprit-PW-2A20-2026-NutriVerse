<?php
require_once __DIR__.'/../config/config.php';
require_once __DIR__.'/../model/Movement.php';

class MovementController {
    public function getMovements(): array {
        $db = config::getConnexion();
        try {
            // Join with produit to get the product name
            $sql = "SELECT m.*, p.nom as nom_produit 
                    FROM movement m 
                    LEFT JOIN produit p ON m.id_produit = p.idproduit";
            $query = $db->query($sql);
            return $query->fetchAll();
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    public function getMovementById(int $id): ?array {
        $db = config::getConnexion();
        try {
            $query = $db->prepare("SELECT * FROM movement WHERE id = :id");
            $query->execute(['id' => $id]);
            $res = $query->fetch();
            return $res ? $res : null;
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    public function addMovement(Movement $movement): void {
        $db = config::getConnexion();
        try {
            $query = $db->prepare(
                "INSERT INTO movement (id_produit, titre, description, type_mouvement, quantite) 
                VALUES (:id_pro, :titre, :desc, :type_mov, :quantite)"
            );
            $query->execute([
                'id_pro' => $movement->getIdProduit() ? $movement->getIdProduit() : null,
                'titre' => $movement->getTitre(),
                'desc' => $movement->getDescription(),
                'type_mov' => $movement->getTypeMouvement(),
                'quantite' => $movement->getQuantite()
            ]);
            
            // Auto update stock if it's a stock movement
            if ($movement->getIdProduit() && in_array($movement->getTypeMouvement(), ['achat', 'ajout_stock'])) {
                $q = $db->prepare("UPDATE produit SET quantite_stock = quantite_stock + :qte WHERE idproduit = :id");
                $q->execute(['qte' => $movement->getQuantite(), 'id' => $movement->getIdProduit()]);
            } else if ($movement->getIdProduit() && $movement->getTypeMouvement() == 'vente') {
                $q = $db->prepare("UPDATE produit SET quantite_stock = quantite_stock - :qte WHERE idproduit = :id");
                $q->execute(['qte' => $movement->getQuantite(), 'id' => $movement->getIdProduit()]);
            }
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    public function updateMovement(Movement $movement, int $id): void {
        $db = config::getConnexion();
        try {
            $query = $db->prepare(
                "UPDATE movement SET 
                    id_produit = :id_pro, 
                    titre = :titre, 
                    description = :desc, 
                    type_mouvement = :type_mov, 
                    quantite = :quantite 
                WHERE id = :id"
            );
            $query->execute([
                'id_pro' => $movement->getIdProduit() ? $movement->getIdProduit() : null,
                'titre' => $movement->getTitre(),
                'desc' => $movement->getDescription(),
                'type_mov' => $movement->getTypeMouvement(),
                'quantite' => $movement->getQuantite(),
                'id' => $id
            ]);
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    public function deleteMovement(int $id): void {
        $db = config::getConnexion();
        try {
            $query = $db->prepare("DELETE FROM movement WHERE id = :id");
            $query->execute(['id' => $id]);
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }
}
?>
