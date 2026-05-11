<!-- LINK GLOBAL NUTRIVERSE STYLE -->
<link rel="stylesheet" href="view/assets/style.css">

<!-- LUXURY DECORATIVE ELEMENTS -->
<div class="luxury-bg-blob blob-1"></div>
<div class="luxury-bg-blob blob-2" style="background: var(--primary);"></div>

<!-- HERO SECTION: SYNCED WITH RECETTES/PROGRAMMES -->
<section class="recipe-header fade-up">
    <div class="icons">
        <span>🥗</span><span>🍎</span><span>🥑</span><span>🍉</span><span>🥦</span><span>🍓</span>
        <span>🥕</span><span>🍋</span><span>🍇</span><span>🥝</span><span>🍍</span><span>🥬</span>
    </div>
    <div class="header-content">
        <h1 style="margin-bottom: 0;">NutriVerse</h1>
        <h2 style="font-size: 2rem; opacity: 0.9; font-weight: 700; margin: 10px 0; color: white;">Suivi de Commande</h2>
    </div>
</section>

<div class="container fade-in" style="padding: 0 20px 100px; position: relative; z-index: 2; margin-top: -40px;">
    
    <div style="margin-bottom: 30px;">
        <a href="shop.php?action=my_orders" style="color: var(--text-muted); font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            ← Retour à l'historique
        </a>
    </div>

    <!-- HEADER DE LA COMMANDE -->
    <div class="glass-card" style="padding: 40px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; max-width: none;">
        <div>
            <span style="font-size: 0.8rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;">ID COMMANDE</span>
            <h2 style="font-family: 'Playfair Display', serif; font-size: 2.2rem; color: var(--text-dark);">#<?= $order['id_commande'] ?? $order->getIdCommande() ?></h2>
        </div>
        <div style="text-align: right;">
            <span style="font-size: 0.8rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;">MONTANT TOTAL</span>
            <h2 style="font-family: 'Playfair Display', serif; font-size: 2.2rem; color: var(--primary-dark);"><?= number_format($order['montant_total'] ?? $order->getMontantTotal(), 2) ?> DT</h2>
        </div>
    </div>

    <!-- Progress Tracker -->
    <?php
        $orderStat = strtolower($order['statut_commande'] ?? $order->getStatutCommande());
        $livStat = (!empty($livraison) && isset($livraison['statut_livraison'])) ? strtolower($livraison['statut_livraison']) : '';

        $step1_active = in_array($orderStat, ['confirmée', 'expédiée', 'livrée']);
        $step2_active = ($livStat === 'en route' || $livStat === 'livrée' || $orderStat === 'expédiée' || $orderStat === 'livrée');
        $step3_active = ($livStat === 'livrée' || $orderStat === 'livrée');
    ?>
    <div class="glass-card" style="margin-bottom: 40px; padding: 40px; max-width: none;">
        <div class="luxury-stepper">
            <div class="l-step <?= $step1_active ? 'active' : '' ?>">
                <div class="l-circle">1</div>
                <span class="l-text">Préparation</span>
            </div>
            <div class="l-line <?= $step2_active ? 'active' : '' ?>"></div>
            <div class="l-step <?= $step2_active ? 'active' : '' ?>">
                <div class="l-circle">2</div>
                <span class="l-text">En Route</span>
            </div>
            <div class="l-line <?= $step3_active ? 'active' : '' ?>"></div>
            <div class="l-step <?= $step3_active ? 'active' : '' ?>">
                <div class="l-circle">3</div>
                <span class="l-text">Livré</span>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
        <!-- Informations de livraison -->
        <div class="glass-card" style="padding: 40px; max-width: none; margin: 0;">
            <h3 class="form-title" style="text-align: left; margin-bottom: 30px; font-size: 1.5rem;">📍 Détails d'Expédition</h3>
            <div class="summary-recap" style="padding: 25px; border-radius: 16px;">
                <div style="margin-bottom: 20px;">
                    <label style="font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Destinataire</label>
                    <div style="font-weight: 700; color: var(--text-dark);"><?= htmlspecialchars($order['nom_client'] ?? $order->getNomClient()) ?></div>
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Adresse</label>
                    <?php if (strtolower($order['statut_commande'] ?? $order->getStatutCommande()) === 'en attente'): ?>
                        <form action="shop.php?action=front_update_address" method="POST" class="inline-edit-form" style="margin-top: 10px;">
                            <input type="hidden" name="id_commande" value="<?= htmlspecialchars($order['id_commande'] ?? $order->getIdCommande()) ?>">
                            <textarea name="adresse" rows="3" required><?= htmlspecialchars($order['adresse_livraison'] ?? $order->getAdresseLivraison()) ?></textarea>
                            <button type="submit" class="btn-mini-save">Mettre à jour l'adresse</button>
                        </form>
                    <?php else: ?>
                        <div style="font-weight: 500; color: var(--text-muted); line-height: 1.6;">
                            <?= nl2br(htmlspecialchars($order['adresse_livraison'] ?? $order->getAdresseLivraison())) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div>
                    <label style="font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Paiement</label>
                    <div style="font-weight: 700; color: var(--primary-dark);">
                        <?= strpos(strtolower($order['mode_paiement'] ?? ''), 'carte') !== false ? '💳 Carte Bancaire' : '🚚 Cash à la livraison' ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produits commandés -->
        <div class="glass-card" style="padding: 40px; max-width: none; margin: 0;">
            <h3 class="form-title" style="text-align: left; margin-bottom: 30px; font-size: 1.5rem;">🛍️ Articles Commandés</h3>
            <div style="margin-bottom: 30px;">
                <?php foreach ($lines as $line): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px dashed #e2e8f0;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <span style="background: #f1f5f9; color: var(--text-dark); padding: 5px 12px; border-radius: 10px; font-weight: 900; font-size: 0.9rem;">
                                <?= is_object($line) ? $line->getQuantite() : $line['quantite'] ?>x
                            </span>
                            <span style="font-weight: 600; color: var(--text-dark);"><?= htmlspecialchars(is_object($line) ? $line->getNomProduit() : $line['nom']) ?></span>
                        </div>
                        <span style="font-weight: 800; color: var(--text-dark);"><?= number_format(is_object($line) ? $line->getPrixUnitaire() : $line['prix_unitaire'], 2) ?> DT</span>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="summary-recap" style="padding: 20px; text-align: right; border-radius: 16px;">
                <span style="font-weight: 800; color: var(--text-muted); font-size: 0.9rem;">TOTAL RÉGLÉ</span>
                <div style="font-size: 2rem; font-weight: 900; color: var(--text-dark);"><?= number_format($order['montant_total'] ?? $order->getMontantTotal(), 2) ?> DT</div>
            </div>

            <?php if (strtolower($order['statut_commande'] ?? $order->getStatutCommande()) === 'en attente'): ?>
                <div class="cancel-zone">
                    <form action="shop.php?action=front_cancel_order" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette commande ?');">
                        <input type="hidden" name="id_commande" value="<?= htmlspecialchars($order['id_commande'] ?? $order->getIdCommande()) ?>">
                        <button type="submit" class="btn-cancel-luxury">✖ Annuler la commande</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .luxury-stepper { display: flex; align-items: center; justify-content: space-between; }
    .l-step { text-align: center; position: relative; flex: 1; }
    .l-circle { width: 45px; height: 45px; background: #eef2f1; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 900; color: #94a3b8; transition: 0.4s; margin: 0 auto; }
    .l-step.active .l-circle { background: var(--primary); color: white; box-shadow: 0 0 20px rgba(89, 184, 77, 0.4); }
    .l-text { display: block; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; margin-top: 10px; color: #94a3b8; }
    .l-step.active .l-text { color: var(--primary-dark); }
    .l-line { flex: 1; height: 4px; background: #eef2f1; margin-top: -32px; border-radius: 2px; }
    .l-line.active { background: var(--primary); }

    .luxury-bg-blob { position: fixed; width: 500px; height: 500px; filter: blur(120px); z-index: -1; opacity: 0.15; border-radius: 50%; }
    .blob-1 { top: -100px; right: -100px; background: var(--primary); }
    .blob-2 { bottom: -100px; left: -100px; }

    @media (max-width: 900px) { .container > div[style*="grid-template-columns"] { grid-template-columns: 1fr !important; } }
</style>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&display=swap');

    .back-link-luxury { color: #64748b; font-weight: 700; display: inline-flex; align-items: center; gap: 10px; transition: 0.3s; }
    .back-link-luxury:hover { color: #59b84d; transform: translateX(-5px); }

    .order-header-luxury { padding: 40px; border-radius: 30px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .ref-box .tag, .total-box .tag { font-size: 0.8rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; }
    .ref-box h2, .total-box h2 { font-family: 'Playfair Display', serif; font-size: 2.2rem; margin-top: 5px; color: #1c2733; }
    .total-box h2 { color: #59b84d; }
    .total-box h2 span { font-size: 1.2rem; }

    /* TRACKER */
    .luxury-tracker { display: flex; align-items: center; justify-content: space-between; max-width: 800px; margin: 0 auto; }
    .l-step { text-align: center; position: relative; z-index: 2; flex: 1; }
    .l-circle { width: 50px; height: 50px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-weight: 900; color: white; transition: 0.4s; }
    .l-step.active .l-circle { background: #59b84d; box-shadow: 0 0 20px rgba(89,184,77,0.4); }
    .l-text { font-weight: 700; font-size: 0.85rem; color: #94a3b8; text-transform: uppercase; }
    .l-step.active .l-text { color: #1c2733; }
    .l-connector { flex: 1; height: 6px; background: #e2e8f0; margin-top: -30px; border-radius: 3px; position: relative; z-index: 1; }
    .l-connector.active { background: #59b84d; }

    /* GRID */
    .detail-grid-luxury { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 40px; }

    .section-title-premium { display: flex; align-items: center; gap: 15px; margin-bottom: 30px; border-bottom: 1.5px solid #f1f5f9; padding-bottom: 15px; }
    .section-title-premium h3 { font-family: 'Playfair Display', serif; font-size: 1.5rem; color: #1c2733; }

    .luxury-info-list { list-style: none; padding: 0; }
    .luxury-info-list li { display: flex; flex-direction: column; gap: 8px; margin-bottom: 25px; }
    .luxury-info-list .label { font-size: 0.8rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; }
    .luxury-info-list .value { font-weight: 700; color: #334155; font-size: 1.05rem; }
    .luxury-info-list .address { color: #64748b; line-height: 1.6; }

    .mode-card { color: #3b82f6 !important; }
    .mode-cash { color: #f5a000 !important; }

    /* ITEMS */
    .luxury-order-items { margin-bottom: 30px; }
    .l-order-item { display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px dashed #e2e8f0; }
    .l-item-left { display: flex; align-items: center; gap: 15px; }
    .l-qty { background: #f1f5f9; color: #1c2733; padding: 5px 12px; border-radius: 10px; font-weight: 900; font-size: 0.9rem; }
    .l-name { font-weight: 600; color: #334155; }
    .l-price { font-weight: 800; color: #1c2733; }

    .luxury-summary-box { background: #fafbfc; padding: 30px; border-radius: 24px; }
    .sum-row { display: flex; justify-content: space-between; align-items: center; }
    .sum-row span { font-weight: 800; color: #64748b; text-transform: uppercase; font-size: 0.9rem; }
    .sum-total { font-size: 2.2rem; font-weight: 900; color: #1c2733; }
    .sum-total span { font-size: 1rem; color: #59b84d; }
    .promo-tag { margin-top: 10px; color: #16a34a; font-weight: 700; text-align: right; font-size: 0.85rem; }

    .cancel-zone { margin-top: 30px; padding-top: 30px; border-top: 2px dashed #f1f5f9; }
    .btn-cancel-luxury { width: 100%; padding: 18px; background: #fff1f2; color: #e11d48; border: 1.5px solid #fda4af; border-radius: 18px; font-weight: 800; cursor: pointer; transition: 0.3s; }
    .btn-cancel-luxury:hover { background: #e11d48; color: white; }

    .inline-edit-form { display: flex; flex-direction: column; gap: 10px; }
    .inline-edit-form textarea { width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 12px; font-family: inherit; font-weight: 600; color: #334155; }
    .btn-mini-save { align-self: flex-start; background: #59b84d; color: white; border: none; padding: 8px 20px; border-radius: 10px; font-weight: 700; cursor: pointer; }

    @media (max-width: 900px) {
        .detail-grid-luxury { grid-template-columns: 1fr; }
        .order-header-luxury { flex-direction: column; text-align: center; gap: 20px; }
        .total-box { text-align: center !important; }
    }
</style>

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
