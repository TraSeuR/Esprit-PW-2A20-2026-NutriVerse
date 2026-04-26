require_once __DIR__ . '/../../controller/RegimeController.php';
require_once __DIR__ . '/../../controller/PlanningController.php';

$regimeCtrl = new RegimeController();
$regimes    = $regimeCtrl->listRegimes();

$planningCtrl = new PlanningController();
// INNER JOIN : chaque planning avec le nom du régime associé
$plannings    = $planningCtrl->listPlanningsWithRegimes();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Programmes - NutriVerse</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

    <header class="header">
        <div class="fade-in">
            <h1>Mes Programmes</h1>
            <p>Retrouvez vos configurations personnalisées</p>
        </div>
    </header>

    <div class="nav-bar">
        <a href="mode_selection.php">← Accueil Module</a>
        <a href="add_regime.php?action=new">Nouveau Programme</a>
        <a href="list_programmes.php" class="active">Ma Liste</a>
    </div>

    <div class="container fade-in">
        <div class="form-box">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="color: var(--primary-dark);">Régimes Enregistrés</h2>
                <a href="add_regime.php?action=new" class="btn-primary" style="width: auto; padding: 10px 20px; font-size: 0.9rem;">+ Ajouter</a>
            </div>

            <table class="creative-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Type</th>
                        <th>Calories</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($regimes)): ?>
                        <tr><td colspan="5" style="text-align: center;">Aucun régime trouvé.</td></tr>
                    <?php else: ?>
                        <?php foreach ($regimes as $r): ?>
                        <tr>
                            <td>#<?php echo $r->getIdRegime(); ?></td>
                            <td style="font-weight: 600;"><?php echo htmlspecialchars($r->getNom()); ?></td>
                            <td><?php echo htmlspecialchars($r->getType()); ?></td>
                            <td><span style="color: var(--primary-dark); font-weight: bold;"><?php echo $r->getCalorieJour(); ?></span> kcal</td>
                            <td class="action-links">
                                <a href="add_planning.php?id_regime=<?php echo $r->getIdRegime(); ?>" style="color: var(--primary);">+ Planning</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="form-box" style="margin-top: 40px;">
            <h2 style="color: var(--primary-dark); margin-bottom: 20px;">Plannings Sportifs</h2>
            <table class="creative-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre</th>
                        <th>Régime associé</th>
                        <th>Sommeil</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($plannings)): ?>
                        <tr><td colspan="4" style="text-align: center;">Aucun planning trouvé.</td></tr>
                    <?php else: ?>
                        <?php foreach ($plannings as $p): ?>
                        <tr>
                            <td>#<?php echo $p['id_planning']; ?></td>
                            <td style="font-weight: 600;"><?php echo htmlspecialchars($p['titre_planning']); ?></td>
                            <!-- nom_regime vient directement de l'INNER JOIN -->
                            <td style="font-size: 0.8rem; color: #4361ee; font-weight: 700;"><?php echo htmlspecialchars($p['nom_regime']); ?></td>
                            <td><?php echo htmlspecialchars($p['sommeil']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $p['statut']; ?>">
                                    <?php echo $p['statut']; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
