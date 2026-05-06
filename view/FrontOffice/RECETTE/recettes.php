<?php

include("../../../controller/recetteC.php");

$recetteC = new recetteC();

$categorie = $_GET['categorie'] ?? '';
$search = $_GET['search'] ?? '';

if (!empty($search)) {
    $categorie = '';
}

$recettes = $recetteC->listes($categorie, $search);

?>

<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">
<title>NutriVerse - Recettes</title>

<link rel="stylesheet" href="../assets/recette.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">

</head>

<body class="recipe-page">

<!-- NAVBAR -->
<header class="header">

<div class="nav">

<div class="logo">
<img src="../images/logo.png" class="logo-img">
</div>

<input type="checkbox" id="nav-toggle" hidden aria-hidden="true">

<label for="nav-toggle" class="menu-toggle">☰</label>

<nav class="navbar">

<a href="../nutri_front.php">Accueil</a>
<a href="../nutri_front.php#categories">Marketplace</a>
<a href="recettes.php" class="active-link">Recettes</a>
<a href="../programme/mode_selection.php">Programmes</a>
<a href="../nutri_front.php#suivi">Suivi</a>
<a href="../nutri_front.php#categories">Produits</a>
<a href="#" class="cart-icon">🛒</a>
<a href="#" class="btn-outline">Se connecter</a>
<a href="#" class="btn-primary">S'inscrire</a>

</nav>
</div>
</header>

<!-- HERO -->
<section class="recipe-header">

<div class="icons">
<span>🥑</span>
<span>🥕</span>
<span>🥦</span>
<span>🍎</span>
<span>🍇</span>
<span>🥬</span>
<span>🍅</span>
<span>🍌</span>
<span>🍓</span>
<span>🥒</span>
<span>🌽</span>
<span>🍍</span>
<span>🥭</span>
<span>🍉</span>
<span>🥔</span>
</div>

<div class="header-content">
<h1>NutriVerse</h1>
<p>Découvrez des recettes saines, gourmandes et durables</p>
</div>

</section>

<form method="GET">

<div class="search-box">
<input type="text" id="search" name="search" placeholder="Rechercher une recette...">
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

<!-- GENERATEUR NORMAL -->
<div class="ai-generator">

<h3> Générer une recette personnalisée </h3>

<p>
Entrez vos ingrédients et préférences,<br>
notre IA vous propose une recette sur mesure !
</p>

<label>🍃 Ingrédients disponibles</label>
<input type="text" id="ingredients" placeholder="Ex : poulet, tomate...">

<label>💚 Préférences</label>
<input type="text" id="preferences" placeholder="Ex : vegan, healthy...">

<label class="quick-tags-label"> Filtres rapides</label>

<div class="quick-tags">
<button type="button" class="quick-tag-btn" data-tag="Healthy">Healthy</button>
<button type="button" class="quick-tag-btn" data-tag="Vegan">Vegan</button>
<button type="button" class="quick-tag-btn" data-tag="Sans gluten">Sans gluten</button>
<button type="button" class="quick-tag-btn" data-tag="Sans lactose">Sans lactose</button>
<button type="button" class="quick-tag-btn" data-tag="Rapide (<30min)">Rapide &lt;30min</button>
<button type="button" class="quick-tag-btn" data-tag="Riche en protéines">Riche en protéines</button>
<button type="button" class="quick-tag-btn" data-tag="Faible en calories">Faible en calories</button>
</div>

<button type="button" id="btnGenerate">Générer ma recette</button>

<div class="ai-tip">
<strong>Astuce</strong><br>
Soyez précis pour des recettes encore plus adaptées à vos envies !
</div>

</div>

<!-- GENERATEUR BUDGET -->
<div class="ai-generator budget-generator">

<h3> Générateur par Budget </h3>

<p>
Entrez votre budget et préférences,<br>
notre IA propose une recette économique !
</p>

<div class="budget-box">

<label> Budget</label>

<div class="budget-row">
<input type="number" id="budget" placeholder="Ex : 10">

<select id="devise">
<option value="DT">DT</option>
<option value="EUR">EUR</option>
<option value="USD">USD</option>
</select>
</div>

</div>

<label> Type de repas</label>

<div class="quick-tags" id="type_repas">
<button type="button" class="quick-tag-btn" data-tag="Petit déjeuner">Petit déjeuner</button>
<button type="button" class="quick-tag-btn" data-tag="Déjeuner">Déjeuner</button>
<button type="button" class="quick-tag-btn" data-tag="Dîner">Dîner</button>
<button type="button" class="quick-tag-btn" data-tag="Snack">Snack</button>
<button type="button" class="quick-tag-btn" data-tag="Dessert">Dessert</button>
<button type="button" class="quick-tag-btn" data-tag="Boisson">Boisson</button>
</div>

<label>💛 Préférences</label>

<div class="quick-tags" id="budget_preferences">
<button type="button" class="quick-tag-btn" data-tag="Healthy">Healthy</button>
<button type="button" class="quick-tag-btn" data-tag="Vegan">Vegan</button>
<button type="button" class="quick-tag-btn" data-tag="Rapide">Rapide</button>
<button type="button" class="quick-tag-btn" data-tag="Riche en protéines">Riche protéines</button>
<button type="button" class="quick-tag-btn" data-tag="Sans lactose">Sans lactose</button>
<button type="button" class="quick-tag-btn" data-tag="Sans gluten">Sans gluten</button>
<button type="button" class="quick-tag-btn" data-tag="Faible en calories">Faible calories</button>
</div>

<label>👥 Nombre de personnes</label>

<div class="quick-tags" id="personnes">
<button type="button" class="quick-tag-btn" data-tag="1">1 personne</button>
<button type="button" class="quick-tag-btn" data-tag="2">2 personnes</button>
<button type="button" class="quick-tag-btn" data-tag="4">4 personnes</button>
</div>

<button type="button" id="btnBudget">Générer recette par budget</button>

<div class="ai-tip">
<strong>Astuce</strong><br>
Plus votre budget est précis, meilleure sera la recette.
</div>

</div>

</div>

<!-- RECETTES -->
<div class="recettes-content">

<div class="recettes-container" id="resultats">

<?php foreach ($recettes as $r) { ?>

<a href="recette_details.php?id=<?= $r['id_recette'] ?>" class="card-link">

<div class="card">

<img
src="../../BackOffice/RECETTE/displayImage.php?id=<?= $r['id_recette'] ?>"
alt="<?= $r['nom'] ?>"
>

<div class="card-content">

<div class="tags">
<span class="tag"><?= $r['categorie'] ?></span>
</div>

<h3><?= $r['nom'] ?></h3>

</div>

</div>

</a>

<?php } ?>

</div>

</div>

</div>

<script src="./search.js"></script>
<script src="./ai.js"></script>

</body>
</html>
