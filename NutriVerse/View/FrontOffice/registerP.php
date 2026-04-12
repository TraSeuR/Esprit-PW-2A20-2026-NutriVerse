<?php
// On démarre la session si on doit récupérer un id_user depuis la session, sinon on le récupère via $_GET
$id_user = isset($_GET['id_user']) ? $_GET['id_user'] : (isset($_SESSION['id_user']) ? $_SESSION['id_user'] : '');
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="NutriVerse – Créez votre profil et commencez votre voyage nutritionnel." />
    <title>Profil – NutriVerse</title>

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
                    <h1>Complétez votre profil</h1>
                    <p>Pour personnaliser votre expérience, aidez-nous à mieux vous connaître.</p>
                </div>

                <!-- Registration form -->
                <form id="form-profile" action="add_profile.php" method="POST" novalidate>

                    <!-- ID USER (Cle Etrangere) -->
                    <input type="hidden" name="id_user" value="<?= htmlspecialchars($id_user) ?>" />

                    <!-- Téléphone -->
                    <div class="form-group">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <div class="input-wrapper">
                            <span class="input-icon">📱</span>
                            <input class="form-input" type="tel" id="telephone" name="telephone" placeholder="+216 00 000 000"
                                required />
                        </div>
                    </div>

                    <!-- Date de naissance + Sexe -->
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="date_naissance" class="form-label">Date de naissance</label>
                            <div class="input-wrapper">
                                <!-- Calendar icon as emoji or feather, sticking to emoji since style uses them -->
                                <span class="input-icon">📅</span>
                                <input class="form-input" type="date" id="date_naissance" name="date_naissance" required />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="sexe" class="form-label">Sexe</label>
                            <div class="input-wrapper">
                                <span class="input-icon">🚻</span>
                                <select class="form-input" id="sexe" name="sexe" required>
                                    <option value="" disabled selected>Sélectionnez...</option>
                                    <option value="Homme">Homme</option>
                                    <option value="Femme">Femme</option>
                                </select>
                            </div>
                        </div>
                    </div><!-- /.form-grid-2 -->

                    <!-- Poids + Taille -->
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="poids" class="form-label">Poids (kg)</label>
                            <div class="input-wrapper">
                                <span class="input-icon">⚖️</span>
                                <input class="form-input" type="number" step="0.1" id="poids" name="poids" placeholder="ex: 75.5"
                                    required />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="taille" class="form-label">Taille (cm)</label>
                            <div class="input-wrapper">
                                <span class="input-icon">📏</span>
                                <input class="form-input" type="number" step="0.1" id="taille" name="taille" placeholder="ex: 180"
                                    required />
                            </div>
                        </div>
                    </div><!-- /.form-grid-2 -->

                    <!-- Objectif Nutritionnel + Préférence Alimentaire -->
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="objectif_nutritionnel" class="form-label">Objectif</label>
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
                            <label for="preference_alimentaire" class="form-label">Préférence</label>
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
                    </div><!-- /.form-grid-2 -->

                    <!-- Allergies -->
                    <div class="form-group">
                        <label for="allergies" class="form-label">Allergies ou intolérances (Optionnel)</label>
                        <div class="input-wrapper" style="align-items: flex-start;">
                            <span class="input-icon" style="padding-top:10px;">⚠️</span>
                            <!-- Using textarea for allergies since it's type TEXT -->
                            <textarea class="form-input" id="allergies" name="allergies" 
                                placeholder="Indiquez vos allergies si vous en avez..." rows="3" style="padding-top:10px; resize:vertical;"></textarea>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-primary btn-block btn-lg" id="btn-save">
                        Enregistrer mon profil 🚀
                    </button>

                </form>

                <!-- Divider -->
                <div class="divider">ou</div>

                <p class="auth-footer-link" style="text-align:center; margin-top:20px;">
                    <a href="index.php">Passer cette étape pour le moment</a>
                </p>

            </div>
        </main>
    </div><!-- /.auth-root -->

</body>

</html>
