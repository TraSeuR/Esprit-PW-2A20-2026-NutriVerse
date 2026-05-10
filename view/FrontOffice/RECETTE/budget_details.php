<?php

$nom = $_GET['nom'] ?? '';
$categorie = $_GET['categorie'] ?? '';
$description = $_GET['description'] ?? '';
$temps = $_GET['temps'] ?? '';
$image = $_GET['image'] ?? '';

$ingredients = explode("|", $_GET['ingredients'] ?? '');
$e�tapes = explode("|", $_GET['e�tapes'] ?? '');

$conseil = $_GET['conseil'] ?? '';

$budget_total = $_GET['budget_total'] ?? 0;
$budget_user  = $_GET['budget_user'] ?? 0;
$personnes    = $_GET['personnes'] ?? 1;
$devise       = $_GET['devise'] ?? 'DT';

$reste = $budget_user - $budget_total;

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($nom) ?> - NutriVerse Budget</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="../assets/recette_details.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Nunito:wght@700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../assets/recette_details.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Nunito:wght@700;800&display=swap" rel="stylesheet">
</head>
<body class="recipe-page">

<div class="header">
    <div class="icons">
        <span>🥑</span><span>🥕</span><span>🥦</span><span>🍎</span>
        <span>🍇</span><span>🥬</span><span>🍅</span><span>🍌</span>
        <span>🍓</span><span>🥒</span><span>🌽</span><span>🍍</span>
        <span>🥭</span><span>🍉</span><span>🥔</span>
    </div>
    <div class="header-content">
        <h1>NutriVerse Budget</h1>
        <p>Cuisiner malin, manger sain</p>
    </div>
</div>

<div class="ai-details-container">
    <div class="ai-top">
        <span class="ai-badge">💰 Budget IA : <?= htmlspecialchars($budget_user) ?> <?= htmlspecialchars($devise) ?></span>
    </div>
    
    <h1 class="ai-title"><?= htmlspecialchars($nom) ?></h1>

    <div class="ai-grid">
        <div class="ai-info">
            <div class="ai-section">
                <h3>Description</h3>
                <p><?= htmlspecialchars($description) ?></p>
            </div>

            <div class="ai-section">
                <h3>Ingrédients</h3>
                <ul class="ai-ingredients">
                    <?php foreach ($ingredients as $i): ?>
                        <?php if(trim($i)): ?>
                        <li><?= htmlspecialchars(trim($i)) ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="ai-section">
                <h3>Détails</h3>
                <p><strong>Temps :</strong> <?= htmlspecialchars($temps) ?></p>
                <p><strong>Catégorie :</strong> <?= htmlspecialchars($categorie) ?></p>
                <p><strong>Pour :</strong> <?= htmlspecialchars($personnes) ?> personne(s)</p>
            </div>

            <div class="ai-section">
                <h3>É�tapes</h3>
                <ol class="ai-steps">
                    <?php foreach ($e�tapes as $e): ?>
                        <?php if(trim($e)): ?>
                        <li><?= htmlspecialchars(trim($e)) ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </div>

            <div class="ai-section">
                <h3>Analyse du Budget</h3>
                <div class="ai-top">
                    <span class="ai-badge">Coût : <?= htmlspecialchars($budget_total) ?> <?= htmlspecialchars($devise) ?></span>
                    <span class="ai-badge" style="background:#e8f5e9; color:#2e7d32;">Économie : <?= htmlspecialchars($reste) ?> <?= htmlspecialchars($devise) ?></span>
                </div>
            </div>

            <?php if ($conseil): ?>
            <div class="ai-section">
                <h3>Astuce Éco</h3>
                <p><?= htmlspecialchars($conseil) ?></p>
            </div>
            <?php endif; ?>

            <div class="details-actions">
                <a href="recettes.php" class="btn-retour">← Retour</a>
                <button type="button" class="btn-export" onclick="window.print()">Imprimer</button>
            </div>
        </div>

        <img src="<?= htmlspecialchars($image) ?>" class="ai-image">
    </div>
</div>

</body>
</html>

