<section class="container">
    <h1>Nos produits</h1>
    <div class="category-grid">
        <?php foreach ($products as $product): ?>
            <div class="category-card">
                <img src="view/FrontOffice/images/<?= htmlspecialchars($product['categorie']) ?>.jpg">
                <h3><?= htmlspecialchars($product['nom']) ?></h3>
                <p><?= number_format($product['prix'], 2) ?> DT</p>
                <a href="shop.php?action=add_to_cart&id=<?= $product['idproduit'] ?>" class="btn-primary">Ajouter au panier</a>
            </div>
        <?php endforeach; ?>
    </div>
</section>
