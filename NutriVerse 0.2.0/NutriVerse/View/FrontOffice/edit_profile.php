<?php
// Auth guard: checks session + sends no-cache headers
require_once __DIR__ . "/../../Controller/auth_check.php";

require_once "../../Controller/csrf.php";
require_once "../../Controller/userC.php";
require_once "../../Controller/profileC.php";

$userC = new userC();
$profileC = new profileC();

$user = $userC->getUserById($_SESSION['id_user']);
$prof = $profileC->getProfileById($_SESSION['id_user']);
if (!$prof) {
    $prof = [];
}

// Parse server-side errors and success flag
$errors = [];
if (!empty($_GET['errors'])) {
    $errMap = [
        'nom_required' => 'Le nom est requis.',
        'prenom_required' => 'Le prénom est requis.',
        'email_invalid' => 'Adresse e-mail invalide.',
        'email_taken' => 'Cette adresse e-mail est déjà utilisée.',
    ];
    foreach (explode(',', $_GET['errors']) as $code) {
        if (isset($errMap[$code]))
            $errors[] = $errMap[$code];
    }
}
$success = isset($_GET['success']) && $_GET['success'] === '1';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="NutriVerse – Éditez votre profil complet." />
    <title>Éditer Profil – NutriVerse</title>

    <!-- Shared stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
        .auth-form-box {
            max-width: 800px;
            /* Make it wider for more fields */
        }
    </style>
</head>

<body>

    <div class="auth-root">
        <main class="auth-form-side" style="padding: 40px; align-items: start;">
            <div class="auth-form-box" style="width: 100%;">

                <!-- Form header -->
                <div class="auth-form-header">
                    <h1>Modifier mon profil</h1>
                    <p>Mettez à jour vos informations personnelles et votre profil nutritionnel.</p>
                </div>

                <!-- Edit Profile form -->
                <form id="form-edit-profile" action="update_profile.php" method="POST" novalidate>
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="id_user" value="<?php echo $_SESSION['id_user']; ?>">

                    <?php if ($success): ?>
                        <div
                            style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;border-radius:8px;padding:12px;margin-bottom:20px;text-align:center;font-weight:500;">
                            ✅ Profil mis à jour avec succès !
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($errors)): ?>
                        <div
                            style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;border-radius:8px;padding:12px;margin-bottom:20px;">
                            <ul style="margin:0 0 0 16px;padding:0;">
                                    <?php foreach ($errors as $e): ?>
                                    <li style="font-size:.9rem;"><?= htmlspecialchars($e) ?></li>
                                    <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <h3 style="margin-bottom: 15px; color: var(--primary);">Identité</h3>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="prenom" class="form-label">Prénom</label>
                            <div class="input-wrapper">
                                <input class="form-input" style="padding-left: 15px;" type="text" id="prenom"
                                    name="prenom" value="<?php echo htmlspecialchars($user['prenom'] ?? ''); ?>"
                                    required />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="nom" class="form-label">Nom</label>
                            <div class="input-wrapper">
                                <input class="form-input" style="padding-left: 15px;" type="text" id="nom" name="nom"
                                    value="<?php echo htmlspecialchars($user['nom'] ?? ''); ?>" required />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Adresse e-mail</label>
                            <div class="input-wrapper">
                                <input class="form-input" style="padding-left: 15px;" type="email" id="email"
                                    name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                                    required />
                            </div>
                        </div>

                        <!-- Avatar Selection -->
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label class="form-label">Avatar</label>
                            <div style="display: flex; gap: 15px; justify-content: flex-start; flex-wrap: wrap;">
                                <?php $currentAvatar = $user['avatar'] ?? 'avatar1.png'; ?>
                                <?php foreach (['avatar1.png', 'avatar2.png', 'avatar3.png', 'avatar4.png'] as $av): ?>
                                    <label style="cursor: pointer; text-align: center;">
                                        <input type="radio" name="avatar" value="<?= $av ?>" <?= $currentAvatar === $av ? 'checked' : '' ?> style="display: none;">
                                        <img src="images/<?= $av ?>" alt="Avatar"
                                            style="width: 50px; height: 50px; border-radius: 50%; border: 3px solid transparent; transition: 0.3s;"
                                            class="avatar-option">
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <style>
                                input[type="radio"]:checked+img.avatar-option {
                                    border-color: var(--clr-primary, #16a34a);
                                    transform: scale(1.1);
                                }

                                img.avatar-option:hover {
                                    transform: scale(1.1);
                                }
                            </style>
                        </div>

                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="mot_de_passe" class="form-label">Nouveau mot de passe</label>
                            <div class="input-wrapper" style="position:relative;">
                                <input class="form-input" style="padding-left: 15px;" type="password" id="mot_de_passe"
                                    name="mot_de_passe" placeholder="Laisser vide = inchangé" />
                                <button type="button" class="toggle-pw" id="toggle-pw1" aria-label="Afficher/masquer"
                                    style="position:absolute; right:10px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer;">👁️</button>
                            </div>
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
                                <li id="rule-spec">Au moins un caractère spécial</li>
                            </ul>
                        </div>
                    </div>

                    <h3 style="margin-top: 30px; margin-bottom: 15px; color: var(--primary);">Informations personnelles
                    </h3>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <div class="input-wrapper">
                                <input class="form-input" style="padding-left: 15px;" type="tel" id="telephone"
                                    name="telephone"
                                    value="<?php echo htmlspecialchars($prof['telephone'] ?? ''); ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="date_naissance" class="form-label">Date de naissance</label>
                            <div class="input-wrapper">
                                <input class="form-input" style="padding-left: 15px;" type="date" id="date_naissance"
                                    name="date_naissance"
                                    value="<?php echo htmlspecialchars($prof['date_naissance'] ?? ''); ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="sexe" class="form-label">Sexe</label>
                            <div class="input-wrapper">
                                <select class="form-input" style="padding-left: 15px;" id="sexe" name="sexe">
                                    <option value="">Choisir...</option>
                                    <option value="Homme" <?php echo (($prof['sexe'] ?? '') === 'Homme') ? 'selected' : ''; ?>>Homme</option>
                                    <option value="Femme" <?php echo (($prof['sexe'] ?? '') === 'Femme') ? 'selected' : ''; ?>>Femme</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="poids" class="form-label">Poids (kg)</label>
                            <div class="input-wrapper">
                                <input class="form-input" style="padding-left: 15px;" type="number" id="poids"
                                    name="poids" step="0.1"
                                    value="<?php echo htmlspecialchars($prof['poids'] ?? ''); ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="taille" class="form-label">Taille (cm)</label>
                            <div class="input-wrapper">
                                <input class="form-input" style="padding-left: 15px;" type="number" id="taille"
                                    name="taille" step="0.1"
                                    value="<?php echo htmlspecialchars($prof['taille'] ?? ''); ?>" />
                            </div>
                        </div>
                    </div>

                    <h3 style="margin-top: 30px; margin-bottom: 15px; color: var(--primary);">Profil nutritionnel</h3>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="objectif_nutritionnel" class="form-label">Objectif nutritionnel</label>
                            <div class="input-wrapper">
                                <select class="form-input" style="padding-left: 15px;" id="objectif_nutritionnel"
                                    name="objectif_nutritionnel">
                                    <option value="">Choisir...</option>
                                    <option value="Perte de poids" <?php echo (($prof['objectif_nutritionnel'] ?? '') === 'Perte de poids') ? 'selected' : ''; ?>>Perte de poids</option>
                                    <option value="Prise de masse" <?php echo (($prof['objectif_nutritionnel'] ?? '') === 'Prise de masse') ? 'selected' : ''; ?>>Prise de masse</option>
                                    <option value="Maintien" <?php echo (($prof['objectif_nutritionnel'] ?? '') === 'Maintien') ? 'selected' : ''; ?>>Maintien</option>
                                    <option value="Amélioration santé" <?php echo (($prof['objectif_nutritionnel'] ?? '') === 'Amélioration santé') ? 'selected' : ''; ?>>Amélioration santé</option>
                                    <option value="Augmenter l'énergie" <?php echo (($prof['objectif_nutritionnel'] ?? '') === "Augmenter l'énergie") ? 'selected' : ''; ?>>Augmenter l'énergie</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="preference_alimentaire" class="form-label">Préférence alimentaire</label>
                            <div class="input-wrapper">
                                <select class="form-input" style="padding-left: 15px;" id="preference_alimentaire"
                                    name="preference_alimentaire">
                                    <option value="">Choisir...</option>
                                    <option value="Omnivore" <?php echo (($prof['preference_alimentaire'] ?? '') === 'Omnivore') ? 'selected' : ''; ?>>Omnivore</option>
                                    <option value="Végétarien" <?php echo (($prof['preference_alimentaire'] ?? '') === 'Végétarien') ? 'selected' : ''; ?>>Végétarien</option>
                                    <option value="Vegan" <?php echo (($prof['preference_alimentaire'] ?? '') === 'Vegan') ? 'selected' : ''; ?>>Vegan</option>
                                    <option value="Pescétarien" <?php echo (($prof['preference_alimentaire'] ?? '') === 'Pescétarien') ? 'selected' : ''; ?>>Pescétarien</option>
                                    <option value="Keto" <?php echo (($prof['preference_alimentaire'] ?? '') === 'Keto') ? 'selected' : ''; ?>>Keto</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="allergies" class="form-label">Allergies</label>
                            <div class="input-wrapper">
                                <input class="form-input" style="padding-left: 15px;" type="text" id="allergies"
                                    name="allergies" placeholder="Ex: Gluten, Lactose..."
                                    value="<?php echo htmlspecialchars($prof['allergies'] ?? ''); ?>" />
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-primary btn-block btn-lg" id="btn-edit-profile"
                        style="margin-top: 20px;">
                        Enregistrer les modifications
                    </button>

                </form>

                <div class="divider">ou</div>

                <p class="auth-footer-link">
                    <a href="index.php">Retour à l'accueil</a>
                </p>

            </div>
        </main>
    </div>

    <!-- JS pour la force du mot de passe et validation -->
    <script src="assets/js/password.strength.js"></script>
    <script src="assets/js/validation.js"></script>
    <script>
        // Prevent form resubmission on refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>

</html>
