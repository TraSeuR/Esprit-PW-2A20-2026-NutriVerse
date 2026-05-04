<?php
require_once __DIR__ . '/../model/OrderModel.php';
require_once __DIR__ . '/../model/OrderDetailModel.php';

class AdminController
{
    private $pdo;

    // C'est ici qu'on prépare le contrôleur de l'administration.
    public function __construct($pdo)
    {
        // On enregistre la connexion à la base de données.
        $this->pdo = $pdo;
    }
    
    // Cette fonction permet d'afficher la liste de toutes les commandes passées sur le site.
    public function listOrders()
    {
        // On demande à la base de données de nous donner toutes les commandes, de la plus récente à la plus ancienne.
        $stmt = $this->pdo->query("SELECT * FROM commande ORDER BY date_commande DESC");
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // On affiche la page qui liste les commandes pour l'administrateur.
        require __DIR__ . '/../view/back/commandes.php';
    }
    
    // Cette fonction permet de voir les détails d'une commande précise.
    public function viewOrder()
    {
        // On récupère l'identifiant (ID) de la commande depuis l'adresse de la page (URL).
        $id = (int)$_GET['id'];
        
        // On va chercher les informations de cette commande dans la base de données.
        $stmt = $this->pdo->prepare("SELECT * FROM commande WHERE id_commande = ?");
        $stmt->execute([$id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // On va aussi chercher tous les produits qui ont été achetés dans cette commande.
        $stmtLines = $this->pdo->prepare("SELECT l.*, p.nom FROM ligne_commande l JOIN produit p ON l.id_produit = p.idproduit WHERE l.id_commande = ?");
        $stmtLines->execute([$id]);
        $lines = $stmtLines->fetchAll(PDO::FETCH_ASSOC);
        
        // On affiche la page des détails de la commande.
        require __DIR__ . '/../view/back/commande_detail.php';
    }
    
    // Cette fonction permet de changer l'état d'une commande (ex: de "en attente" à "expédiée").
    public function editStatus()
    {
        // Si on a envoyé un formulaire avec de nouvelles infos :
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = (int)$_POST['id'];
            $status = $_POST['statut'];
            
            // On met à jour le statut de la commande dans la base de données.
            $stmt = $this->pdo->prepare("UPDATE commande SET statut_commande = ? WHERE id_commande = ?");
            $stmt->execute([$status, $orderId]);
            
            // Si la commande est marquée comme "expédiée", on crée automatiquement une livraison.
            if ($status === 'expédiée') {
                // On vérifie d'abord si une livraison n'existe pas déjà.
                $checkStmt = $this->pdo->prepare("SELECT id_livraison FROM livraison WHERE id_commande = ?");
                $checkStmt->execute([$orderId]);
                
                if ($checkStmt->rowCount() === 0) {
                    // On récupère l'adresse de livraison de la commande.
                    $addrStmt = $this->pdo->prepare("SELECT adresse_livraison FROM commande WHERE id_commande = ?");
                    $addrStmt->execute([$orderId]);
                    $addr = $addrStmt->fetchColumn();
                    
                    // On enregistre une nouvelle livraison dans la base de données.
                    $insertLivraison = $this->pdo->prepare(
                        "INSERT INTO livraison (date_livraison, statut_livraison, adresse_livraison, nom_livreur, id_commande) 
                         VALUES (NOW(), 'en cours de préparation', ?, 'Non assigné', ?)"
                    );
                    $insertLivraison->execute([$addr, $orderId]);
                }
            }
            
            // Une fois terminé, on renvoie l'administrateur vers la liste des commandes.
            header('Location: index.php?action=admin_orders');
            exit();
        }
    }
    
    // Cette fonction permet de supprimer une commande.
    public function deleteOrder()
    {
        // Si on a bien reçu l'identifiant de la commande à supprimer :
        if (isset($_GET['id'])) {
            $orderId = (int)$_GET['id'];
            
            // On supprime d'abord les lignes de produits liées à cette commande.
            $stmtLines = $this->pdo->prepare("DELETE FROM ligne_commande WHERE id_commande = ?");
            $stmtLines->execute([$orderId]);

            // Ensuite, on supprime la commande elle-même.
            $stmt = $this->pdo->prepare("DELETE FROM commande WHERE id_commande = ?");
            $stmt->execute([$orderId]);
        }
        // On retourne à la liste des commandes.
        header('Location: index.php?action=admin_orders');
        exit();
    }
}
