<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Auto-login from "remember me" cookie
if (!isset($_SESSION['id_user']) && isset($_COOKIE['remember_token'])) {
    require_once __DIR__ . '/../../../Controller/userC.php';
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
<!-- HEADER GLOBAL NUTRIVERSE -->
<header class="header">
    <div class="container nav">
        <div class="logo">
            <a href="../index.php">
                <img src="../images/logo.png" alt="Logo NutriVerse" class="logo-img">
            </a>
        </div>

        <input type="checkbox" id="nav-toggle" hidden aria-hidden="true" />
        <label for="nav-toggle" class="menu-toggle" aria-label="Ouvrir le menu">☰</label>

        <nav class="navbar">
            <a href="../index.php">Accueil</a>
            <a href="../index.php#categories">Marketplace</a>
            <a href="../RECETTE/recettes.php">Recettes</a>
            <a href="mode_selection.php" class="active-link">Programmes</a>
            <a href="../index.php#suivi">Suivi</a>
            <a href="../index.php#categories">Produits</a>

            <a href="#" class="cart-icon" title="Commandes">🛒</a>

            <?php if (isset($_SESSION['id_user'])): ?>
                <div class="user-menu admin-box-style">
                    <button class="user-btn transparent-btn" id="userMenuBtn">
                        <img src="../images/<?= htmlspecialchars($_SESSION['avatar'] ?? 'avatar1.png') ?>" alt="Avatar"
                            class="user-avatar-circle" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                        <div class="user-info-text">
                            <h4><?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?></h4>
                            <p>Utilisateur</p>
                        </div>
                        <span>▼</span>
                    </button>

                    <div class="user-dropdown" id="userDropdown" style="top: 100%; right: 0;">
                        <a href="../utilisateur/edit_profile.php">👤 Éditer Profil</a>
                        <a href="../utilisateur/logout.php" class="logout">🚪 Déconnexion</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="../utilisateur/login.php" class="btn-outline">Se connecter</a>
                <a href="../utilisateur/register.php" class="btn-primary">S'inscrire</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
