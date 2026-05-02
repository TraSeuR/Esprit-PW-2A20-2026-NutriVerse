<?php

include("../../Controller/recetteC.php");

$recetteC = new recetteC();


$categorie = $_GET['categorie'] ?? '';
$search = $_GET['search'] ?? '';
if (!empty($search)) { 
    $categorie = ''; } 
$recettes = $recetteC->listes($categorie, $search);
?>

<!DOCTYPE html>
<html lang="fr">
<head>

  <meta charset="UTF-8">
  <title>NutriVerse - Recettes</title>
  <link rel="stylesheet" href="assets/recette.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
</head>

<body class="recipe-page">


<!-- NAVBAR -->
<header class="header">

<div class="nav">
<div class="logo">
    <img src="/RECETTE_VF3/View/backOffice_recette/images/logo.png" class="logo-img">
</div>

<input type="checkbox" id="nav-toggle" hidden aria-hidden="true">

<label for="nav-toggle" class="menu-toggle">☰</label>

<nav class="navbar">

<a href="#">Accueil</a>

<a href="#">Marketplace</a>

<a href="recettes.php" class="active-link">Recettes</a>

<a href="#">Programmes</a>

<a href="#">Suivi</a>

<a href="#">Produits</a>

<a href="#" class="cart-icon">🛒</a>

<a href="#" class="btn-outline">Se connecter</a>

<a href="#" class="btn-primary">S'inscrire</a>

</nav>

</div>

</header>



<!-- HERO VERT -->
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
    <input 
        type="text"
         id="search"
        name="search"
        placeholder="Rechercher une recette..."
        >
</div>

<div class="filters">

    <button type="submit" name="categorie" value="vegan">Vegan</button>
    <button type="submit" name="categorie" value="healthy">Healthy</button>
    <button type="submit" name="categorie" value="cuisine durable">Cuisine Durable</button>
    <button type="submit" name="categorie" value="all">Tous</button>

</div>

</form>
<div class="main-layout">

    <div class="ai-generator">

    <h3> Générer une recette personnalisée <span class="badge"></span></h3>

    <p>
        Entrez vos ingrédients et préférences,<br>
        notre IA vous propose une recette sur mesure !
    </p>

    <label>🍃 Ingrédients disponibles</label>
    <input type="text" id="ingredients" placeholder="Ex : poulet, tomate...">

    <label>💚 Préférences</label>
    <input type="text" id="preferences" placeholder="Ex : vegan, healthy...">
   
  <label class="quick-tags-label">⚡ Filtres rapides</label>
    <div class="quick-tags" role="group" aria-label="Filtres rapides de préférences">
        <button type="button" class="quick-tag-btn" data-tag="Healthy"><span>Healthy</span></button>
        <button type="button" class="quick-tag-btn" data-tag="Vegan"><span>Vegan</span></button>
        <button type="button" class="quick-tag-btn" data-tag="Sans gluten"><span>Sans gluten</span></button>
        <button type="button" class="quick-tag-btn" data-tag="Sans lactose"><span>Sans lactose</span></button>
        <button type="button" class="quick-tag-btn" data-tag="Rapide (<30min)"><span>Rapide &lt;30min</span></button>
        <button type="button" class="quick-tag-btn" data-tag="Riche en protéines"><span>Riche en protéines</span></button>
        <button type="button" class="quick-tag-btn" data-tag="Faible en calories"><span>Faible en calories</span></button>
    </div>

<button type="button" id="btnGenerate">Générer ma recette</button>

    <div class="ai-tip">
         <strong>Astuce</strong><br>
        Soyez précis pour des recettes encore plus adaptées à vos envies !
    </div>

</div>


    <!-- RECETTES -->
    <div class="recettes-content">
        <div class="recettes-container" id="resultats">

            <?php foreach ($recettes as $r) { ?> 

            <a href="recette_details.php?id=<?= $r['id_recette'] ?>" class="card-link">

                <div class="card">
                    <img src="../backOffice_recette/displayImage.php?id=<?= $r['id_recette'] ?>"
                    alt="<?= $r['nom'] ?>">

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
<script src="search.js"></script>
<script src="ai.js"></script>
</body>
</html>