<?php
// No-cache headers + session start
require_once __DIR__ . "/../../Controller/no_cache.php";
require_once __DIR__ . "/../../Controller/csrf.php";


// Redirect already logged-in users
if (isset($_SESSION['id_user'])) {
    header("Location: index.php");
    exit();
}

// Parse error codes from add_user.php
$errors = [];
if (!empty($_GET['errors'])) {
    $errors = explode(',', $_GET['errors']);
}
$errorMap = [
    'nom_required' => 'Le nom est requis.',
    'prenom_required' => 'Le prénom est requis.',
    'email_invalid' => 'Adresse e-mail invalide.',
    'email_taken' => 'Cette adresse e-mail est déjà utilisée.',
    'password_short' => 'Le mot de passe doit contenir au moins 8 caractères.',
    'password_mismatch' => 'Les mots de passe ne correspondent pas.',
    'password_weak' => 'Le mot de passe doit contenir au moins une majuscule, un chiffre et un caractère spécial.',
];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="NutriVerse – Créez votre compte et commencez votre voyage nutritionnel." />
    <title>Inscription – NutriVerse</title>
    <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>
    <div class="auth-root">
        <main class="auth-form-side">
            <div class="auth-form-box">

                <div class="auth-form-header">
                    <h1>Créer un compte</h1>
                    <p>Remplissez le formulaire ci-dessous pour rejoindre la plateforme.</p>
                </div>

                <?php if (!empty($errors)): ?>
                    <div
                        style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;border-radius:8px;padding:14px;margin-bottom:20px;">
                        <strong>Veuillez corriger les erreurs suivantes :</strong>
                        <ul style="margin:8px 0 0 16px;padding:0;">
                            <?php foreach ($errors as $code): ?>
                                <?php if (isset($errorMap[$code])): ?>
                                    <li style="font-size:.9rem;"><?= $errorMap[$code] ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form id="form-register" action="add_user.php" method="POST" novalidate>
                    <?php echo csrf_field(); ?>

                    <!-- Nom + Prénom -->
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="prenom" class="form-label">Prénom</label>
                            <div class="input-wrapper">
                                <span class="input-icon">👤</span>
                                <input class="form-input" type="text" id="prenom" name="prenom" placeholder="Jean"
                                    autocomplete="given-name" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="nom" class="form-label">Nom</label>
                            <div class="input-wrapper">
                                <span class="input-icon">👤</span>
                                <input class="form-input" type="text" id="nom" name="nom" placeholder="Dupont"
                                    autocomplete="family-name" required />
                            </div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email" class="form-label">Adresse e-mail</label>
                        <div class="input-wrapper">
                            <span class="input-icon">✉️</span>
                            <input class="form-input" type="email" id="email" name="email" placeholder="votre@email.com"
                                autocomplete="email" required />
                        </div>
                    </div>

                    <!-- Avatar Selection -->
                    <div class="form-group">
                        <label class="form-label">Choisissez votre Avatar</label>
                        <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                            <label style="cursor: pointer; text-align: center;">
                                <input type="radio" name="avatar" value="avatar1.png" checked style="display: none;">
                                <img src="images/avatar1.png" alt="Avatar 1"
                                    style="width: 60px; height: 60px; border-radius: 50%; border: 3px solid transparent; transition: 0.3s;"
                                    class="avatar-option">
                            </label>
                            <label style="cursor: pointer; text-align: center;">
                                <input type="radio" name="avatar" value="avatar2.png" style="display: none;">
                                <img src="images/avatar2.png" alt="Avatar 2"
                                    style="width: 60px; height: 60px; border-radius: 50%; border: 3px solid transparent; transition: 0.3s;"
                                    class="avatar-option">
                            </label>
                            <label style="cursor: pointer; text-align: center;">
                                <input type="radio" name="avatar" value="avatar3.png" style="display: none;">
                                <img src="images/avatar3.png" alt="Avatar 3"
                                    style="width: 60px; height: 60px; border-radius: 50%; border: 3px solid transparent; transition: 0.3s;"
                                    class="avatar-option">
                            </label>
                            <label style="cursor: pointer; text-align: center;">
                                <input type="radio" name="avatar" value="avatar4.png" style="display: none;">
                                <img src="images/avatar4.png" alt="Avatar 4"
                                    style="width: 60px; height: 60px; border-radius: 50%; border: 3px solid transparent; transition: 0.3s;"
                                    class="avatar-option">
                            </label>
                        </div>
                        <style>
                            input[type="radio"]:checked+img.avatar-option {
                                transform: scale(1.1);
                            }

                            img.avatar-option:hover {
                                transform: scale(1.1);
                            }
                        </style>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="mot_de_passe" class="form-label">Mot de passe</label>
                        <div class="input-wrapper">
                            <span class="input-icon">🔒</span>
                            <input class="form-input" type="password" id="mot_de_passe" name="mot_de_passe"
                                placeholder="Minimum 8 caractères" autocomplete="new-password" required minlength="8" />
                            <button type="button" class="toggle-pw" id="toggle-pw1"
                                aria-label="Afficher/masquer">👁️</button>
                        </div>
                        <!-- Password strength bar -->
                        <div id="pw-strength-bar-wrap" style="margin-top:8px;display:none;">
                            <div id="pw-strength-bar"
                                style="height:6px;border-radius:4px;background:#e5e7eb;overflow:hidden;">
                                <div id="pw-strength-fill"
                                    style="height:100%;width:0;border-radius:4px;transition:width .3s,background .3s;">
                                </div>
                            </div>
                            <div style="display:flex;justify-content:space-between;margin-top:4px;">
                                <span id="pw-strength-label" style="font-size:.78rem;color:#6b7280;"></span>
                                <button type="button" id="btn-gen-pw"
                                    style="font-size:.78rem;color:var(--clr-primary,#16a34a);background:none;border:none;cursor:pointer;font-weight:600;padding:0;">⚡
                                    Générer</button>
                            </div>
                        </div>
                        <ul id="pw-rules"
                            style="margin:6px 0 0 16px;padding:0;font-size:.78rem;color:#6b7280;display:none;">
                            <li id="rule-len">Au moins 8 caractères</li>
                            <li id="rule-upper">Au moins une majuscule</li>
                            <li id="rule-num">Au moins un chiffre</li>
                            <li id="rule-special">Au moins un caractère spécial</li>
                        </ul>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label for="confirm_mot_de_passe" class="form-label">Confirmer le mot de passe</label>
                        <div class="input-wrapper">
                            <span class="input-icon">🔒</span>
                            <input class="form-input" type="password" id="confirm_mot_de_passe"
                                name="confirm_mot_de_passe" placeholder="••••••••" autocomplete="new-password"
                                required />
                            <button type="button" class="toggle-pw" id="toggle-pw2"
                                aria-label="Afficher/masquer la confirmation">👁️</button>
                        </div>
                        <p id="match-msg" style="font-size:.78rem;margin-top:4px;display:none;"></p>
                    </div>

                    <!-- Terms -->
                    <div class="form-group">
                        <label class="form-check" for="terms">
                            <input type="checkbox" id="terms" name="terms" required />
                            <span class="form-check-label">
                                J'accepte les
                                <a href="#" style="color:var(--clr-primary);font-weight:600;">conditions
                                    d'utilisation</a>
                                et la
                                <a href="#" style="color:var(--clr-primary);font-weight:600;">politique de
                                    confidentialité</a>.
                            </span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg" id="btn-register">
                        Créer mon compte
                    </button>
                </form>

                <!-- ── Google signup button ────────────────── -->
                <div class="divider">ou</div>

                <a href="google_auth.php" class="btn-google" id="btn-google-signup">
                  <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg"
                       alt="Google" width="20" height="20" />
                  S'inscrire avec Google
                </a>

                <div class="divider"></div>

                <p class="auth-footer-link">
                    Déjà un compte ? <a href="login.php">Se connecter</a>
                </p>
            </div>
        </main>
    </div>

    <script src="assets/js/password.strength.js"></script>
    <script src="assets/js/register.validate.js"></script>
    <script>
        // Prevent form resubmission on refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>

</html>
