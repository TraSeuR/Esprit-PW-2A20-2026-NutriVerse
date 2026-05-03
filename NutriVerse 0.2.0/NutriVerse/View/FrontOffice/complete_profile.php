<?php
/**
 * complete_profile.php  (View/FrontOffice)
 * ──────────────────────────────────────────────────────
 * Shown to new Google users after first-time sign-in.
 * They must fill in their missing profile information.
 */

require_once __DIR__ . '/../../Controller/auth_check.php';
require_once __DIR__ . '/../../Controller/csrf.php';

// Only Google new users should see this page
if (!isset($_SESSION['google_new_user'])) {
    header('Location: index.php');
    exit();
}

// Show any errors passed back from complete_profile_action.php
$errors = [];
if (!empty($_GET['errors'])) {
    $errors = explode(',', $_GET['errors']);
}
$errorMap = [
    'phone_required'     => 'Le numéro de téléphone est requis.',
    'birthdate_required' => 'La date de naissance est requise.',
    'gender_required'    => 'Le sexe est requis.',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="NutriVerse – Complétez votre profil." />
    <title>Compléter votre profil – NutriVerse</title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
        /* ── Google badge shown at the top ── */
        .google-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 24px;
            font-size: 0.9rem;
            color: #166534;
        }
        .google-badge img {
            width: 22px;
            height: 22px;
        }
    </style>
</head>
<body>
<div class="auth-root">
    <main class="auth-form-side">
        <div class="auth-form-box">

            <!-- Header -->
            <div class="auth-form-header">
                <h1>Complétez votre profil 📋</h1>
                <p>Quelques informations supplémentaires pour personnaliser votre expérience NutriVerse.</p>
            </div>

            <!-- Google badge -->
            <div class="google-badge">
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" />
                Connecté avec Google en tant que
                <strong><?= htmlspecialchars($_SESSION['email'] ?? '') ?></strong>
            </div>

            <!-- Error messages -->
            <?php if (!empty($errors)): ?>
            <div style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;border-radius:8px;padding:12px;margin-bottom:16px;">
                <ul style="margin:0 0 0 16px; padding:0;">
                    <?php foreach ($errors as $e): ?>
                        <li style="font-size:.9rem;"><?= htmlspecialchars($errorMap[$e] ?? 'Erreur inconnue.') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <!-- Profile completion form -->
            <form action="complete_profile_action.php" method="POST" novalidate>
                <?php echo csrf_field(); ?>

                <!-- Phone -->
                <div class="form-group">
                    <label for="telephone" class="form-label">Téléphone <span style="color:#dc2626;">*</span></label>
                    <div class="input-wrapper">
                        <span class="input-icon">📞</span>
                        <input class="form-input" type="tel" id="telephone" name="telephone"
                            placeholder="+216 XX XXX XXX" required />
                    </div>
                </div>

                <!-- Date of birth -->
                <div class="form-group">
                    <label for="date_naissance" class="form-label">Date de naissance <span style="color:#dc2626;">*</span></label>
                    <div class="input-wrapper">
                        <span class="input-icon">📅</span>
                        <input class="form-input" type="date" id="date_naissance" name="date_naissance" required />
                    </div>
                </div>

                <!-- Gender -->
                <div class="form-group">
                    <label for="sexe" class="form-label">Sexe <span style="color:#dc2626;">*</span></label>
                    <select class="form-input" id="sexe" name="sexe" required style="padding-left:16px;">
                        <option value="">Choisir…</option>
                        <option value="Homme">Homme</option>
                        <option value="Femme">Femme</option>
                    </select>
                </div>

                <!-- Weight (optional) -->
                <div class="form-group">
                    <label for="poids" class="form-label">Poids (kg)</label>
                    <div class="input-wrapper">
                        <span class="input-icon">⚖️</span>
                        <input class="form-input" type="number" id="poids" name="poids"
                            placeholder="Ex: 70" step="0.1" min="20" max="300" />
                    </div>
                </div>

                <!-- Height (optional) -->
                <div class="form-group">
                    <label for="taille" class="form-label">Taille (cm)</label>
                    <div class="input-wrapper">
                        <span class="input-icon">📏</span>
                        <input class="form-input" type="number" id="taille" name="taille"
                            placeholder="Ex: 175" step="0.1" min="50" max="250" />
                    </div>
                </div>

                <!-- Nutritional goal (optional) -->
                <div class="form-group">
                    <label for="objectif_nutritionnel" class="form-label">Objectif nutritionnel</label>
                    <select class="form-input" id="objectif_nutritionnel" name="objectif_nutritionnel"
                            style="padding-left:16px;">
                        <option value="">Choisir…</option>
                        <option value="Perte de poids">Perte de poids</option>
                        <option value="Prise de masse">Prise de masse</option>
                        <option value="Maintien">Maintien</option>
                        <option value="Amélioration santé">Amélioration santé</option>
                        <option value="Augmenter l'énergie">Augmenter l'énergie</option>
                    </select>
                </div>

                <!-- Diet preference (optional) -->
                <div class="form-group">
                    <label for="preference_alimentaire" class="form-label">Préférence alimentaire</label>
                    <select class="form-input" id="preference_alimentaire" name="preference_alimentaire"
                            style="padding-left:16px;">
                        <option value="">Choisir…</option>
                        <option value="Omnivore">Omnivore</option>
                        <option value="Végétarien">Végétarien</option>
                        <option value="Vegan">Vegan</option>
                        <option value="Pescétarien">Pescétarien</option>
                        <option value="Keto">Keto</option>
                    </select>
                </div>

                <!-- Allergies (optional) -->
                <div class="form-group">
                    <label for="allergies" class="form-label">Allergies</label>
                    <div class="input-wrapper">
                        <span class="input-icon">⚠️</span>
                        <input class="form-input" type="text" id="allergies" name="allergies"
                            placeholder="Ex: Gluten, Lactose (laisser vide si aucune)" />
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    Enregistrer mon profil ✅
                </button>
            </form>

        </div>
    </main>
</div>
</body>
</html>
