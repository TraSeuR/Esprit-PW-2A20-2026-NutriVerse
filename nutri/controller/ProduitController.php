<?php
require_once __DIR__.'/../config/config.php';
require_once __DIR__.'/../model/Produit.php';
require_once __DIR__.'/NotificationController.php';

class ProduitController {
    public function getProduits(): array {
        $db = config::getConnexion();
        try {
            $query = $db->query("SELECT * FROM produit");
            return $query->fetchAll();
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    public function getProduitsActifs(string $search = '', string $category = '', string $sort = 'asc'): array {
        $db = config::getConnexion();
        try {
            $sql = "SELECT * FROM produit WHERE statut='actif'";
            
            if (!empty($category)) {
                $sql .= " AND categorie = :category";
            }

            if (!empty($search)) {
                $sql .= " AND (nom LIKE :search OR categorie LIKE :search)";
            }
            
            if ($sort === 'desc') {
                $sql .= " ORDER BY prix DESC";
            } else {
                $sql .= " ORDER BY prix ASC";
            }
            
            $query = $db->prepare($sql);
            
            if (!empty($category)) {
                $query->bindValue(':category', $category);
            }

            if (!empty($search)) {
                $query->bindValue(':search', '%' . $search . '%');
            }
            
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    public function getProduitById(int $id): ?array {
        $db = config::getConnexion();
        try {
            $query = $db->prepare("SELECT * FROM produit WHERE idproduit = :id");
            $query->execute(['id' => $id]);
            $res = $query->fetch();
            return $res ? $res : null;
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    public function addProduit(Produit $produit): string {
        $db = config::getConnexion();
        try {
            $query = $db->prepare(
                "INSERT INTO produit (nom, prix, prix_original, quantite_stock, seuil_alerte, categorie, date_expiration, statut) 
                VALUES (:nom, :prix, :prix_orig, :quantite, :seuil, :cat, :date_exp, :statut)"
            );
            $query->execute([
                'nom' => $produit->getNom(),
                'prix' => $produit->getPrix(),
                'prix_orig' => $produit->getPrixOriginal() ?? $produit->getPrix(),
                'quantite' => $produit->getQuantiteStock(),
                'seuil' => $produit->getSeuilAlerte(),
                'cat' => $produit->getCategorie(),
                'date_exp' => $produit->getDateExpiration() ? $produit->getDateExpiration() : null,
                'statut' => $produit->getStatut()
            ]);
            return $db->lastInsertId();
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    public function updateProduit(Produit $produit, int $id): void {
        $db = config::getConnexion();
        try {
            // Check for manual price change before update
            $oldProduit = $this->getProduitById($id);
            if ($oldProduit && $oldProduit['prix'] != $produit->getPrix()) {
                $notifController = new NotificationController();
                $msg = "Changement de prix pour " . $produit->getNom() . " : " . $oldProduit['prix'] . " -> " . $produit->getPrix() . " TND (Manuel)";
                $notifController->addNotification(new Notification($msg, 'price_drop', $id));
            }

            $query = $db->prepare(
                "UPDATE produit SET 
                    nom = :nom, 
                    prix = :prix, 
                    prix_original = :prix_orig,
                    quantite_stock = :quantite, 
                    seuil_alerte = :seuil, 
                    categorie = :cat, 
                    date_expiration = :date_exp, 
                    statut = :statut 
                WHERE idproduit = :id"
            );
            $query->execute([
                'nom' => $produit->getNom(),
                'prix' => $produit->getPrix(),
                'prix_orig' => $produit->getPrixOriginal(),
                'quantite' => $produit->getQuantiteStock(),
                'seuil' => $produit->getSeuilAlerte(),
                'cat' => $produit->getCategorie(),
                'date_exp' => $produit->getDateExpiration() ? $produit->getDateExpiration() : null,
                'statut' => $produit->getStatut(),
                'id' => $id
            ]);
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    public function deleteProduit(int $id): void {
        $db = config::getConnexion();
        try {
            $query = $db->prepare("DELETE FROM produit WHERE idproduit = :id");
            $query->execute(['id' => $id]);
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    public function runMonitoring(): void {
        $produits = $this->getProduits();
        $notifController = new NotificationController();
        $db = config::getConnexion();
        require_once __DIR__.'/../service/EmailService.php';
        
        // Use midnight today for calendar day comparison
        $today = new DateTime('today');

        foreach ($produits as $prod) {
            // Fix prix_original if it is missing (NULL or 0)
            if (empty($prod['prix_original']) || $prod['prix_original'] == 0) {
                $db->prepare("UPDATE produit SET prix_original = :p WHERE idproduit = :id")
                   ->execute(['p' => $prod['prix'], 'id' => $prod['idproduit']]);
                $prod['prix_original'] = $prod['prix'];
            }

            // 1. Stock Monitoring
            if ($prod['quantite_stock'] <= $prod['seuil_alerte']) {
                $msg = "⚠️ Stock faible pour " . $prod['nom'] . " (" . $prod['quantite_stock'] . " restant)";
                $notifController->addNotification(new Notification($msg, 'stock_low', $prod['idproduit']));
                
                // Email Alert
                EmailService::sendAlert("manager@nutriverse.tn", "ALERTE STOCK: " . $prod['nom'], $msg);
            }

            // 2. Expiration & Price Reduction Monitoring
            if (!empty($prod['date_expiration'])) {
                $expDate = new DateTime($prod['date_expiration']);
                $interval = $today->diff($expDate);
                $daysLeft = $interval->invert ? -$interval->days : $interval->days;

                $reduction = 0;
                if ($daysLeft <= 0) $reduction = 0.60;
                elseif ($daysLeft == 1) $reduction = 0.40;
                elseif ($daysLeft <= 3) $reduction = 0.30;
                elseif ($daysLeft <= 7) $reduction = 0.20;
                elseif ($daysLeft <= 14) $reduction = 0.10;

                $targetPrix = $prod['prix_original'];
                if ($reduction > 0) {
                    $targetPrix = round($prod['prix_original'] * (1 - $reduction), 2);
                }

                if ($targetPrix != $prod['prix']) {
                    $db->prepare("UPDATE produit SET prix = :prix WHERE idproduit = :id")
                       ->execute(['prix' => $targetPrix, 'id' => $prod['idproduit']]);

                    if ($reduction > 0) {
                        $msg = "📉 Promo Expire: " . $prod['nom'] . " est à " . $targetPrix . " TND (-" . ($reduction * 100) . "%)";
                        $notifController->addNotification(new Notification($msg, 'price_drop', $prod['idproduit']));
                        
                        // Email Alert
                        EmailService::sendAlert("user@nutriverse.tn", "PROMO: " . $prod['nom'], $msg);
                    } else {
                        $msg = "🔄 Prix restauré pour " . $prod['nom'] . " (Expiration éloignée)";
                        $notifController->addNotification(new Notification($msg, 'price_drop', $prod['idproduit']));
                    }
                }
            }
        }
    }
}
?>
