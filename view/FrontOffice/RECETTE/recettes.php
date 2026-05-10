<?php
require_once __DIR__ . "/../../../Controller/no_cache.php";
require_once __DIR__ . "/../../../Controller/recetteC.php";

$recetteC = new recetteC();

$categorie = $_GET['categorie'] ?? '';
$search = $_GET['search'] ?? '';

if (!empty($search)) {
    $categorie = '';
}

// listes returns a PDOStatement
$recettes = $recetteC->listes($categorie, $search);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>NutriVerse - Recettes</title>
    <link rel="stylesheet" href="../assets/recette.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="recipe-page">

<?php 
$rel = "../";
include '../header.php'; 
?>

<!-- HERO -->
<section class="recipe-header">
    <div class="icons">
        <span>🥑</span><span>🥕</span><span>🥦</span><span>🍎</span><span>🍇</span>
        <span>🥬</span><span>🍅</span><span>🍌</span><span>🍓</span><span>🥒</span>
        <span>🌽</span><span>🍍</span><span>🥭</span><span>🍉</span><span>🥔</span>
    </div>
    <div class="header-content">
        <h1>NutriVerse</h1>
        <p>Découvrez des recettes saines, gourmandes et durables</p>
    </div>
</section>

<form method="GET">
    <div class="search-box">
        <input type="text" id="search" name="search" placeholder="Rechercher une recette..." value="<?= htmlspecialchars($search) ?>">
    </div>

    <div class="filters">
        <button type="submit" name="categorie" value="vegan">Vegan</button>
        <button type="submit" name="categorie" value="healthy">Healthy</button>
        <button type="submit" name="categorie" value="cuisine durable">Cuisine Durable</button>
        <button type="submit" name="categorie" value="all">Tous</button>
    </div>
</form>

<div class="main-layout">
    <div class="left-column">
        <!-- AI GENERATOR NORMAL -->
        <div class="ai-generator">
            <h3>Générer une recette personnalisée</h3>
            <p>Notre IA vous propose une recette sur mesure !</p>
            
            <label>🍃 Ingrédients disponibles</label>
            <input type="text" id="ingredients" placeholder="Ex : poulet, tomate...">

            <label>💚 Préférences</label>
            <input type="text" id="preferences" placeholder="Ex : vegan, healthy...">

            <label class="quick-tags-label">Filtres rapides</label>
            <div class="quick-tags">
                <button type="button" class="quick-tag-btn" data-tag="Healthy">Healthy</button>
                <button type="button" class="quick-tag-btn" data-tag="Vegan">Vegan</button>
                <button type="button" class="quick-tag-btn" data-tag="Sans gluten">Sans gluten</button>
                <button type="button" class="quick-tag-btn" data-tag="Sans lactose">Sans lactose</button>
                <button type="button" class="quick-tag-btn" data-tag="Rapide">Rapide</button>
            </div>

            <button type="button" id="btnGenerate">Générer ma recette</button>
        </div>

        <!-- AI GENERATOR BUDGET -->
        <div class="ai-generator budget-generator">
            <h3>Générateur par Budget</h3>
            <p>Notre IA propose une recette économique !</p>

            <div class="budget-box">
                <label>Budget</label>
                <div class="budget-row">
                    <input type="number" id="budget" placeholder="Ex : 10">
                    <select id="devise">
                        <option value="DT">DT</option>
                        <option value="EUR">EUR</option>
                    </select>
                </div>
            </div>

            <label>Type de repas</label>
            <div class="quick-tags" id="type_repas">
                <button type="button" class="quick-tag-btn" data-tag="Déjeuner">Déjeuner</button>
                <button type="button" class="quick-tag-btn" data-tag="Dîner">Dîner</button>
            </div>

            <button type="button" id="btnBudget">Générer par budget</button>
        </div>
    </div>

    <!-- RECETTES GRID -->
    <div class="recettes-content">
        <div class="recettes-container" id="resultats">
            <?php if ($recettes) : ?>
                <?php foreach ($recettes as $r) : ?>
                    <a href="recette_details.php?id=<?= $r['id_recette'] ?>" class="card-link">
                        <div class="card">
                            <img src="../../BackOffice/RECETTE/displayImage.php?id=<?= $r['id_recette'] ?>" alt="<?= htmlspecialchars($r['nom']) ?>">
                            <div class="card-content">
                                <div class="tags">
                                    <span class="tag"><?= htmlspecialchars($r['categorie']) ?></span>
                                </div>
                                <h3><?= htmlspecialchars($r['nom']) ?></h3>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else : ?>
                <p>Aucune recette trouvée.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="./search.js"></script>
<script src="./ai.js"></script>

</body>
</html>
