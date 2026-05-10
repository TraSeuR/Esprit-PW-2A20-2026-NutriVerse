<?php
require_once __DIR__ . "/../../Controller/no_cache.php";
require_once __DIR__ . '/../../controller/recetteC.php';


// Auto-login from "remember me" cookie
if (!isset($_SESSION['id_user']) && isset($_COOKIE['remember_token'])) {
  require_once __DIR__ . "/../../Controller/userC.php";
  $userC = new userC();
  $user = $userC->getUserByRememberToken($_COOKIE['remember_token']);
  if ($user) {
    $_SESSION['id_user'] = $user['id_user'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['nom'] = $user['nom'];
    $_SESSION['prenom'] = $user['prenom'];
    $_SESSION['avatar'] = $user['avatar'] ?? 'avatar1.png';
  }
}

$recetteC = new recetteC();
$recettes = $recetteC->listes("all", "");

require_once __DIR__ . '/../../Controller/ProduitController.php';
$produitController = new ProduitController();
$latest_produits = $produitController->getLatestProduits(4);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriVerse - Accueil</title>

  <!-- CSS -->
  <link rel="stylesheet" href="assets/css/front.css" />

  <style>

  </style>

  <!-- Google Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet">
</head>
<body>
  <!-- HEADER -->
  <?php 
  $rel = "";
  include 'header.php'; 
  ?>

  <!-- HERO -->
  <section class="hero section" id="hero">
    <div class="container hero-grid">
      <div class="hero-content fade-up">
        <span class="badge">Nutrition intelligente • Santé • Bien-être</span>
        <h1>Mangez mieux,<br>vivez mieux</h1>
        <p>
          NutriVerse vous accompagne vers une alimentation plus saine grâce à
          des produits locaux, des conseils nutritionnels, des recettes équilibrées
          et des programmes bien-être personnalisés.
        </p>

        <div class="hero-buttons">
          <a href="#" class="btn-primary large">Explorer la nutrition</a>
          <a href="#" class="btn-secondary large">Découvrir nos produits</a>
        </div>

        <div class="hero-stats">
          <div class="stat-card">
            <h3>+120</h3>
            <p>Recettes santé</p>
          </div>
          <div class="stat-card">
            <h3>+80</h3>
            <p>Produits locaux</p>
          </div>
          <div class="stat-card">
            <h3>+15</h3>
            <p>Programmes bien-être</p>
          </div>
        </div>
      </div>

      <div class="hero-visual fade-up delay-1">
        <div class="hero-main-img">
          <img src="images/hero-salad.jpg" alt="Salade saine">
        </div>

        <div class="hero-side">
          <div class="small-img">
            <img src="images/oranges.jpg" alt="Oranges et gingembre">
          </div>
          <div class="quote-box">
            <p>“Votre santé commence dans votre assiette.”</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CATEGORIES (Now Products) -->
  <section class="categories section" id="categories">
    <div class="container">
      <div class="section-header fade-up">
        <span class="section-tag">Marketplace locale</span>
        <h2>Nos derniers produits locaux</h2>
        <p>Des produits sains, frais et sélectionnés avec soin pour votre bien-être quotidien.</p>
      </div>

      <div class="category-grid">
        <?php 
        $delay = 0;
        foreach($latest_produits as $prod): 
            $imgGlob = glob(__DIR__ . '/../BackOffice/images/produit_' . $prod['idproduit'] . '.*');
            $imgPath = $imgGlob ? '../BackOffice/images/' . basename($imgGlob[0]) : 'images/fruits.jpg';
        ?>
        <a href="produit/listProduit.php?search=<?= urlencode($prod['nom']) ?>" style="text-decoration: none; color: inherit; display: block;">
          <div class="category-card fade-up <?= $delay > 0 ? 'delay-'.$delay : '' ?>">
            <img src="<?= htmlspecialchars($imgPath) ?>" alt="<?= htmlspecialchars($prod['nom']) ?>" style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%; margin-bottom: 15px;">
            <h3><?= htmlspecialchars($prod['nom']) ?></h3>
            <p style="font-weight: bold; color: var(--primary); margin-top: 10px; font-size: 1.1rem;"><?= htmlspecialchars($prod['prix']) ?> TND</p>
          </div>
        </a>
        <?php 
        $delay++;
        endforeach; 
        ?>
        <?php if(empty($latest_produits)): ?>
           <p style="grid-column: 1 / -1; text-align: center;">Aucun produit disponible pour le moment.</p>
        <?php endif; ?>
      </div>
      
      <div style="text-align: center; margin-top: 30px;" class="fade-up">
        <a href="produit/listProduit.php" class="btn-primary" style="padding: 12px 25px;">Voir tous nos produits</a>
      </div>
    </div>
  </section>


<section class="recipes section" id="recipes">
  <div class="container">

    <div class="section-header center fade-up">
      <span class="section-tag">Cuisine santé</span>
      <h2>Dernières Recettes Santé</h2>
      <p>Inspirez-vous de nos créations culinaires simples, locales et savoureuses.</p>
    </div>

    <div class="recipe-grid">

      <?php
      $i = 0;
      foreach($recettes as $r){
        $large = ($i == 0) ? "large" : "";
      ?>

      <a href="RECETTE/recette_details.php?id=<?= $r['id_recette'] ?>" class="recipe-card <?= $large ?> fade-up">

        <img src="../BackOffice/RECETTE/displayImage.php?id=<?= $r['id_recette'] ?>">

        <div class="recipe-overlay">
          <h3><?= $r['nom'] ?></h3>
          <span><?= $r['categorie'] ?></span>
        </div>

      </a>

      <?php
      $i++;
      if($i == 5) break; // juste 5 recettes accueil
      }
      ?>

    </div>

    <div class="center-btn fade-up">
      <a href="RECETTE/recettes.php" class="text-link">
        Découvrir toutes les recettes →
      </a>
    </div>

  </div>
</section>


  <!-- PROGRAMS -->
  <section class="programs section" id="programs">
    <div class="container programs-grid">
      <div class="program-list fade-up">

        <div class="program-card">
          <img src="images/prise.jpg" alt="Programme 1">
          <div class="program-info">
            <div>
              <h3>Prise de masse</h3>
              <p>Optimisez vos performances et votre volume musculaire.</p>
            </div>
            <span>01</span>
          </div>
        </div>

        <div class="program-card">
          <img src="images/perte.jpg" alt="Programme 2">
          <div class="program-info">
            <div>
              <h3>Perte de poids</h3>
              <p>Brûlez des calories et affinez votre silhouette.</p>
            </div>
            <span>02</span>
          </div>
        </div>

        <div class="program-card">
          <img src="images/equilibre.jpg" alt="Programme 3">
          <div class="program-info">
            <div>
              <h3>Équilibre Santé</h3>
              <p>Stabilisez votre poids et purifiez votre organisme.</p>
            </div>
            <span>03</span>
          </div>
        </div>
      </div>

      <div class="program-content fade-up delay-1">
        <span class="section-tag">Nos Programmes</span>
        <h2>Optimisez votre vitalité avec NutriVerse</h2>
        <p>
          Découvrez des programmes exclusifs alliant nutrition intelligente,
          bien-être et activité physique. Conçus pour aider vos utilisateurs
          à mieux manger, mieux bouger et mieux vivre au quotidien.
        </p>
        <a href="programme/mode_selection.php" class="btn-primary large">Découvrir les programmes</a>
      </div>
    </div>
  </section>



  <!-- CTA -->
  <section class="cta section">
    <div class="container cta-box fade-up">
      <div>
        <span class="section-tag">Commencez aujourd’hui</span>
        <h2>Votre santé mérite une meilleure expérience digitale</h2>
        <p>Une plateforme moderne pour connecter nutrition, recettes, produits locaux et bien-être.</p>
      </div>
      <a href="#" class="btn-primary large">Rejoindre NutriVerse</a>
    </div>
  </section>

  <footer class="footer">
    <div class="container footer-content">
      <div>
        <h3>NutriVerse</h3>
        <p>Nutrition intelligente pour une vie plus saine.</p>
      </div>

      <div class="footer-links">
        <a href="#hero">Accueil</a>
        <a href="#recipes">Recettes</a>
        <a href="produit/listProduit.php">Produits locaux</a>

        <a href="#programs">Programmes</a>
      </div>
    </div>
  </footer>


  <?php include 'programme/coach_widget.php'; ?>
</body>
</html>
