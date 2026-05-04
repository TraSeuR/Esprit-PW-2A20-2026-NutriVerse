<section class="container my-orders">
    <div class="page-header fade-up">
        <a href="index.php?action=my_orders" class="back-link">← Retour à mes commandes</a>
        <h1>Détail de la commande #<?= $order['id_commande'] ?? $order->getIdCommande() ?></h1>
    </div>

    <!-- Progress Tracker -->
    <?php
        $orderStat = strtolower($order['statut_commande'] ?? $order->getStatutCommande());
        $livStat = (!empty($livraison) && isset($livraison['statut_livraison'])) ? strtolower($livraison['statut_livraison']) : '';

        // Conditions pour les étapes
        $step1_active = in_array($orderStat, ['confirmée', 'expédiée', 'livrée']);
        $step2_active = ($livStat === 'en route' || $livStat === 'livrée');
        $step3_active = ($livStat === 'livrée' || $orderStat === 'livrée');
    ?>
    <div class="status-tracker-container fade-up delay-1">
        <div class="tracker-segments">
            <div class="segment <?= $step1_active ? 'active' : '' ?>">
                <div class="segment-bar"></div>
                <span class="segment-label">Boutique</span>
            </div>
            <div class="segment <?= $step2_active ? 'active' : '' ?>">
                <div class="segment-bar"></div>
                <span class="segment-label">En route</span>
            </div>
            <div class="segment <?= $step3_active ? 'active' : '' ?>">
                <div class="segment-bar"></div>
                <span class="segment-label">Livré</span>
            </div>
        </div>
    </div>

    <div class="detail-grid">
        <!-- Informations de livraison -->
        <div class="detail-card info-card fade-up delay-1">
            <h3>📍 Informations de livraison</h3>
            <ul>
                <?php 
                    $orderStatus = strtolower(htmlspecialchars($order['statut_commande'] ?? $order->getStatutCommande()));
                    $isPending = ($orderStatus === 'en attente');
                ?>
                <li><strong>Client :</strong> <?= htmlspecialchars($order['nom_client'] ?? $order->getNomClient()) ?></li>
                <li><strong>Adresse :</strong> 
                    <?php if ($isPending): ?>
                        <div style="margin-top: 10px; padding: 15px; background: #f8f9fa; border-radius: 12px;">
                            <form action="index.php?action=front_update_address" method="POST" style="display:flex; flex-direction:column; gap:10px;">
                                <input type="hidden" name="id_commande" value="<?= $order['id_commande'] ?? $order->getIdCommande() ?>">
                                <textarea name="adresse" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; font-family:inherit; min-height:80px;"><?= htmlspecialchars($order['adresse_livraison'] ?? $order->getAdresseLivraison()) ?></textarea>
                                <button type="submit" style="align-self:flex-start; background:var(--green, #59b84d); color:white; border:none; padding:8px 16px; border-radius:6px; cursor:pointer; font-weight:600;">Modifier l'adresse</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <br><?= nl2br(htmlspecialchars($order['adresse_livraison'] ?? $order->getAdresseLivraison())) ?>
                    <?php endif; ?>
                </li>
                <li><strong>Téléphone :</strong> <?= htmlspecialchars($order['telephone_client'] ?? $order->getTelephoneClient()) ?></li>
                <li><strong>Date :</strong> <?= date('d/m/Y H:i', strtotime($order['date_commande'] ?? $order->getDateCommande())) ?></li>
                <li><strong>Paiement :</strong> 
                    <?php 
                        $modePaiement = strtolower($order['mode_paiement'] ?? 'livraison');
                        if ($modePaiement === 'carte' || strpos($modePaiement, 'carte') !== false) {
                            echo '<span style="color: #2980b9; font-weight: 500;">💳 Carte bancaire</span>';
                        } else {
                            echo '<span style="color: #e67e22; font-weight: 500;">🚚 À la livraison</span>';
                        }
                    ?>
                </li>
                <li><strong>Statut :</strong> 
                    <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $order['statut_commande'] ?? $order->getStatutCommande())) ?>">
                        <?= htmlspecialchars($order['statut_commande'] ?? $order->getStatutCommande()) ?>
                    </span>
                </li>
            </ul>
            
            <?php if ($isPending): ?>
                <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px dashed #e5e9eb;">
                    <form action="index.php?action=front_cancel_order" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette commande ?');">
                        <input type="hidden" name="id_commande" value="<?= $order['id_commande'] ?? $order->getIdCommande() ?>">
                        <button type="submit" style="width: 100%; background: white; color: #e74c3c; border: 2px solid #e74c3c; padding: 12px; border-radius: 12px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: 0.3s;" onmouseover="this.style.background='#e74c3c'; this.style.color='white'" onmouseout="this.style.background='white'; this.style.color='#e74c3c'">Annuler ma commande</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <!-- Produits commandés -->
        <div class="detail-card products-card fade-up delay-2">
            <h3>🛍️ Produits commandés</h3>
            <div class="products-list">
                <?php foreach ($lines as $line): ?>
                    <div class="product-item">
                        <div class="product-info">
                            <span class="product-qty"><?= is_object($line) ? $line->getQuantite() : $line['quantite'] ?>x</span>
                            <span class="product-name"><?= htmlspecialchars(is_object($line) ? $line->getNomProduit() : $line['nom']) ?></span>
                        </div>
                        <div class="product-price">
                            <?= number_format(is_object($line) ? $line->getPrixUnitaire() : $line['prix_unitaire'], 2) ?> DT
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="total-line final">
                <div style="display: flex; flex-direction: column;">
                    <span>Montant Total</span>
                    <?php if(!empty($order['code_promo'])): ?>
                        <small style="color: #27ae60; font-size: 0.9rem; font-weight: 500; margin-top: 5px;">Code promo appliqué : <?= htmlspecialchars($order['code_promo']) ?> (-20%)</small>
                    <?php endif; ?>
                </div>
                <span class="total-amount"><?= number_format($order['montant_total'] ?? $order->getMontantTotal(), 2) ?> DT</span>
            </div>
        </div>
    </div>
</section>

<style>
    .back-link {
        display: inline-block;
        color: var(--green, #59b84d);
        font-weight: 600;
        margin-bottom: 1rem;
        text-decoration: none;
    }

    .back-link:hover {
        color: var(--green-dark, #3f9636);
        text-decoration: underline;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-top: 2rem;
    }

    .detail-card {
        background: #ffffff;
        border-radius: 24px;
        padding: 2.5rem;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.05);
    }

    /* Tracker Styles */
    .status-tracker-container {
        margin: 2rem 0 3rem;
        padding: 0;
    }

    .tracker-segments {
        display: flex;
        gap: 10px;
        width: 100%;
    }

    .segment {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .segment-bar {
        height: 8px;
        background: #e5e9eb;
        border-radius: 4px;
        transition: all 0.6s ease;
    }

    .segment.active .segment-bar {
        background: var(--green, #59b84d);
        box-shadow: 0 0 15px rgba(89, 184, 77, 0.3);
    }

    .segment-label {
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--muted, #6f7680);
        text-align: center;
        transition: color 0.4s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .segment.active .segment-label {
        color: var(--green-dark, #3f9636);
    }


    .detail-card h3 {
        margin-bottom: 1.5rem;
        color: var(--text, #1c2733);
        border-bottom: 2px solid var(--green-soft, #edf7ec);
        padding-bottom: 0.5rem;
    }

    .info-card ul {
        list-style: none;
        padding: 0;
    }

    .info-card li {
        margin-bottom: 1rem;
        color: var(--muted, #6f7680);
    }

    .info-card strong {
        color: var(--text, #1c2733);
        display: inline-block;
        width: 120px;
    }

    .product-item {
        display: flex;
        justify-content: space-between;
        padding: 1rem 0;
        border-bottom: 1px dashed #e5e9eb;
    }

    .product-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .product-qty {
        background: var(--green-soft, #edf7ec);
        color: var(--green-dark, #3f9636);
        padding: 0.3rem 0.6rem;
        border-radius: 8px;
        font-weight: bold;
        font-size: 0.9rem;
    }

    .product-name {
        font-weight: 500;
    }

    .product-price {
        font-weight: 600;
    }

    .total-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 2px solid var(--green-soft, #edf7ec);
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--green-dark, #3f9636);
    }

    @media (max-width: 768px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }
    }
</style>