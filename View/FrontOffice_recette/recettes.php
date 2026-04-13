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
        name="search"
        placeholder="Rechercher une recette..."
        value="<?= $search ?>" > <!--affiche la valr eli tketbt -->
</div>

<div class="filters">

    <button type="submit" name="categorie" value="vegan">Vegan</button>
    <button type="submit" name="categorie" value="healthy">Healthy</button>
    <button type="submit" name="categorie" value="cuisine durable">Cuisine Durable</button>
    <button type="submit" name="categorie" value="all">Tous</button>

</div>

</form>


<div class="container">

<?php foreach ($recettes as $r) { ?> 

<a href="recette_details.php?id=<?= $r['id_recette'] ?>" class="card-link"> <!-- lien lel detail mta recet yabath lid en url-->

    <div class="card">

      <img 
        src="../backOffice_recette/images/<?= $r['images'] ?>" 
        alt="<?= $r['nom'] ?>"
      >

      <div class="card-content">

        <div class="tags">
          <span class="tag"><?= $r['categorie'] ?></span>
        </div>

        <h3><?= $r['nom'] ?></h3> <!-- affich esm el rect-->

      </div>

    </div>

</a>

<?php } ?>

</div>

</body>
</html>