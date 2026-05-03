<?php
// No-cache headers: prevent browser from showing this page
// from cache after the user logs out.
require_once __DIR__ . "/../../Controller/no_cache.php";

// Auto-login from "remember me" cookie
if (!isset($_SESSION['id_user']) && isset($_COOKIE['remember_token'])) {
  require_once __DIR__ . "/../../Controller/userC.php";
  $userC = new userC();
  $user = $userC->getUserByRememberToken($_COOKIE['remember_token']);
  if ($user) {
    $_SESSION['id_user'] = $user['id_user'];
    $_SESSION['email']   = $user['email'];
    $_SESSION['role']    = $user['role'];
    $_SESSION['nom']     = $user['nom'];
    $_SESSION['prenom']  = $user['prenom'];
    $_SESSION['avatar']  = $user['avatar'] ?? 'avatar1.png';
  }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriVerse - Accueil</title>

  <!-- CSS -->
  <link rel="stylesheet" href="assets/css/front.css" />

  <!-- Google Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/userbox.css" />
</head>

<body>
  <!-- HEADER -->
  <header class="header">
    <div class="container nav">
      <div class="logo">
        <img src="images/logo.png" alt="Logo NutriVerse" class="logo-img" />
      </div>

      <input type="checkbox" id="nav-toggle" hidden aria-hidden="true" />
      <label for="nav-toggle" class="menu-toggle" aria-label="Ouvrir le menu">☰</label>
      <nav class="navbar">
        <a href="#hero">Accueil</a>
        <a href="#categories">Marketplace</a>
        <a href="#recipes">Recettes</a>
        <a href="#programs">Programmes</a>
        <a href="#suivi">Suivi</a>
        <a href="#categories">Produits</a>

        <a href="#" class="cart-icon" title="Commandes">🛒</a>

        <?php if (isset($_SESSION['id_user'])): ?>
          <div class="user-menu admin-box-style">
            <button class="user-btn transparent-btn" id="userMenuBtn">
              <img src="images/<?= htmlspecialchars($_SESSION['avatar'] ?? 'avatar1.png') ?>" alt="Avatar"
                class="user-avatar-circle" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
              <div class="user-info-text">
                <h4><?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?></h4>
                <p>Utilisateur</p>
              </div>
              <span>▼</span>
            </button>

            <div class="user-dropdown" id="userDropdown" style="top: 100%; right: 0;">
              <a href="edit_profile.php">👤 Éditer Profil</a>
              <a href="logout.php" class="logout">🚪 Déconnexion</a>
            </div>
          </div>
        <?php else: ?>
          <a href="login.php" class="btn-outline">Se connecter</a>
          <a href="register.php" class="btn-primary">S'inscrire</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <!-- HERO -->
  <section class="hero section" id="hero">
    <div class="container hero-grid">
      <div class="hero-content fade-up">
        <span class="badge">Nutrition intelligente • Santé • Bien-être</span>
        <h1>Mangez mieux,<br />vivez mieux</h1>
        <p>
          NutriVerse vous accompagne vers une alimentation plus saine grâce à
          des produits locaux, des conseils nutritionnels, des recettes
          équilibrées et des programmes bien-être personnalisés.
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
          <img src="images/hero-salad.jpg" alt="Salade saine" />
        </div>

        <div class="hero-side">
          <div class="small-img">
            <img src="images/oranges.jpg" alt="Oranges et gingembre" />
          </div>
          <div class="quote-box">
            <p>“Votre santé commence dans votre assiette.”</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CATEGORIES -->
  <section class="categories section" id="categories">
    <div class="container">
      <div class="section-header fade-up">
        <span class="section-tag">Marketplace locale</span>
        <h2>Explorez nos catégories de produits</h2>
        <p>
          Des produits sains, frais et sélectionnés avec soin pour votre
          bien-être quotidien.
        </p>
      </div>

      <div class="category-grid">
        <div class="category-card fade-up">
          <img src="images/fruits.jpg" alt="Fruits frais" />
          <h3>Fruits Frais</h3>
          <p>Riches en vitamines et en fraîcheur.</p>
        </div>

        <div class="category-card fade-up delay-1">
          <img src="images/legumes.jpg" alt="Légumes croquants" />
          <h3>Légumes Croquants</h3>
          <p>Des légumes sains pour une cuisine équilibrée.</p>
        </div>

        <div class="category-card fade-up delay-2">
          <img src="images/dairy.jpg" alt="Crèmerie bio" />
          <h3>Crèmerie Bio</h3>
          <p>Laitages naturels et produits fermiers.</p>
        </div>

        <div class="category-card fade-up delay-3">
          <img src="images/grains.jpg" alt="Grains et céréales" />
          <h3>Grains & Céréales</h3>
          <p>Des bases nutritives pour votre énergie.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- RECIPES -->
  <section class="recipes section" id="recipes">
    <div class="container">
      <div class="section-header center fade-up">
        <span class="section-tag">Cuisine santé</span>
        <h2>Dernières Recettes Santé</h2>
        <p>
          Inspirez-vous de nos créations culinaires simples, locales et
          savoureuses.
        </p>
      </div>

      <div class="recipe-grid">
        <div class="recipe-card large fade-up">
          <img src="images/recipe1.jpg" alt="Recette 1" />
          <div class="recipe-overlay">
            <h3>Salade vitaminée</h3>
            <span>15 min</span>
          </div>
        </div>

        <div class="recipe-card fade-up delay-1">
          <img src="images/recipe2.jpg" alt="Recette 2" />
          <div class="recipe-overlay">
            <h3>Smoothie kiwi</h3>
            <span>10 min</span>
          </div>
        </div>

        <div class="recipe-card fade-up delay-2">
          <img src="images/recipe3.jpg" alt="Recette 3" />
          <div class="recipe-overlay">
            <h3>Toast avocat</h3>
            <span>12 min</span>
          </div>
        </div>

        <div class="recipe-card fade-up delay-3">
          <img src="images/recipe4.jpg" alt="Recette 4" />
          <div class="recipe-overlay">
            <h3>Grillade légère</h3>
            <span>20 min</span>
          </div>
        </div>

        <div class="recipe-card fade-up delay-1">
          <img src="images/recipe5.jpg" alt="Recette 5" />
          <div class="recipe-overlay">
            <h3>Chia bowl</h3>
            <span>8 min</span>
          </div>
        </div>
      </div>

      <div class="center-btn fade-up">
        <a href="#" class="text-link">Découvrir tout le blog →</a>
      </div>
    </div>
  </section>

  <!-- PROGRAMS -->
  <section class="programs section" id="programs">
    <div class="container programs-grid">
      <div class="program-list fade-up">
        <div class="program-card">
          <img src="images/elite.jpg" alt="Programme 1" />
          <div class="program-info">
            <div>
              <h3>Nutrition Élite</h3>
              <p>Optimisez vos performances sportives.</p>
            </div>
            <span>01</span>
          </div>
        </div>

        <div class="program-card">
          <img src="images/fitness.jpg" alt="Programme 2" />
          <div class="program-info">
            <div>
              <h3>Challenge Cardio</h3>
              <p>Relancez votre métabolisme intelligemment.</p>
            </div>
            <span>02</span>
          </div>
        </div>

        <div class="program-card">
          <img src="images/detox.jpg" alt="Programme 3" />
          <div class="program-info">
            <div>
              <h3>Reset Détox</h3>
              <p>Purifiez votre organisme en douceur.</p>
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
          bien-être et activité physique. Conçus pour aider vos utilisateurs à
          mieux manger, mieux bouger et mieux vivre au quotidien.
        </p>
        <a href="#" class="btn-primary large">Découvrir les programmes</a>
      </div>
    </div>
  </section>

  <!-- SUIVI -->
  <section class="suivi section" id="suivi">
    <div class="container">
      <div class="section-header center fade-up">
        <span class="section-tag">Suivi intelligent</span>
        <h2>Gardez le contrôle sur votre santé</h2>
        <p>
          Suivez votre alimentation, vos habitudes et vos objectifs bien-être
          dans une seule plateforme simple et moderne.
        </p>
      </div>

      <div class="suivi-box fade-up delay-1">
        <div class="suivi-card">
          <h3>Suivi Nutritionnel</h3>
          <p>Analyse quotidienne de vos repas et recommandations adaptées.</p>
        </div>
        <div class="suivi-card">
          <h3>Objectifs Santé</h3>
          <p>
            Définissez vos objectifs : énergie, poids, équilibre et
            performance.
          </p>
        </div>
        <div class="suivi-card">
          <h3>Progression</h3>
          <p>Visualisez vos améliorations semaine après semaine.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="cta section">
    <div class="container cta-box fade-up">
      <div>
        <span class="section-tag">Commencez aujourd’hui</span>
        <h2>Votre santé mérite une meilleure expérience digitale</h2>
        <p>
          Une plateforme moderne pour connecter nutrition, recettes, produits
          locaux et bien-être.
        </p>
      </div>
      <a href="#" class="btn-primary large">Rejoindre NutriVerse</a>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="footer">
    <div class="container footer-content">
      <div>
        <h3>NutriVerse</h3>
        <p>Nutrition intelligente pour une vie plus saine.</p>
      </div>

      <div class="footer-links">
        <a href="#">Accueil</a>
        <a href="#">Recettes</a>
        <a href="#">Produits</a>
        <a href="#">Programmes</a>
        <a href="#">Suivi</a>
      </div>
    </div>
  </footer>

  <script src="assets/js/userbox.js"></script>
</body>

</html>
