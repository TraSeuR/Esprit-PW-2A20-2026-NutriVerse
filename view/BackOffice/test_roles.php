<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Handle role switch
if (isset($_GET['set_role'])) {
    $_SESSION['user_role'] = $_GET['set_role'];
    $_SESSION['role'] = $_GET['set_role']; // for compatibility
    header('Location: nutri_back.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>NutriVerse - Test des Rôles RBAC</title>
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f5f7f8; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 40px; border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); text-align: center; max-width: 500px; }
        h1 { color: #0b8f3c; margin-bottom: 30px; }
        .role-btn { display: block; width: 100%; padding: 15px; margin: 10px 0; border-radius: 14px; border: 1px solid #e5ebea; background: white; cursor: pointer; font-weight: 600; text-decoration: none; color: #1a2433; transition: 0.3s; }
        .role-btn:hover { background: #eaf8ef; color: #0b8f3c; border-color: #0b8f3c; transform: translateY(-2px); }
        .role-admin { background: #0b8f3c !important; color: white !important; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Simulateur de Rôles</h1>
        <p>Cliquez sur un rôle pour simuler une connexion et tester l'accès au Back Office :</p>
        
        <a href="?set_role=admin" class="role-btn role-admin">Connexion en tant qu'Administrateur</a>
        <a href="?set_role=Responsable recette" class="role-btn">Connexion: Responsable Recette</a>
        <a href="?set_role=Responsable Produit" class="role-btn">Connexion: Responsable Produit</a>
        <a href="?set_role=Responsable commande" class="role-btn">Connexion: Responsable Commande</a>
        <a href="?set_role=Livreur" class="role-btn">Connexion: Livreur</a>
        <a href="?set_role=Responsable Programmes" class="role-btn">Connexion: Responsable Programmes</a>
        <a href="?set_role=Responsable Offre & ingrediant" class="role-btn">Connexion: Responsable Offre & Ingrédient</a>
        
        <p style="margin-top: 20px; font-size: 0.8rem; color: #6e7782;">Ce script est temporaire pour vos tests.</p>
    </div>
</body>
</html>
