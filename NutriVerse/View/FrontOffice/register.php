<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="NutriVerse – Créez votre compte et commencez votre voyage nutritionnel." />
    <title>Inscription – NutriVerse</title>

    <!-- Shared stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css" />

</head>

<body>

    <div class="auth-root">
        <!-- ── RIGHT FORM SIDE ─────────────────────────────────── -->
        <main class="auth-form-side">
            <div class="auth-form-box">

                <!-- Form header -->
                <div class="auth-form-header">
                    <h1>Créer un compte</h1>
                    <p>Remplissez le formulaire ci-dessous pour rejoindre la plateforme.</p>
                </div>

                <!-- Registration form -->
                <form id="form-register" action="add_user.php" method="POST" novalidate>

                    <!-- Nom + Prénom – two columns -->
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

                    </div><!-- /.form-grid-2 -->

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
                            <input class="form-input" type="password" id="mot_de_passe" name="mot_de_passe"
                                placeholder="Minimum 8 caractères" autocomplete="new-password" required minlength="8" />
                            <button type="button" class="toggle-pw" id="toggle-pw1"
                                aria-label="Afficher/masquer le mot de passe">👁️</button>
                        </div>
                    </div>

                    <!-- Confirm password -->
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
                    </div>

                    <!-- Accept terms -->
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

                    <!-- Submit -->
                    <button type="submit" class="btn btn-primary btn-block btn-lg" id="btn-register">
                        Créer mon compte 🚀
                    </button>

                </form>

                <!-- Divider -->
                <div class="divider">déjà inscrit ?</div>

                <!-- Link to login -->

                <p class="auth-footer-link">
                    Vous avez déjà un compte ?
                    <a href="login.php">Se connecter</a>
                </p>

            </div>
        </main>
    </div><!-- /.auth-root -->

    <!-- ── Minimal JS: password visibility toggles ───────────── -->
    <script>
        function makePwToggle(btnId, fieldId) {
            const btn = document.getElementById(btnId);
            const field = document.getElementById(fieldId);
            if (!btn || !field) return;
            btn.addEventListener('click', function () {
                const hidden = field.type === 'password';
                field.type = hidden ? 'text' : 'password';
                btn.textContent = hidden ? '🙈' : '👁️';
            });
        }
        makePwToggle('toggle-pw1', 'mot_de_passe');
        makePwToggle('toggle-pw2', 'confirm_mot_de_passe');

    </script>

</body>

</html>