<?php
require_once __DIR__ . '/../Model/OrderModel.php';
require_once __DIR__ . '/../Model/OrderDetailModel.php';
require_once __DIR__ . '/../config/config.php';

class AdminController
{
    private $db;

    // C'est ici qu'on prépare le contrôleur de l'administration.
    public function __construct()
    {
        // On enregistre la connexion à la base de données.
        $this->db = config::getConnexion();
    }

    // Cette fonction affiche le tableau de bord principal du back-office.
    public function dashboard()
    {
        // 1. Statistiques Globales
        $totalOrders = $this->db->query("SELECT COUNT(*) FROM commande")->fetchColumn();
        $totalRevenue = $this->db->query("SELECT SUM(montant_total) FROM commande")->fetchColumn() ?: 0;
        $totalProducts = $this->db->query("SELECT COUNT(*) FROM produit")->fetchColumn();
        $totalUsers = $this->db->query("SELECT COUNT(*) FROM user")->fetchColumn();
        $totalRecettes = $this->db->query("SELECT COUNT(*) FROM recette")->fetchColumn();

        // 2. Commandes Récentes (les 5 dernières)
        $stmtRecent = $this->db->query("SELECT * FROM commande ORDER BY date_commande DESC LIMIT 5");
        $recentOrders = $stmtRecent->fetchAll(PDO::FETCH_ASSOC);

        // 3. Nouveaux Utilisateurs (les 5 derniers)
        $stmtUsers = $this->db->query("SELECT * FROM user ORDER BY id_user DESC LIMIT 5");
        $recentUsers = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

        // 4. Données pour le Graphique (Commandes par mois)
        $stmtChart = $this->db->query("SELECT MONTH(date_commande) as mois, COUNT(*) as nb FROM commande WHERE YEAR(date_commande) = YEAR(CURDATE()) GROUP BY MONTH(date_commande)");
        $chartData = array_fill(1, 12, 0);
        while ($row = $stmtChart->fetch(PDO::FETCH_ASSOC)) {
            $chartData[(int)$row['mois']] = (int)$row['nb'];
        }
        $chartJson = json_encode(array_values($chartData));

        require __DIR__ . '/../view/BackOffice/back.php';
    }
    
    // Cette fonction permet d'afficher la liste de toutes les commandes passées sur le site.
    public function listOrders()
    {
        // On demande à la base de données de nous donner toutes les commandes, de la plus récente à la plus ancienne.
        $stmt = $this->db->query("SELECT * FROM commande ORDER BY date_commande DESC");
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // On affiche la page qui liste les commandes pour l'administrateur.
        require __DIR__ . '/../view/BackOffice/commande/commandes.php';
    }
    
    // Cette fonction permet de voir les détails d'une commande précise.
    public function viewOrder()
    {
        // On récupère l'identifiant (ID) de la commande depuis l'adresse de la page (URL).
        $id = (int)$_GET['id'];
        
        // On va chercher les informations de cette commande dans la base de données.
        $stmt = $this->db->prepare("SELECT * FROM commande WHERE id_commande = ?");
        $stmt->execute([$id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // On va aussi chercher tous les produits qui ont été achetés dans cette commande.
        $stmtLines = $this->db->prepare("SELECT l.*, p.nom FROM ligne_commande l JOIN produit p ON l.id_produit = p.idproduit WHERE l.id_commande = ?");
        $stmtLines->execute([$id]);
        $lines = $stmtLines->fetchAll(PDO::FETCH_ASSOC);
        
        // On affiche la page des détails de la commande.
        require __DIR__ . '/../view/BackOffice/commande/commande_detail.php';
    }
    
    // Cette fonction permet de changer l'état d'une commande (ex: de "en attente" à "expédiée").
    public function editStatus()
    {
        // Si on a envoyé un formulaire avec de nouvelles infos :
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = (int)$_POST['id'];
            $status = $_POST['statut'];
            
            // On met à jour le statut de la commande dans la base de données.
            $stmt = $this->db->prepare("UPDATE commande SET statut_commande = ? WHERE id_commande = ?");
            $stmt->execute([$status, $orderId]);
            
            // Si la commande est marquée comme "expédiée", on crée automatiquement une livraison.
            if ($status === 'expédiée') {
                // On vérifie d'abord si une livraison n'existe pas déjà.
                $checkStmt = $this->db->prepare("SELECT id_livraison FROM livraison WHERE id_commande = ?");
                $checkStmt->execute([$orderId]);
                
                if ($checkStmt->rowCount() === 0) {
                    // On récupère l'adresse de livraison de la commande.
                    $addrStmt = $this->db->prepare("SELECT adresse_livraison FROM commande WHERE id_commande = ?");
                    $addrStmt->execute([$orderId]);
                    $addr = $addrStmt->fetchColumn();
                    
                    // On enregistre une nouvelle livraison dans la base de données.
                    $insertLivraison = $this->db->prepare(
                        "INSERT INTO livraison (date_livraison, statut_livraison, adresse_livraison, nom_livreur, id_commande) 
                         VALUES (NOW(), 'en cours de préparation', ?, 'Non assigné', ?)"
                    );
                    $insertLivraison->execute([$addr, $orderId]);
                }
            }
            
            // Une fois terminé, on renvoie l'administrateur vers la liste des commandes.
            header('Location: shop.php?action=admin_orders');
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
            $stmtLines = $this->db->prepare("DELETE FROM ligne_commande WHERE id_commande = ?");
            $stmtLines->execute([$orderId]);

            // Ensuite, on supprime la commande elle-même.
            $stmt = $this->db->prepare("DELETE FROM commande WHERE id_commande = ?");
            $stmt->execute([$orderId]);
        }
        // On retourne à la liste des commandes.
        header('Location: shop.php?action=admin_orders');
        exit();
    }
}
