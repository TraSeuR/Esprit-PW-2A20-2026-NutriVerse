<?php
// No-cache headers + session start
require_once __DIR__ . "/../../Controller/no_cache.php";

if (empty($_SESSION['reset_authorized_id'])) {
    header("Location: login.php");
    exit();
}

$id    = $_SESSION['reset_authorized_id'];
$email = $_SESSION['reset_authorized_email'];

require_once __DIR__ . "/../../Controller/csrf.php";

$errorMap = [
    'password_short'    => 'Le mot de passe doit contenir au moins 8 caractères.',
    'password_mismatch' => 'Les mots de passe ne correspondent pas.',
    'password_weak'     => 'Le mot de passe doit contenir au moins une majuscule, un chiffre et un caractère spécial.'
];
$errors = isset($_GET['errors']) ? explode(',', $_GET['errors']) : [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="NutriVerse – Choisissez votre nouveau mot de passe." />
    <title>Nouveau mot de passe – NutriVerse</title>
    <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
    <script>
        history.pushState(null, '', location.href);
        window.addEventListener('popstate', () => history.pushState(null, '', location.href));
    </script>

    <div class="auth-root">
        <main class="auth-form-side">
            <div class="auth-form-box">
                <div class="auth-form-header">
                    <h1>Nouveau mot de passe 🔒</h1>
                    <p>Choisissez un mot de passe fort pour votre compte <strong><?= htmlspecialchars($email) ?></strong>.</p>
                </div>

                <?php if (!empty($errors)): ?>
                <div style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;border-radius:8px;padding:12px;margin-bottom:16px;">
                    <ul style="margin:0 0 0 16px;padding:0;">
                        <?php foreach ($errors as $e): ?>
                            <li style="font-size:.9rem;"><?= htmlspecialchars($errorMap[$e] ?? 'Erreur inconnue') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <form action="do_reset_password.php" method="POST" novalidate id="form-new-password">
                    <?php echo csrf_field(); ?>

                    <div class="form-group">
                        <label for="mot_de_passe" class="form-label">Nouveau mot de passe</label>
                        <div class="input-wrapper">
                            <span class="input-icon">🔒</span>
                            <input class="form-input" type="password" id="mot_de_passe" name="mot_de_passe"
                                placeholder="Minimum 8 caractères" autocomplete="new-password" required minlength="8" />
                            <button type="button" class="toggle-pw" id="toggle-pw1" aria-label="Afficher/masquer">👁️</button>
                        </div>
                        <div id="pw-strength-bar-wrap" style="margin-top:8px;display:none;">
                            <div id="pw-strength-bar" style="height:6px;border-radius:4px;background:#e5e7eb;overflow:hidden;">
                                <div id="pw-strength-fill" style="height:100%;width:0;border-radius:4px;transition:width .3s,background .3s;"></div>
                            </div>
                            <div style="display:flex;justify-content:space-between;margin-top:4px;">
                                <span id="pw-strength-label" style="font-size:.78rem;color:#6b7280;"></span>
                                <button type="button" id="btn-gen-pw" style="font-size:.78rem;color:var(--clr-primary,#16a34a);background:none;border:none;cursor:pointer;font-weight:600;padding:0;">⚡ Générer</button>
                            </div>
                        </div>
                        <ul id="pw-rules" style="margin:6px 0 0 16px;padding:0;font-size:.78rem;color:#6b7280;display:none;">
                            <li id="rule-len">Au moins 8 caractères</li>
                            <li id="rule-upper">Au moins une majuscule</li>
                            <li id="rule-num">Au moins un chiffre</li>
                            <li id="rule-spec">Au moins un caractère spécial</li>
                        </ul>
                    </div>

                    <div class="form-group">
                        <label for="confirm_mot_de_passe" class="form-label">Confirmez le mot de passe</label>
                        <div class="input-wrapper">
                            <span class="input-icon">🔒</span>
                            <input class="form-input" type="password" id="confirm_mot_de_passe"
                                name="confirm_mot_de_passe" placeholder="••••••••" autocomplete="new-password" required />
                            <button type="button" class="toggle-pw" id="toggle-pw2" aria-label="Afficher/masquer la confirmation">👁️</button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg" id="btn-submit">
                        Changer mon mot de passe
                    </button>
                </form>

            </div>
        </main>
    </div>

    <script src="assets/js/password.strength.js"></script>
    <script>
        document.getElementById('form-new-password').addEventListener('submit', function (e) {
            let pwd = document.getElementById('mot_de_passe').value;
            let conf = document.getElementById('confirm_mot_de_passe').value;
            if (pwd !== conf) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas.');
                return;
            }
            if (pwd.length < 8 || !/[A-Z]/.test(pwd) || !/[0-9]/.test(pwd) || !/[\W_]/.test(pwd)) {
                e.preventDefault();
                alert('Le nouveau mot de passe est trop faible. Veuillez respecter les critères.');
            }
        });
    </script>
</body>
</html>

