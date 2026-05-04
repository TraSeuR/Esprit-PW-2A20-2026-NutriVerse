<?php
require_once __DIR__ . '/../model/OrderModel.php';
require_once __DIR__ . '/../model/CartModel.php';
require_once __DIR__ . '/../model/OrderDetailModel.php';

class OrderController
{
    private $pdo;

    // C'est ici qu'on prépare le contrôleur qui gère les commandes.
    public function __construct($pdo)
    {
        // On enregistre la connexion à la base de données.
        $this->pdo = $pdo;
    }

    // Cette fonction affiche le formulaire pour passer une commande (adresse, nom, etc.).
    public function showForm()
    {
        // On récupère les articles du panier et le total.
        $cartItems = CartModel::getCartItems($this->pdo);
        $total = array_sum(array_column($cartItems, 'sous_total'));
        
        // Si le panier est vide, on renvoie vers la liste des produits.
        if (empty($cartItems)) {
            header('Location: index.php?action=products');
            exit();
        }
        
        // On affiche les parties de la page de commande.
        require __DIR__ . '/../view/front/header.php';
        require __DIR__ . '/../view/front/commande.php';
        require __DIR__ . '/../view/front/footer.php';
    }

    // Cette fonction vérifie si un code promo est valide.
    public function validatePromo()
    {
        // On efface tout affichage précédent pour être sûr d'envoyer un JSON propre
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        $code = trim($_GET['code'] ?? '');

        if (empty($code)) {
            echo json_encode(['success' => false, 'message' => "Veuillez entrer un code promo"]);
            exit();
        }

        try {
            // On cherche le code dans la base de données.
            // Note: MySQL est généralement insensible à la casse par défaut.
            $stmt = $this->pdo->prepare("SELECT * FROM codes_promo WHERE code = ?");
            $stmt->execute([$code]);
            $promo = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($promo) {
                echo json_encode(['success' => true, 'discount' => (int)$promo['remise']]);
            } else {
                echo json_encode(['success' => false, 'message' => "Votre code promo n'existe pas"]);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => "Erreur base de données : " . $e->getMessage()]);
        }
        exit();
    }

    
    public function placeOrder()
    {
        // On vérifie que le formulaire a bien été envoyé.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=cart');
            exit();
        }
        
        // On récupère les articles du panier.
        $cartItems = CartModel::getCartItems($this->pdo);
        if (empty($cartItems)) {
            header('Location: index.php?action=cart');
            exit();
        }
        $total = array_sum(array_column($cartItems, 'sous_total'));
        
        // On vérifie le code promo pour appliquer la réduction réelle si besoin.
        $codePromo = trim($_POST['code_promo'] ?? '');
        if (!empty($codePromo)) {
            $stmtPromo = $this->pdo->prepare("SELECT * FROM codes_promo WHERE UPPER(code) = UPPER(?)");
            $stmtPromo->execute([$codePromo]);
            $promo = $stmtPromo->fetch(PDO::FETCH_ASSOC);
            if ($promo) {
                $remise = (int)$promo['remise'];
                $total = $total * (1 - $remise / 100); 
            }
        }

        $userId = $_SESSION['user_id'] ?? null;
        $methodePaiement = $_POST['paiement'] ?? 'livraison';
        $statutCommande = 'en attente'; // Statut initial

        // On enregistre la commande principale (date, total, adresse, etc.).
        $sql = "INSERT INTO commande (date_commande, statut_commande, montant_total, mode_paiement, adresse_livraison, nom_client, telephone_client, id_utilisateur, code_promo)
                VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$statutCommande, $total, $methodePaiement, $_POST['adresse'], $_POST['nom'], $_POST['telephone'], $userId, $codePromo]);
        $orderId = $this->pdo->lastInsertId();

        // On enregistre chaque produit de la commande un par un.
        foreach ($cartItems as $item) {
            $stmtLine = $this->pdo->prepare("INSERT INTO ligne_commande (id_commande, id_produit, quantite, prix_unitaire) VALUES (?, ?, ?, ?)");
            $stmtLine->execute([$orderId, $item['idproduit'], $item['quantite_panier'], $item['prix']]);
        }
        
        // On vide le panier car la commande est créée.
        CartModel::clearCart();

        // ======= PROCESSUS DE PAIEMENT =======
        if ($methodePaiement === 'carte') {
            $nomCarte = $_POST['nom_carte'] ?? '';
            $numCarte = $_POST['numero_carte'] ?? '';
            $dateExp = $_POST['date_expiration'] ?? '';

            // 1. Créer l'enregistrement de paiement 'en_attente'
            $insertPaiement = $this->pdo->prepare("INSERT INTO paiement (commande_id, nom_carte, numero_carte, date_expiration, montant, statut, methode, date_paiement) VALUES (?, ?, ?, ?, ?, 'en_attente', 'carte bancaire', NOW())");
            $insertPaiement->execute([$orderId, $nomCarte, $numCarte, $dateExp, $total]);

            // 2. Vérification du paiement dans la base de données
            $numCarteClean = str_replace(' ', '', $numCarte);
            
            $stmtCard = $this->pdo->prepare("SELECT * FROM carte_bancaire WHERE REPLACE(numero, ' ', '') = ? AND UPPER(nom) = UPPER(?) AND date_expiration = ?");
            $stmtCard->execute([$numCarteClean, $nomCarte, $dateExp]);
            $card = $stmtCard->fetch(PDO::FETCH_ASSOC);

            if ($card) {
                // Paiement réussi
                $this->pdo->prepare("UPDATE paiement SET statut = 'payé' WHERE commande_id = ?")->execute([$orderId]);
                
                // Mettre à jour la commande : 'confirmée' puis 'expédiée' (en livraison)
                $this->pdo->prepare("UPDATE commande SET statut_commande = 'expédiée' WHERE id_commande = ?")->execute([$orderId]);
                
                // Déclencher le processus de livraison
                $addr = $_POST['adresse'];
                $insertLivraison = $this->pdo->prepare(
                    "INSERT INTO livraison (date_livraison, statut_livraison, adresse_livraison, nom_livreur, id_commande) 
                     VALUES (NOW(), 'en cours de préparation', ?, 'Non assigné', ?)"
                );
                $insertLivraison->execute([$addr, $orderId]);

                header("Location: index.php?action=order_confirmation&id=$orderId&payment=success");
                exit();
            } else {
                // Paiement échoué
                $this->pdo->prepare("UPDATE paiement SET statut = 'refusé' WHERE commande_id = ?")->execute([$orderId]);
                // On met la commande en "paiement refusé" pour la bloquer sans l'annuler définitivement
                $this->pdo->prepare("UPDATE commande SET statut_commande = 'paiement refusé' WHERE id_commande = ?")->execute([$orderId]);

                header("Location: index.php?action=order_confirmation&id=$orderId&payment=failed");
                exit();
            }
        }

        // Si paiement à la livraison, redirection normale
        header("Location: index.php?action=order_confirmation&id=$orderId");
        exit();
    }
    
    // Cette fonction affiche la page de succès après une commande.
    public function confirmation()
    {
        // On récupère l'identifiant de la commande.
        $orderId = (int)$_GET['id'];
        
        // On va chercher les infos de la commande dans la base de données.
        $stmt = $this->pdo->prepare("SELECT * FROM commande WHERE id_commande = ?");
        $stmt->execute([$orderId]);
        $orderData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$orderData) die('Commande introuvable.');
        
        // On récupère aussi les produits achetés.
        $stmtLines = $this->pdo->prepare("SELECT l.*, p.nom FROM ligne_commande l JOIN produit p ON l.id_produit = p.idproduit WHERE l.id_commande = ?");
        $stmtLines->execute([$orderId]);
        $linesData = $stmtLines->fetchAll(PDO::FETCH_ASSOC);
        
        // On affiche la page de confirmation.
        require __DIR__ . '/../view/front/header.php';
        require __DIR__ . '/../view/front/confirmation.php';
        require __DIR__ . '/../view/front/footer.php';
    }

    // Cette fonction affiche la liste de toutes les commandes de l'utilisateur.
    public function myOrders()
    {
        // On simule un utilisateur connecté.
        $userId = $_SESSION['user_id'] ?? 1; 
        
        // On récupère toutes les commandes.
        $stmt = $this->pdo->prepare("SELECT * FROM commande ORDER BY date_commande DESC"); 
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // On affiche la page "Mes commandes".
        require __DIR__ . '/../view/front/header.php';
        require __DIR__ . '/../view/front/mes_commandes.php';
        require __DIR__ . '/../view/front/footer.php';
    }

    // Cette fonction affiche les détails d'une de mes commandes.
    public function orderDetail()
    {
        // On récupère l'identifiant de la commande.
        $orderId = (int)$_GET['id'];

        // On va chercher la commande dans la base de données.
        $stmt = $this->pdo->prepare("SELECT * FROM commande WHERE id_commande = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$order) {
            die('Commande introuvable.');
        }

        // On va chercher les produits de cette commande.
        $stmtLines = $this->pdo->prepare("SELECT l.*, p.nom FROM ligne_commande l JOIN produit p ON l.id_produit = p.idproduit WHERE l.id_commande = ?");
        $stmtLines->execute([$orderId]);
        $lines = $stmtLines->fetchAll(PDO::FETCH_ASSOC);

        // On regarde si une livraison est déjà prévue pour cette commande.
        $stmtLivraison = $this->pdo->prepare("SELECT * FROM livraison WHERE id_commande = ?");
        $stmtLivraison->execute([$orderId]);
        $livraison = $stmtLivraison->fetch(PDO::FETCH_ASSOC);

        // On affiche la page des détails.
        require __DIR__ . '/../view/front/header.php';
        require __DIR__ . '/../view/front/commande_detail.php';
        require __DIR__ . '/../view/front/footer.php';
    }

    // Cette fonction permet à l'utilisateur de changer son adresse tant que la commande n'est pas envoyée.
    public function updateAddress()
    {
        // Si on a envoyé une nouvelle adresse :
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = (int)$_POST['id_commande'];
            $newAddress = $_POST['adresse'];
            
            // On vérifie si la commande est encore "en attente".
            $checkStmt = $this->pdo->prepare("SELECT statut_commande FROM commande WHERE id_commande = ?");
            $checkStmt->execute([$orderId]);
            $status = $checkStmt->fetchColumn();
            
            // Si c'est le cas, on met à jour l'adresse.
            if ($status === 'en attente') {
                $stmt = $this->pdo->prepare("UPDATE commande SET adresse_livraison = ? WHERE id_commande = ?");
                $stmt->execute([$newAddress, $orderId]);
            }
            
            // On retourne sur la page de détail de la commande.
            header('Location: index.php?action=order_detail&id=' . $orderId);
            exit();
        }
    }

    // Cette fonction permet à l'utilisateur d'annuler sa commande.
    public function cancelOrder()
    {
        // Si on a cliqué sur le bouton d'annulation :
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = (int)$_POST['id_commande'];
            
            // On vérifie si la commande est toujours "en attente".
            $checkStmt = $this->pdo->prepare("SELECT statut_commande FROM commande WHERE id_commande = ?");
            $checkStmt->execute([$orderId]);
            $status = $checkStmt->fetchColumn();
            
            // Si oui, on change son état en "annulée".
            if ($status === 'en attente') {
                $stmt = $this->pdo->prepare("UPDATE commande SET statut_commande = 'annulée' WHERE id_commande = ?");
                $stmt->execute([$orderId]);
            }
            
            // On retourne à la liste des commandes.
            header('Location: index.php?action=my_orders');
            exit();
        }
    }

    // Cette fonction permet de changer le mode de paiement en cas d'échec de la carte bancaire
    public function changeToLivraison()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = (int)$_POST['id_commande'];
            
            // On vérifie que la commande est bien en "paiement refusé"
            $checkStmt = $this->pdo->prepare("SELECT statut_commande FROM commande WHERE id_commande = ?");
            $checkStmt->execute([$orderId]);
            $status = $checkStmt->fetchColumn();
            
            if ($status === 'paiement refusé') {
                // On repasse la commande "en attente" avec le mode "livraison"
                $stmt = $this->pdo->prepare("UPDATE commande SET statut_commande = 'en attente', mode_paiement = 'livraison' WHERE id_commande = ?");
                $stmt->execute([$orderId]);
            }
            
            // On redirige vers la confirmation avec succès (paiement à la livraison)
            header("Location: index.php?action=order_confirmation&id=$orderId&payment=changed");
            exit();
        }
    }
}
