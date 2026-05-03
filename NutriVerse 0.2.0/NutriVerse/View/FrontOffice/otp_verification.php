<?php
// No-cache headers + session start
require_once __DIR__ . "/../../Controller/no_cache.php";
require_once __DIR__ . "/../../Controller/csrf.php";
require_once __DIR__ . "/../../Controller/userC.php";

// ── Guard: must be in OTP step ────────────────────────────────
if (empty($_SESSION['email']) || ($_SESSION['step'] ?? '') !== 'otp') {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$id    = $_SESSION['pending_id'] ?? $_SESSION['otp_id'] ?? 0;
$name  = ($_SESSION['prenom'] ?? '') . ' ' . ($_SESSION['nom'] ?? '');
$msg   = '';
$error = '';

$userC = new userC();

// ── Handle OTP submission ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_code'])) {
    $result = $userC->processOtpVerify($id, $email); // redirects on success
    $error  = $result['error'] ?? '';
}

// ── Handle resend request ─────────────────────────────────────
if (isset($_GET['resend']) && $_GET['resend'] === '1') {
    $msg = $userC->processOtpResend($id, $email, $name);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vérification OTP – NutriVerse</title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
        .otp-wrap { display:flex; gap:10px; justify-content:center; margin:24px 0; }
        .otp-wrap input {
            width:52px; height:60px; text-align:center; font-size:1.6rem; font-weight:700;
            border:2px solid #d1d5db; border-radius:10px; outline:none;
            transition:border-color .2s;
        }
        .otp-wrap input:focus { border-color:#16a34a; box-shadow:0 0 0 3px rgba(22,163,74,.15); }
    </style>
</head>
<body>
    <script>
        // 🔙 FEATURE: OTP Page BACK Behavior (Reinforced)
        // If user enters OTP page and clicks BACK -> Always redirect to login.php
        history.pushState(null, '', location.href);
        window.addEventListener('popstate', function() {
            window.location.href = 'login.php';
        });
    </script>

    <div class="auth-root">
        <main class="auth-form-side">
            <div class="auth-form-box">
                <div class="auth-form-header">
                    <h1>Vérification de sécurité 🛡️</h1>
                    <?php if (isset($_GET['pending'])): ?>
                    <p style="color:#b45309; background:#fef3c7; padding:10px; border-radius:8px; margin-bottom:15px; font-weight:600; font-size:.9rem;">
                        ⚠️ Votre compte n'est pas encore vérifié. Veuillez entrer le code envoyé par e-mail pour l'activer.
                    </p>
                    <?php endif; ?>
                    <p>Saisissez le code à 6 chiffres envoyé à<br>
                       <strong><?= htmlspecialchars($email) ?></strong></p>
                </div>

                <?php if ($error): ?>
                <div style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;border-radius:8px;padding:12px;margin-bottom:16px;text-align:center;">
                    <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>

                <?php if ($msg): ?>
                <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;border-radius:8px;padding:12px;margin-bottom:16px;text-align:center;">
                    ✅ <?= htmlspecialchars($msg) ?>
                </div>
                <?php endif; ?>

                <form id="form-otp" action="otp_verification.php" method="POST" novalidate>
                    <?php echo csrf_field(); ?>

                    <div class="otp-wrap" id="otp-boxes">
                        <input type="text" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]" autocomplete="one-time-code" />
                        <input type="text" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]" />
                        <input type="text" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]" />
                        <input type="text" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]" />
                        <input type="text" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]" />
                        <input type="text" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]" />
                    </div>
                    <input type="hidden" name="verify_code" id="verify_code_hidden" />

                    <button type="submit" class="btn btn-primary btn-block btn-lg" id="btn-verify">
                        Vérifier le code ✅
                    </button>
                </form>

                <div class="divider">ou</div>

                <p class="auth-footer-link">
                    Code non reçu ?
                    <a href="otp_verification.php?resend=1">Renvoyer un code</a>
                </p>

                <p class="auth-footer-link" style="margin-top: 20px;">
                    <a href="login.php" style="color: #6b7280; font-size: 0.9rem;">Retour à la connexion</a>
                </p>
            </div>
        </main>
    </div>

    <script>
    const digits = document.querySelectorAll('.otp-digit');
    const hidden = document.getElementById('verify_code_hidden');

    digits.forEach((box, i) => {
        box.addEventListener('input', () => {
            box.value = box.value.replace(/\D/g, '').slice(-1);
            if (box.value && i < digits.length - 1) digits[i + 1].focus();
            hidden.value = [...digits].map(d => d.value).join('');
        });
        box.addEventListener('keydown', e => {
            if (e.key === 'Backspace' && !box.value && i > 0) digits[i - 1].focus();
        });
        box.addEventListener('paste', e => {
            e.preventDefault();
            const pasted = (e.clipboardData.getData('text') || '').replace(/\D/g, '').slice(0, 6);
            [...pasted].forEach((ch, j) => { if (digits[j]) digits[j].value = ch; });
            hidden.value = [...digits].map(d => d.value).join('');
            if (digits[pasted.length - 1]) digits[pasted.length - 1].focus();
        });
    });

    document.getElementById('form-otp').addEventListener('submit', e => {
        hidden.value = [...digits].map(d => d.value).join('');
        if (hidden.value.length !== 6) {
            e.preventDefault();
            alert('Veuillez saisir les 6 chiffres du code.');
        }
    });
    </script>
</body>
</html>

