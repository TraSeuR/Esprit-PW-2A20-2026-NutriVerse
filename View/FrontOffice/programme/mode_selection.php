<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriVerse - Programmes</title>
    
    <!-- Nutrition styles -->
    <link rel="stylesheet" href="../assets/front.css">
    <link rel="stylesheet" href="../assets/recette.css">
    <!-- Existing programme styles -->
    <link rel="stylesheet" href="../assets/style.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../assets/technical_front.css">

    <style>
        body {
            background-color: #f8f8f6;
        }
        .recipe-header {
            height: 250px;
            margin-bottom: 40px;
        }
        .header-content h1 {
            font-size: clamp(2.5rem, 6vw, 4rem) !important;
            margin-bottom: 10px;
        }
        .choice-card {
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        .choice-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 45px rgba(0,0,0,0.15);
        }
        .choice-overlay h2 {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
        }
        .page-bg-subtle {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at top right, rgba(89, 184, 77, 0.05), transparent),
                        radial-gradient(circle at bottom left, rgba(89, 184, 77, 0.05), transparent);
            z-index: -1;
        }

    </style>
</head>
<body class="recipe-page">

    <?php include 'global_header.php'; ?>

    <div class="page-bg-subtle"></div>
 <!-- HERO VERT -->
    <section class="recipe-header ">
        <div class="icons">
            <span>🥗</span>
            <span>🏃‍♂️</span>
            <span>💧</span>
            <span>📊</span>
            <span>💪</span>
            <span>🍏</span>
            <span>🔋</span>
            <span>🥬</span>
            <span>🧘‍♀️</span>
            <span>👟</span>
            <span>🍳</span>
            <span>🥑</span>
            <span>🍋</span>
            <span>🥣</span>
            <span>🥛</span>
        </div>
      <div class="header-content">
    <h1 style="margin-bottom: 0; font-size: 4.2rem;">
        Nos solutions
    </h1>

    <h2 style="
        font-size: 1.35rem;
        opacity: 0.95;
        font-weight: 600;
        margin: 8px 0 0;
        color: white;
    ">
        Choisissez votre approche pour transformer votre santé
    </h2>
</div>
    </section>

    <div class="container" style="max-width: 1200px; padding-bottom: 80px;">
        <div class="choice-grid fade-up" style="animation-delay: 0.2s;">
            
            <!-- PROPOSITION 1 : MANUEL -->
            <a href="add_regime.php?action=new" class="choice-card">
                <img src="https://thumbs.dreamstime.com/b/comptage-des-calories-r%C3%A9gime-alimentaire-contr%C3%B4le-aliments-et-perte-de-poids-tablette-avec-application-compteur-calorique-%C3%A0-l-157093707.jpg" alt="Plat sain" class="choice-img">
                <div class="choice-overlay">
                    <p style="text-transform: uppercase; font-weight: 800; font-size: 0.7rem; color: #59b84d; margin-bottom: 5px;">Suivi Manuel</p>
                    <h2>Gérer mon Régime</h2>
                    <p>Déterminez vos objectifs personnels et construisez votre plan idéal étape par étape.</p>
                </div>
            </a>

            <!-- PROPOSITION 2 : EXPERTS -->
            <a href="view_ready_plannings.php" class="choice-card">
                <img src="https://img.freepik.com/photos-gratuite/nature-morte-du-rouleau-mousse_23-2151817470.jpg?semt=ais_hybrid&w=740&q=80" alt="Sport high-end" class="choice-img">
                <div class="choice-overlay">
                    <p style="text-transform: uppercase; font-weight: 800; font-size: 0.7rem; color: #59b84d; margin-bottom: 5px;">Collection Experts</p>
                    <h2>Plannings Complets</h2>
                    <p>Accédez aux programmes clés-en-main validés par nos conseillers en nutrition.</p>
                </div>
            </a>

        </div>

        <div style="text-align: center; margin-top: 60px;" class="fade-up">
            <a href="../nutri_front.php" style="color: #1c2733; font-weight: 600; text-decoration: none; border-bottom: 2px solid #59b84d; padding-bottom: 5px; transition: 0.3s;">← Retour au portail NutriVerse</a>
        </div>
    </div>


    <?php include 'coach_widget.php'; ?>

</body>
</html>
