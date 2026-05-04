<section class="container">
    <div class="panier">
        <h1>Mon panier</h1>
        <?php if (empty($cartItems)): ?>
            <p>Votre panier est vide.</p>
        <?php else: ?>
            <form method="post" action="index.php?action=update_cart">
                <table>... (votre code existant) ...</table>
                <div class="total">Total : <?= number_format($total,2) ?> DT</div>
                <button type="submit">Mettre à jour</button>
            </form>
            <a href="index.php?action=checkout" class="btn-primary">Passer commande</a>
        <?php endif; ?>
    </div>
</section>