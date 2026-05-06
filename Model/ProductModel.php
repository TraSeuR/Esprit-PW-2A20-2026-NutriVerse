<?php
class ProductModel
{
    private $pdo;
    // C'est ici qu'on prépare le modèle des produits.
    public function __construct($pdo)
    {
        // On enregistre la connexion à la base de données.
        $this->pdo = $pdo;
    }
    // Cette fonction va chercher tous les produits "actifs" dans la base de données.
    public function getAllProducts()
    {
        // On demande tous les produits triés par leur nom.
        $stmt = $this->pdo->query("SELECT * FROM produit WHERE statut = 'actif' ORDER BY nom");
        // On renvoie la liste complète.
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Cette fonction va chercher un seul produit précis grâce à son identifiant (ID).
    public function getProductById($id)
    {
        // On prépare la demande pour un ID précis.
        $stmt = $this->pdo->prepare("SELECT * FROM produit WHERE idproduit = ?");
        $stmt->execute([$id]);
        // On renvoie les informations de ce produit.
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
