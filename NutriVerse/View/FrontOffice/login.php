<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="NutriVerse – Connexion à votre espace nutrition personnalisé." />
  <title>Connexion – NutriVerse</title>

  <!-- Shared stylesheet -->
  <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>
  <!-- ════════════════════════════════════════════════════════════
       AUTH ROOT – two-column layout
       Left  : decorative green panel
       Right : login form
  ════════════════════════════════════════════════════════════ -->
  <div class="auth-root">
    <!-- ── RIGHT FORM SIDE ─────────────────────────────────── -->
    <main class="auth-form-side">
      <div class="auth-form-box">
        <!-- Form header -->
        <div class="auth-form-header">
          <h1>Bon retour !</h1>
          <p>Connectez-vous à votre compte NutriVerse.</p>
        </div>
        <!-- Login form-->
        <form id="form-login" action="userlogin.php" method="POST" novalidate>
          <!-- Email -->
          <div class="form-group">
            <label for="email" class="form-label">Adresse e-mail</label>
            <div class="input-wrapper">
              <span class="input-icon">✉️</span>
              <input class="form-input" type="email" id="email" name="email" placeholder="votre@email.com"
                autocomplete="email" required />
            </div>
          </div>

          <!-- Password -->
          <div class="form-group">
            <label for="mot_de_passe" class="form-label">Mot de passe</label>
            <div class="input-wrapper">
              <span class="input-icon">🔒</span>
              <input class="form-input" type="password" id="mot_de_passe" name="mot_de_passe" placeholder="••••••••"
                autocomplete="current-password" required />
              <!-- Toggle button – minimal JS for UX only -->
              <button type="button" class="toggle-pw" id="toggle-pw-btn" aria-label="Afficher/masquer le mot de passe">
                👁️
              </button>
            </div>
          </div>

          <!-- Remember me + Forgot password -->
          <div class="form-meta">
            <label class="form-check" for="remember_me">
              <input type="checkbox" id="remember_me" name="remember_me" value="1" />
              <span class="form-check-label">Se souvenir de moi</span>
            </label>

            <a href="forgot-password.html" class="text-sm fw-500 text-primary">
              Mot de passe oublié ?
            </a>
          </div>

          <!-- Submit -->
          <button type="submit" class="btn btn-primary btn-block btn-lg" id="btn-login">
            Se connecter
          </button>
        </form>

        <!-- Divider -->
        <div class="divider">ou</div>

        <!-- Link to register -->

        <p class="auth-footer-link">
          Pas encore de compte ?
          <a href="register.html">Créer un compte</a>
        </p>
      </div>
    </main>
  </div>
  <!-- /.auth-root -->

  <!-- ── Minimal JS: password toggle + form redirect ─────────── -->
  <script>
    /* UI-only: toggle password field visibility */
    document
      .getElementById("toggle-pw-btn")
      .addEventListener("click", function () {
        const field = document.getElementById("mot_de_passe");
        const isHidden = field.type === "password";
        field.type = isHidden ? "text" : "password";
        this.textContent = isHidden ? "🙈" : "👁️";
      });
  </script>
</body>

</html>