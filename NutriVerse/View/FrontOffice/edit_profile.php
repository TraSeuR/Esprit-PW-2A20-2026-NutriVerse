<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

require_once "../../Controller/userC.php";
require_once "../../Controller/profileC.php";

$userC = new userC();
$profileC = new profileC();

$user = $userC->getUserById($_SESSION['id_user']);
$prof = $profileC->getProfileById($_SESSION['id_user']);
if (!$prof) {
    $prof = []; // Default empty if no profile exists yet
}
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
            max-width: 800px; /* Make it wider for more fields */
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
                    <input type="hidden" name="id_user" value="<?php echo $_SESSION['id_user']; ?>">

                    <h3 style="margin-bottom: 15px; color: var(--primary);">Identité</h3>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="prenom" class="form-label">Prénom</label>
                            <div class="input-wrapper">
                                <input class="form-input" style="padding-left: 15px;" type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom'] ?? ''); ?>" required />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="nom" class="form-label">Nom</label>
                            <div class="input-wrapper">
                                <input class="form-input" style="padding-left: 15px;" type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom'] ?? ''); ?>" required />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Adresse e-mail</label>
                            <div class="input-wrapper">
                                <input class="form-input" style="padding-left: 15px;" type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mot_de_passe" class="form-label">Nouveau mot de passe</label>
                            <div class="input-wrapper">
                                <input class="form-input" style="padding-left: 15px;" type="password" id="mot_de_passe" name="mot_de_passe" placeholder="Laisser vide = inchangé" />
                            </div>
                        </div>
                    </div>

                    <h3 style="margin-top: 30px; margin-bottom: 15px; color: var(--primary);">Informations personnelles</h3>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <div class="input-wrapper">
                                <input class="form-input" style="padding-left: 15px;" type="tel" id="telephone" name="telephone" value="<?php echo htmlspecialchars($prof['telephone'] ?? ''); ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="date_naissance" class="form-label">Date de naissance</label>
                            <div class="input-wrapper">
                                <input class="form-input" style="padding-left: 15px;" type="date" id="date_naissance" name="date_naissance" value="<?php echo htmlspecialchars($prof['date_naissance'] ?? ''); ?>" />
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
                                <input class="form-input" style="padding-left: 15px;" type="number" id="poids" name="poids" step="0.1" value="<?php echo htmlspecialchars($prof['poids'] ?? ''); ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="taille" class="form-label">Taille (cm)</label>
                            <div class="input-wrapper">
                                <input class="form-input" style="padding-left: 15px;" type="number" id="taille" name="taille" step="0.1" value="<?php echo htmlspecialchars($prof['taille'] ?? ''); ?>" />
                            </div>
                        </div>
                    </div>

                    <h3 style="margin-top: 30px; margin-bottom: 15px; color: var(--primary);">Profil nutritionnel</h3>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="objectif_nutritionnel" class="form-label">Objectif nutritionnel</label>
                            <div class="input-wrapper">
                                <select class="form-input" style="padding-left: 15px;" id="objectif_nutritionnel" name="objectif_nutritionnel">
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
                                <select class="form-input" style="padding-left: 15px;" id="preference_alimentaire" name="preference_alimentaire">
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
                                <input class="form-input" style="padding-left: 15px;" type="text" id="allergies" name="allergies" placeholder="Ex: Gluten, Lactose..." value="<?php echo htmlspecialchars($prof['allergies'] ?? ''); ?>" />
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-primary btn-block btn-lg" id="btn-edit-profile" style="margin-top: 20px;">
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

</body>
</html>
