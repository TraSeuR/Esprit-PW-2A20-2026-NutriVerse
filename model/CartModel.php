<?php
class CartModel
{
    // Cette fonction permet de récupérer le panier actuel qui est stocké dans la "session" (la mémoire temporaire du navigateur).
    public static function getCart()
    {
        // Si le panier n'existe pas encore, on en crée un vide.
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        // On renvoie le panier.
        return $_SESSION['cart'];
    }

    // Cette fonction permet d'ajouter un produit dans le panier.
    public static function addItem($productId, $quantity = 1)
    {
        // On récupère le panier actuel.
        $cart = self::getCart();
        // Si le produit est déjà dans le panier, on augmente sa quantité.
        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            // Sinon, on ajoute le nouveau produit avec sa quantité.
            $cart[$productId] = $quantity;
        }
        // On enregistre le panier mis à jour.
        $_SESSION['cart'] = $cart;
    }

    // Cette fonction permet de changer la quantité d'un produit déjà présent dans le panier.
    public static function updateQuantity($productId, $quantity)
    {
        // On récupère le panier actuel.
        $cart = self::getCart();
        // Si la quantité est de 0 ou moins, on retire carrément le produit du panier.
        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            // Sinon, on met la nouvelle quantité.
            $cart[$productId] = $quantity;
        }
        // On enregistre le panier mis à jour.
        $_SESSION['cart'] = $cart;
    }

    // Cette fonction permet de supprimer complètement un produit du panier.
    public static function removeItem($productId)
    {
        // On récupère le panier actuel.
        $cart = self::getCart();
        // On enlève le produit correspondant à l'ID.
        unset($cart[$productId]);
        // On enregistre le panier mis à jour.
        $_SESSION['cart'] = $cart;
    }

    // Cette fonction permet de vider tout le panier d'un seul coup.
    public static function clearCart()
    {
        // On remet le panier à zéro (liste vide).
        $_SESSION['cart'] = [];
    }

    // Cette fonction permet de récupérer les détails (nom, prix, etc.) de chaque produit du panier.
    public static function getCartItems($pdo)
    {
        // On récupère les IDs des produits qui sont dans le panier.
        $cart = self::getCart();
        if (empty($cart)) return [];
        $ids = implode(',', array_keys($cart));
        
        // On demande à la base de données les infos de tous ces produits.
        $stmt = $pdo->query("SELECT * FROM produit WHERE idproduit IN ($ids)");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Pour chaque produit, on ajoute sa quantité et on calcule son prix total (sous-total).
        foreach ($products as &$product) {
            $product['quantite_panier'] = $cart[$product['idproduit']];
            $product['sous_total'] = $product['prix'] * $product['quantite_panier'];
        }
        // On renvoie la liste détaillée.
        return $products;
    }
}
