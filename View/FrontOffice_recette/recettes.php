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
<body>

<div class="header">
  <div class="icons">
     <span>🥑</span><span>🥕</span><span>🥦</span><span>🍎</span>
     <span>🍇</span><span>🥬</span><span>🍅</span><span>🍌</span>
     <span>🍓</span><span>🥒</span><span>🌽</span><span>🍍</span>
     <span>🥭</span><span>🍉</span><span>🥔</span>
  </div>

  <div class="header-content">
    <h1>NutriVerse</h1>
    <p>Découvrez des recettes saines, gourmandes et durables</p>
  </div>
</div>

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

    <button id="btnGenerate"> Générer ma recette</button>

    <div class="ai-tip">
         <strong>Astuce</strong><br>
        Soyez précis pour des recettes encore plus adaptées à vos envies !
    </div>

</div>


    <!-- RECETTES -->
    <div class="recettes-content">
        <div class="container" id="resultats">

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