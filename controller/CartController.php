<?php
require_once __DIR__ . '/../model/CartModel.php';
require_once __DIR__ . '/../model/ProductModel.php';

class CartController
{
    private $pdo;
    // C'est ici qu'on prépare le contrôleur du panier.
    public function __construct($pdo)
    {
        // On garde la connexion à la base de données pour plus tard.
        $this->pdo = $pdo;
    }
    // Cette fonction permet d'ajouter un produit au panier.
    public function add()
    {
        // Si on a l'identifiant du produit dans l'adresse (URL) :
        if (isset($_GET['id'])) {
            // On l'ajoute au panier avec la quantité demandée (ou 1 par défaut).
            CartModel::addItem((int)$_GET['id'], isset($_GET['qty']) ? (int)$_GET['qty'] : 1);
        }
        // On redirige l'utilisateur vers la page du panier.
        header('Location: index.php?action=cart');
        exit();
    }
    // Cette fonction permet de mettre à jour les quantités des produits dans le panier.
    public function update()
    {
        // Si on a envoyé un formulaire avec de nouvelles quantités :
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantities'])) {
            // Pour chaque produit, on met à jour sa quantité.
            foreach ($_POST['quantities'] as $id => $qty) {
                CartModel::updateQuantity((int)$id, (int)$qty);
            }
        }
        // On retourne à la page du panier.
        header('Location: index.php?action=cart');
        exit();
    }
    // Cette fonction permet de retirer un produit du panier.
    public function remove()
    {
        // Si on a l'identifiant du produit à enlever :
        if (isset($_GET['id'])) {
            // On le retire du panier.
            CartModel::removeItem((int)$_GET['id']);
        }
        // On retourne à la page du panier.
        header('Location: index.php?action=cart');
        exit();
    }
    // Cette fonction permet d'afficher le contenu du panier.
    public function showCart()
    {
        // On récupère tous les articles qui sont dans le panier.
        $cartItems = CartModel::getCartItems($this->pdo);
        // On calcule le prix total du panier.
        $total = array_sum(array_column($cartItems, 'sous_total'));
        
        // On affiche les parties de la page du panier.
        require __DIR__ . '/../view/front/header.php';
        require __DIR__ . '/../view/front/panier.php';
        require __DIR__ . '/../view/front/footer.php';
    }
}
