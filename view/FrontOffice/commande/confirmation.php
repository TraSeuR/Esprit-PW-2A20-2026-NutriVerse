<section class="container" style="text-align: center; padding: 4rem 2rem;">
        <h2 style="color: #59b84d;">✅ Merci pour votre commande !</h2>
        <p>Numéro de commande : <strong>#<?= $orderId ?></strong></p>
        
        <?php if (isset($_GET['payment']) && $_GET['payment'] === 'success'): ?>
            <p>Votre paiement par carte a été <strong>validé avec succès</strong>.</p>
            <p>Le processus de livraison a été déclenché automatiquement.</p>
        <?php elseif (isset($_GET['payment']) && $_GET['payment'] === 'changed'): ?>
            <p style="color: #27ae60;">Le mode de paiement a été mis à jour avec succès : <strong>Paiement à la livraison</strong>.</p>
            <p>Votre commande est désormais confirmée. Un email de confirmation vous a été envoyé (simulation).</p>
        <?php else: ?>
            <p>Vous avez choisi le paiement à la livraison.</p>
            <p>Un email de confirmation vous a été envoyé (simulation).</p>
        <?php endif; ?>
        
        <a href="view/FrontOffice/produit/listProduit.php" class="btn-primary" style="margin-top: 20px; display: inline-block;">Continuer mes achats</a>
</section>
