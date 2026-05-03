<?php
// No-cache headers + session start
require_once __DIR__ . "/../../Controller/no_cache.php";
require_once __DIR__ . "/../../Controller/csrf.php";

// Redirect already-logged-in users
if (isset($_SESSION['id_user'])) {
    header("Location: index.php");
    exit();
}

// Parse error codes
$errors = [];
if (!empty($_GET['errors'])) {
    $errors = explode(',', $_GET['errors']);
}
$errorMap = [
    'invalid_credentials' => '⚠️ Adresse e-mail ou mot de passe incorrect.',
    'empty_fields'        => '⚠️ Veuillez remplir tous les champs.',
    'account_inactive'    => '🚫 Votre compte a été désactivé. Contactez l\'administrateur.',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="NutriVerse – Connexion à votre espace nutrition personnalisé." />
  <title>Connexion – NutriVerse</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <div class="auth-root">
    <main class="auth-form-side">
      <div class="auth-form-box">

        <div class="auth-form-header">
          <h1>Bon retour !</h1>
          <p>Connectez-vous à votre compte NutriVerse.</p>
        </div>

        <?php foreach ($errors as $code): ?>
          <?php if (isset($errorMap[$code])): ?>
            <div style="background-color:#f8d7da;color:#721c24;border:1px solid #f5c6cb;border-radius:8px;padding:12px;margin-bottom:20px;text-align:center;font-weight:500;font-size:.95rem;">
              <?= $errorMap[$code] ?>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>

        <?php if (isset($_GET['success']) && $_GET['success'] === 'verified'): ?>
          <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;border-radius:8px;padding:12px;margin-bottom:20px;text-align:center;font-weight:500;">
            ✅ Votre e-mail a été vérifié ! Vous pouvez maintenant vous connecter.
          </div>
        <?php endif; ?>

        <?php if (isset($_GET['success']) && $_GET['success'] === 'password_reset'): ?>
          <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;border-radius:8px;padding:12px;margin-bottom:20px;text-align:center;font-weight:500;">
            ✅ Mot de passe réinitialisé avec succès. Connectez-vous.
          </div>
        <?php endif; ?>

        <form id="form-login" action="userlogin.php" method="POST" novalidate>
          <?php echo csrf_field(); ?>

          <!-- Email -->
          <div class="form-group">
            <label for="email" class="form-label">Adresse e-mail</label>
            <div class="input-wrapper">
              <span class="input-icon">✉️</span>
              <input class="form-input" type="email" id="email" name="email"
                placeholder="votre@email.com" autocomplete="email" required />
            </div>
          </div>

          <!-- Password -->
          <div class="form-group">
            <label for="mot_de_passe" class="form-label">Mot de passe</label>
            <div class="input-wrapper">
              <span class="input-icon">🔒</span>
              <input class="form-input" type="password" id="mot_de_passe" name="mot_de_passe"
                placeholder="••••••••" autocomplete="current-password" required />
              <button type="button" class="toggle-pw" id="toggle-pw-btn" aria-label="Afficher/masquer le mot de passe">👁️</button>
            </div>
          </div>

          <!-- Remember me + Forgot -->
          <div class="form-meta">
            <label class="form-check" for="remember_me">
              <input type="checkbox" id="remember_me" name="remember_me" value="1" />
              <span class="form-check-label">Se souvenir de moi</span>
            </label>
            <a href="forgot_password.php" class="text-sm fw-500 text-primary">Mot de passe oublié ?</a>
          </div>

          <button type="submit" class="btn btn-primary btn-block btn-lg" id="btn-login">Se connecter</button>
        </form>

        <!-- ── Google login button ────────────────── -->
        <div class="divider">ou</div>

        <a href="google_auth.php" class="btn-google" id="btn-google-login">
          <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg"
               alt="Google" width="20" height="20" />
          Continuer avec Google
        </a>

        <div class="divider"></div>

        <p class="auth-footer-link">
          Pas encore de compte ?
          <a href="register.php">Créer un compte</a>
        </p>
      </div>
    </main>
  </div>

  <script src="assets/js/login.validate.js"></script>
  <script>
    // Prevent form resubmission on refresh
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
  </script>
</body>
</html>
