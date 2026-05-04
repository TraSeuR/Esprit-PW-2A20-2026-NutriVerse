<?php
require_once __DIR__ . '/../model/ProductModel.php';

class ProductController
{
    private $productModel;
    // C'est ici qu'on prépare le contrôleur quand il commence à travailler.
    // Il a besoin d'une connexion à la base de données (pdo) pour fonctionner.
    public function __construct($pdo)
    {
        // On crée un "ProductModel" qui va s'occuper de parler à la base de données pour nous.
        $this->productModel = new ProductModel($pdo);
    }
    // Cette fonction sert à afficher tous les produits sur une page.
    public function listProducts()
    {
        // On demande au modèle de nous donner tous les produits.
        $products = $this->productModel->getAllProducts();
        
        // On affiche les différentes parties de la page (le haut, la liste des produits, et le bas).
        require __DIR__ . '/../view/front/header.php';
        require __DIR__ . '/../view/front/produits.php';
        require __DIR__ . '/../view/front/footer.php';
    }

    // Cette fonction sert à afficher la page d'accueil du site.
    public function frontPage()
    {
        // On affiche le haut de la page, le contenu principal, et le bas de la page.
        require __DIR__ . '/../view/front/header.php';
        require __DIR__ . '/../view/front/front.php'; // votre contenu HTML
        require __DIR__ . '/../view/front/footer.php';
    }
}
