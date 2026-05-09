<?php
// ai_recette_details.php

$nom = $_GET['nom'] ?? 'Recette IA';
$categorie = $_GET['categorie'] ?? 'Healthy';
$description = $_GET['desc'] ?? '';
$temps = $_GET['temps'] ?? '20 min';
$ingredientsRaw = $_GET['ing'] ?? '';
$stepsRaw = $_GET['steps'] ?? '';
$tipsRaw = $_GET['tips'] ?? '';
$imageUrl = $_GET['img'] ?? 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&q=80&w=800';

// Convert strings to arrays
$ingredients = explode(", ", $ingredientsRaw);
$steps = explode(" | ", $stepsRaw);
$tips = explode(" | ", $tipsRaw);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($nom) ?> - NutriVerse IA</title>
    
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
        <h1>NutriVerse AI</h1>
        <p>Votre chef intelligent à votre service</p>
    </div>
</div>

<div class="ai-details-container">
    <div class="ai-top">
        <span class="ai-badge">✨ Recette IA</span>
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
                    <?php foreach ($ingredients as $ing): ?>
                        <li><?= htmlspecialchars(trim($ing)) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="ai-section">
                <h3>Étapes de préparation</h3>
                <ul class="ai-steps">
                    <?php foreach ($steps as $step): ?>
                        <?php if (trim($step)): ?>
                            <li><?= htmlspecialchars(trim($step)) ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>

            <?php if (!empty($tips) && trim($tips[0])): ?>
            <div class="ai-section">
                <h3>Conseils du Chef</h3>
                <ul class="ai-steps">
                    <?php foreach ($tips as $tip): ?>
                        <?php if (trim($tip)): ?>
                            <li><?= htmlspecialchars(trim($tip)) ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <div class="ai-top">
                <span class="ai-badge">⏱ <?= htmlspecialchars($temps) ?></span>
                <span class="ai-badge">🏷 <?= htmlspecialchars($categorie) ?></span>
            </div>

            <div class="details-actions">
                <a href="recettes.php" class="btn-retour">← Retour</a>
                <button onclick="window.print()" class="btn-export">Imprimer Recette</button>
            </div>
        </div>

        <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($nom) ?>" class="ai-image">
    </div>
</div>

</body>
</html>
