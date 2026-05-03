<?php
// No-cache headers + session start
require_once __DIR__ . "/../../Controller/no_cache.php";
require_once __DIR__ . "/../../Controller/csrf.php";

// Must arrive here from register.php flow — if no session data, redirect back
if (empty($_SESSION['pending_user']) || ($_SESSION['step'] ?? '') !== 'profile') {
    header("Location: register.php");
    exit();
}

// Parse errors
$errors = [];
if (!empty($_GET['errors'])) {
    $errors = explode(',', $_GET['errors']);
}
$errorMap = [
    'telephone_required' => 'Le téléphone est requis.',
    'date_required'      => 'La date de naissance est requise.',
    'sexe_required'      => 'Le sexe est requis.',
    'poids_required'     => 'Le poids est requis (> 0).',
    'taille_required'    => 'La taille est requise (> 0).',
    'objectif_required'  => "L'objectif nutritionnel est requis.",
    'preference_required'=> 'La préférence alimentaire est requise.',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="NutriVerse – Complétez votre profil nutritionnel." />
    <title>Profil – NutriVerse</title>
    <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
    <!-- Prevent back navigation to register step 1 -->
    <script>
        history.pushState(null, '', location.href);
        window.addEventListener('popstate', () => history.pushState(null, '', location.href));
    </script>

    <div class="auth-root">
        <main class="auth-form-side">
            <div class="auth-form-box">

                <div class="auth-form-header">
                    <h1>Complétez votre profil</h1>
                    <p>Pour personnaliser votre expérience, aidez-nous à mieux vous connaître.</p>
                </div>

                <!-- Step indicator -->
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:24px;">
                    <div style="width:28px;height:28px;border-radius:50%;background:#e5e7eb;display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:600;color:#9ca3af;">✓</div>
                    <div style="flex:1;height:3px;background:linear-gradient(to right,#16a34a,#16a34a);border-radius:2px;"></div>
                    <div style="width:28px;height:28px;border-radius:50%;background:#16a34a;display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;color:#fff;">2</div>
                    <div style="flex:1;height:3px;background:#e5e7eb;border-radius:2px;"></div>
                    <div style="width:28px;height:28px;border-radius:50%;background:#e5e7eb;display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:600;color:#9ca3af;">3</div>
                </div>

                <?php if (!empty($errors)): ?>
                <div style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;border-radius:8px;padding:14px;margin-bottom:20px;">
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

                <form id="form-profile" action="add_profile.php" method="POST" novalidate>
                    <?php echo csrf_field(); ?>

                    <!-- Téléphone -->
                    <div class="form-group">
                        <label for="telephone" class="form-label">Téléphone <span style="color:#ef4444;">*</span></label>
                        <div class="input-wrapper">
                            <span class="input-icon">📱</span>
                            <input class="form-input" type="tel" id="telephone" name="telephone"
                                placeholder="+216 00 000 000" required />
                        </div>
                    </div>

                    <!-- Date + Sexe -->
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="date_naissance" class="form-label">Date de naissance <span style="color:#ef4444;">*</span></label>
                            <div class="input-wrapper">
                                <span class="input-icon">📅</span>
                                <input class="form-input" type="date" id="date_naissance" name="date_naissance" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="sexe" class="form-label">Sexe <span style="color:#ef4444;">*</span></label>
                            <div class="input-wrapper">
                                <span class="input-icon">🚻</span>
                                <select class="form-input" id="sexe" name="sexe" required>
                                    <option value="" disabled selected>Sélectionnez...</option>
                                    <option value="Homme">Homme</option>
                                    <option value="Femme">Femme</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Poids + Taille -->
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="poids" class="form-label">Poids (kg) <span style="color:#ef4444;">*</span></label>
                            <div class="input-wrapper">
                                <span class="input-icon">⚖️</span>
                                <input class="form-input" type="number" step="0.1" min="1" id="poids" name="poids"
                                    placeholder="ex: 75.5" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="taille" class="form-label">Taille (cm) <span style="color:#ef4444;">*</span></label>
                            <div class="input-wrapper">
                                <span class="input-icon">📏</span>
                                <input class="form-input" type="number" step="0.1" min="1" id="taille" name="taille"
                                    placeholder="ex: 180" required />
                            </div>
                        </div>
                    </div>

                    <!-- Objectif + Préférence -->
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="objectif_nutritionnel" class="form-label">Objectif <span style="color:#ef4444;">*</span></label>
                            <div class="input-wrapper">
                                <span class="input-icon">🎯</span>
                                <select class="form-input" id="objectif_nutritionnel" name="objectif_nutritionnel" required>
                                    <option value="" disabled selected>Sélectionnez...</option>
                                    <option value="Perte de poids">Perte de poids</option>
                                    <option value="Maintien">Maintien</option>
                                    <option value="Prise de masse">Prise de masse</option>
                                    <option value="Amélioration santé">Amélioration santé</option>
                                    <option value="Augmenter l'énergie">Augmenter l'énergie</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="preference_alimentaire" class="form-label">Préférence <span style="color:#ef4444;">*</span></label>
                            <div class="input-wrapper">
                                <span class="input-icon">🍽️</span>
                                <select class="form-input" id="preference_alimentaire" name="preference_alimentaire" required>
                                    <option value="" disabled selected>Sélectionnez...</option>
                                    <option value="Omnivore">Omnivore</option>
                                    <option value="Végétarien">Végétarien</option>
                                    <option value="Vegan">Vegan</option>
                                    <option value="Pescétarien">Pescétarien</option>
                                    <option value="Keto">Keto</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Allergies -->
                    <div class="form-group">
                        <label for="allergies" class="form-label">Allergies ou intolérances <span style="color:#9ca3af;font-size:.8rem;">(Optionnel)</span></label>
                        <div class="input-wrapper" style="align-items:flex-start;">
                            <span class="input-icon" style="padding-top:10px;">⚠️</span>
                            <textarea class="form-input" id="allergies" name="allergies"
                                placeholder="Indiquez vos allergies si vous en avez..." rows="3"
                                style="padding-top:10px;resize:vertical;"></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg" id="btn-save">
                        Enregistrer mon profil 🚀
                    </button>
                </form>

                <!-- NOTE: "Skip" link removed — profile is mandatory -->

            </div>
        </main>
    </div>

    <script src="assets/js/registerP.validate.js"></script>
</body>
</html>
