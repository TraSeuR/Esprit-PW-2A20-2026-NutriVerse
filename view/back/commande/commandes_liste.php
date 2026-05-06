<h2>Liste des commandes</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Date</th>
        <th>Client</th>
        <th>Total</th>
        <th>Statut</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($orders as $o): ?>
        <tr>
            <td><?= $o['id_commande'] ?></td>
            <td><?= $o['date_commande'] ?></td>
            <td><?= htmlspecialchars($o['nom_client']) ?></td>
            <td><?= number_format($o['montant_total'], 2) ?> DT</td>
            <td><?= $o['statut_commande'] ?></td>
            <td>
                <a href="?action=admin_order_view&id=<?= $o['id_commande'] ?>">Voir</a>
                <a href="?action=admin_order_delete&id=<?= $o['id_commande'] ?>" onclick="return confirm('Supprimer ?')">Supprimer</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>