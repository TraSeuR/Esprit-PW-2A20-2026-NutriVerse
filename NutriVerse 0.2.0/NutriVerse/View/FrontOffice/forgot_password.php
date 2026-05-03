<?php
// No-cache headers + session start
require_once __DIR__ . "/../../Controller/no_cache.php";
require_once __DIR__ . "/../../Controller/csrf.php";

// Redirect logged-in users
if (isset($_SESSION['id_user'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="NutriVerse – Réinitialisation du mot de passe." />
    <title>Mot de passe oublié – NutriVerse</title>
    <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
    <div class="auth-root">
        <main class="auth-form-side">
            <div class="auth-form-box">

                <div class="auth-form-header">
                    <h1>Mot de passe oublié 🔑</h1>
                    <p>Entrez votre e-mail pour recevoir un code de vérification (OTP).</p>
                </div>

                <?php if (isset($_GET['errors']) && $_GET['errors'] === 'email_not_found'): ?>
                <div style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;border-radius:8px;padding:12px;margin-bottom:16px;text-align:center;">
                    ⚠️ Cette adresse e-mail n'existe pas dans notre base de données.
                </div>
                <?php endif; ?>

                <?php if (isset($_GET['error']) && $_GET['error'] === 'empty'): ?>
                <div style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;border-radius:8px;padding:12px;margin-bottom:16px;text-align:center;">
                    ⚠️ Veuillez entrer votre adresse e-mail.
                </div>
                <?php endif; ?>

                <form action="send_forgot.php" method="POST" novalidate>
                    <?php echo csrf_field(); ?>

                    <div class="form-group">
                        <label for="email" class="form-label">Adresse e-mail</label>
                        <div class="input-wrapper">
                            <span class="input-icon">✉️</span>
                            <input class="form-input" type="email" id="email" name="email"
                                placeholder="votre@email.com" autocomplete="email" required />
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        Envoyer le code OTP
                    </button>
                </form>

                <div class="divider">ou</div>

                <p class="auth-footer-link">
                    <a href="login.php">← Retour à la connexion</a>
                </p>

            </div>
        </main>
    </div>
</body>
</html>

