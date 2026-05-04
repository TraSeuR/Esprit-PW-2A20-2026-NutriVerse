<section class="container" style="text-align: center; padding: 4rem 2rem;">
    <?php if (isset($_GET['payment']) && $_GET['payment'] === 'failed'): ?>
        <h2 style="color: #e74c3c;">❌ Paiement refusé</h2>
        <p>Malheureusement, les informations de votre carte bancaire sont incorrectes ou le paiement a échoué.</p>
        <p>Votre commande <strong>#<?= $orderId ?></strong> est actuellement en attente.</p>
        
        <div style="margin-top: 30px; padding: 20px; background: #fff3e0; border-radius: 16px; display: inline-block;">
            <p style="margin-bottom: 15px; color: #e67e22; font-weight: 500;">Vous pouvez toujours valider votre commande en modifiant le mode de paiement :</p>
            <form action="index.php?action=change_to_livraison" method="POST">
                <input type="hidden" name="id_commande" value="<?= $orderId ?>">
                <button type="submit" class="btn-primary" style="background: var(--orange, #f39c12); border: none; cursor: pointer;">Passer au Paiement à la Livraison</button>
            </form>
        </div>
        
        <div style="margin-top: 20px;">
            <a href="index.php?action=cart" style="color: #e74c3c; text-decoration: underline;">Annuler et retourner au panier</a>
        </div>

    <?php else: ?>
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
        
        <a href="index.php?action=products" class="btn-primary" style="margin-top: 20px; display: inline-block;">Continuer mes achats</a>
    <?php endif; ?>
</section>