<?php
require_once __DIR__.'/../../../controller/ProduitController.php';

$produitController = new ProduitController();

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? 'asc';

// Get filtered active products
$produits = $produitController->getProduitsActifs($search, $category, $sort);

if (empty($produits)) {
    echo '<div style="grid-column: 1 / -1; text-align: center; padding: 40px;">Aucun produit disponible pour le moment.</div>';
    exit;
}

foreach($produits as $prod): 
?>
<article class="plu-product-card <?= ($prod['quantite_stock'] == 0) ? 'plu-product-card-out' : '' ?>">
    <?php 
    $imgGlob = glob(__DIR__ . '/../../back/images/produit_' . $prod['idproduit'] . '.*');
    if($imgGlob) {
        $imgPath = '../../back/images/' . basename($imgGlob[0]);
        echo '<div class="plu-product-visual"><img src="'.htmlspecialchars($imgPath).'" alt="'.htmlspecialchars($prod['nom']).'" /></div>';
    } else {
        echo '<div class="plu-product-visual" aria-hidden="true" style="font-size: 40px; color:#aaa;">🛒</div>';
    }
    ?>
    <div class="plu-product-body">
      <h3><?= htmlspecialchars($prod['nom']) ?></h3>
      <p class="plu-price">
        <?php if($prod['prix'] < ($prod['prix_original'] ?? $prod['prix'])): ?>
            <span style="text-decoration: line-through; color: #888; font-size: 0.8em;"><?= htmlspecialchars($prod['prix_original']) ?> TND</span>
            <span style="color: #e74c3c; font-weight: bold;"><?= htmlspecialchars($prod['prix']) ?> TND</span>
            <span class="plu-tag plu-tag-waste" style="background: #e74c3c; color: white; display: inline-block; margin-left: 5px;">🔥 PROMO</span>
        <?php else: ?>
            <?= htmlspecialchars($prod['prix']) ?> TND
        <?php endif; ?>
      </p>
      <div class="plu-meta-row">
        <?php if($prod['quantite_stock'] == 0): ?>
            <span class="plu-tag plu-tag-stock-out">Rupture de stock</span>
        <?php elseif($prod['quantite_stock'] <= $prod['seuil_alerte']): ?>
            <span class="plu-tag plu-tag-stock-low"><?= htmlspecialchars($prod['quantite_stock']) ?> restants</span>
        <?php else: ?>
            <span class="plu-tag plu-tag-stock-ok"><?= htmlspecialchars($prod['quantite_stock']) ?> en stock</span>
        <?php endif; ?>
        
        <?php if($prod['date_expiration']): ?>
            <?php 
            $days = (strtotime($prod['date_expiration']) - time()) / (60 * 60 * 24);
            if($days > 0 && $days <= 5): 
            ?>
            <span class="plu-tag plu-tag-waste">À écouler</span>
            <?php endif; ?>
        <?php endif; ?>
      </div>
      
      <?php if($prod['date_expiration']): ?>
      <p class="plu-expire-line">À consommer avant le <strong><?= date("d/m/Y", strtotime($prod['date_expiration'])) ?></strong></p>
      <?php endif; ?>

      <div class="plu-card-actions">
        <?php if($prod['quantite_stock'] > 0): ?>
            <a href="#" onclick="addToCart(<?= $prod['idproduit'] ?>, '<?= htmlspecialchars($prod['nom'], ENT_QUOTES) ?>', <?= $prod['prix'] ?>); return false;" class="btn-primary">Ajouter au panier</a>
        <?php else: ?>
            <span class="btn-primary plu-btn-muted" aria-disabled="true">Indisponible</span>
        <?php endif; ?>
      </div>
    </div>
</article>
<?php endforeach; ?>
