<section class="container my-orders">
    <div class="page-header">
        <h1>Mes commandes</h1>
        <p>Retrouvez ici l’historique de toutes vos commandes.</p>
    </div>

    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <div class="empty-icon">🛒</div>
            <h3>Vous n'avez encore passé aucune commande</h3>
            <p>Explorez nos produits et passez votre première commande dès maintenant !</p>
            <a href="index.php?action=products" class="btn-primary">Découvrir les produits</a>
        </div>
    <?php else: ?>
        <div class="orders-table-wrapper">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>N° commande</th>
                        <th>Date</th>
                        <th>Montant total</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td data-label="N° commande">#<?= $order['id_commande'] ?></td>
                            <td data-label="Date"><?= date('d/m/Y', strtotime($order['date_commande'])) ?></td>
                            <td data-label="Montant total">
                                <?= number_format($order['montant_total'], 2) ?> DT
                                <?php if(!empty($order['code_promo'])): ?>
                                    <div style="font-size: 0.8rem; color: #27ae60;"> <?= htmlspecialchars($order['code_promo']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td data-label="Statut">
                                <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $order['statut_commande'])) ?>">
                                    <?= htmlspecialchars($order['statut_commande']) ?>
                                </span>
                            </td>
                            <td data-label="Actions">
                                <a href="index.php?action=order_detail&id=<?= $order['id_commande'] ?>" class="btn-detail">
                                    Voir le détail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<style>
    /* Styles spécifiques à la page Mes commandes */
    .my-orders {
        max-width: 1200px;
        margin: 4rem auto;
        padding: 0 1.5rem;
        display: block;
        width: 100%;
    }

    .page-header {
        margin-bottom: 3rem;
        text-align: center;
        width: 100%;
    }

    .page-header h1 {
        font-size: 2rem;
        color: var(--text, #1c2733);
        margin-bottom: 0.5rem;
    }

    .page-header p {
        color: var(--muted, #6f7680);
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 24px;
        box-shadow: var(--shadow, 0 12px 30px rgba(0, 0, 0, 0.08));
    }

    .empty-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        color: var(--text, #1c2733);
    }

    .empty-state p {
        color: var(--muted, #6f7680);
        margin-bottom: 1.5rem;
    }

    /* Table wrapper responsive */
    .orders-table-wrapper {
        overflow-x: auto;
        border-radius: 20px;
        box-shadow: var(--shadow, 0 12px 30px rgba(0, 0, 0, 0.08));
        background: white;
        width: 100%;
        display: block;
    }

    .orders-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.95rem;
    }

    .orders-table thead tr {
        background: var(--green-soft, #edf7ec);
        border-bottom: 2px solid var(--green, #59b84d);
    }

    .orders-table th {
        text-align: left;
        padding: 1rem 1.2rem;
        font-weight: 600;
        color: var(--green-dark, #3f9636);
    }

    .orders-table td {
        padding: 1rem 1.2rem;
        border-bottom: 1px solid #e5e9eb;
        vertical-align: middle;
    }

    .orders-table tbody tr:hover {
        background-color: #f9fff9;
        transition: 0.2s;
    }

    /* Badges de statut */
    .status-badge {
        display: inline-block;
        padding: 0.3rem 0.8rem;
        border-radius: 30px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .status-en-attente {
        background: #fff3e0;
        color: #e67e22;
    }

    .status-confirmée {
        background: #e3f7e3;
        color: #27ae60;
    }

    .status-expédiée {
        background: #e0f0ff;
        color: #2980b9;
    }

    .status-livrée {
        background: #d5f5e3;
        color: #2ecc71;
    }

    .status-annulée {
        background: #ffe6e6;
        color: #e74c3c;
    }

    /* Bouton détail */
    .btn-detail {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        background: none;
        border: 1px solid var(--green, #59b84d);
        color: var(--green-dark, #3f9636);
        padding: 0.4rem 1rem;
        border-radius: 30px;
        font-size: 0.8rem;
        font-weight: 500;
        text-decoration: none;
        transition: 0.2s;
    }

    .btn-detail:hover {
        background: var(--green, #59b84d);
        color: white;
        border-color: var(--green, #59b84d);
    }

    /* Responsive : transformation en cartes sur mobile */
    @media (max-width: 768px) {
        .orders-table thead {
            display: none;
        }

        .orders-table,
        .orders-table tbody,
        .orders-table tr,
        .orders-table td {
            display: block;
            width: 100%;
        }

        .orders-table tr {
            margin-bottom: 1.5rem;
            border: 1px solid #e5e9eb;
            border-radius: 16px;
            background: white;
            padding: 0.5rem 0;
        }

        .orders-table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f0f0f0;
            text-align: right;
        }

        .orders-table td:last-child {
            border-bottom: none;
        }

        .orders-table td::before {
            content: attr(data-label);
            font-weight: 600;
            color: var(--green-dark, #3f9636);
            text-align: left;
        }

        .btn-detail {
            justify-content: center;
            width: 100%;
        }
    }
</style>