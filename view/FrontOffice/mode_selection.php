<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriVerse - Programmes</title>
    
    <!-- Nutrition styles -->
    <link rel="stylesheet" href="assets/front.css">
    <!-- Existing programme styles -->
    <link rel="stylesheet" href="../assets/style.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        .page-bg { 
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;
            background-size: cover; background-position: center;
        }
    </style>
</head>
<body>

    <!-- SHARED NAVBAR -->
    <header class="header">
        <div class="container nav">
            <div class="logo">
                <img src="images/logo.png" alt="Logo NutriVerse" class="logo-img">
            </div>
            <nav class="navbar">
                <a href="nutri_front.php">Accueil</a>
                <a href="nutri_front.php#categories">Marketplace</a>
                <a href="nutri_front.php#recipes">Recettes</a>
                <a href="mode_selection.php" class="active">Programmes</a>
                <a href="nutri_front.php#suivi">Suivi</a>
                <a href="#" class="btn-primary">Mon Compte</a>
            </nav>
        </div>
    </header>

    <div class="page-bg" style="background-image: linear-gradient(rgba(89, 184, 77, 0.7), rgba(45, 106, 79, 0.8)), url('https://images.unsplash.com/photo-1505253149613-112d21d9f6a9?auto=format&fit=crop&q=80&w=1920');"></div>

    <header class="premium-header fade-up" style="color: white; padding: 120px 20px 60px;">
        <h1 style="color: white; font-size: 4rem;">Nos Solutions</h1>
        <p style="color: rgba(255,255,255,0.9); font-weight: 400;">Choisissez votre approche pour transformer votre santé.</p>
    </header>

    <div class="container" style="max-width: 1200px; padding-bottom: 80px;">
        <div class="choice-grid fade-up" style="animation-delay: 0.2s;">
            
            <!-- PROPOSITION 1 : MANUEL -->
            <a href="add_regime.php?action=new" class="choice-card">
                <img src="https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?auto=format&fit=crop&q=80&w=800" alt="Plat sain" class="choice-img">
                <div class="choice-overlay">
                    <p style="text-transform: uppercase; font-weight: 800; font-size: 0.7rem; color: var(--primary-light); margin-bottom: 5px;">Suivi Manuel</p>
                    <h2>Gérer mon Régime</h2>
                    <p>Déterminez vos objectifs personnels et construisez votre plan idéal étape par étape.</p>
                </div>
            </a>

            <!-- PROPOSITION 2 : EXPERTS -->
            <a href="view_ready_plannings.php" class="choice-card">
                <img src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&q=80&w=800" alt="Sport high-end" class="choice-img">
                <div class="choice-overlay">
                    <p style="text-transform: uppercase; font-weight: 800; font-size: 0.7rem; color: var(--primary-light); margin-bottom: 5px;">Collection Experts</p>
                    <h2>Plannings Complets</h2>
                    <p>Accédez aux programmes clés-en-main validés par nos conseillers en nutrition.</p>
                </div>
            </a>

        </div>

        <div style="text-align: center; margin-top: 60px;" class="fade-up" style="animation-delay: 0.4s;">
            <a href="nutri_front.php" style="color: white; font-weight: 600; text-decoration: none; border-bottom: 2px solid rgba(255,255,255,0.5); padding-bottom: 5px;">← Retour au portail NutriVerse</a>
        </div>
    </div>

</body>
</html>
