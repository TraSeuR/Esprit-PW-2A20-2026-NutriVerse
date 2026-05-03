<?php

require_once __DIR__ . "/../config.php";

class profileC
{
    public function listProfile()
    {
        $db = config::getConnexion();
        try {
            $list = $db->query("SELECT * FROM profile");
            return $list;
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }
    public function addProfile($profile)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("INSERT INTO profile (telephone, date_naissance, sexe, poids, taille, objectif_nutritionnel, preference_alimentaire, allergies, id_user) VALUES (:telephone, :date_naissance, :sexe, :poids, :taille, :objectif_nutritionnel, :preference_alimentaire, :allergies, :id_user)");
            $req->execute([
                'telephone' => $profile->getTelephone(),
                'date_naissance' => $profile->getDateNaissance(),
                'sexe' => $profile->getSexe(),
                'poids' => $profile->getPoids(),
                'taille' => $profile->getTaille(),
                'objectif_nutritionnel' => $profile->getObjectifNutritionnel(),
                'preference_alimentaire' => $profile->getPreferenceAlimentaire(),
                'allergies' => $profile->getAllergies(),
                'id_user' => $profile->getIdUser()
            ]);
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }
    public function delateProfile($id_user)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("DELETE FROM profile WHERE id_user = :id_user");
            $req->execute([
                'id_user' => $id_user
            ]);
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }
    public function getProfileById($id_user)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("SELECT * FROM profile WHERE id_user = :id_user");
            $req->execute([
                'id_user' => $id_user
            ]);
            return $req->fetch();
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }
    public function updateProfile($profile, $id_user)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("UPDATE profile SET telephone = :telephone, date_naissance = :date_naissance, sexe = :sexe, poids = :poids, taille = :taille, objectif_nutritionnel = :objectif_nutritionnel, preference_alimentaire = :preference_alimentaire, allergies = :allergies, id_user = :id_user WHERE id_user = :id_user");
            $req->execute([
                'telephone' => $profile->getTelephone(),
                'date_naissance' => $profile->getDateNaissance(),
                'sexe' => $profile->getSexe(),
                'poids' => $profile->getPoids(),
                'taille' => $profile->getTaille(),
                'objectif_nutritionnel' => $profile->getObjectifNutritionnel(),
                'preference_alimentaire' => $profile->getPreferenceAlimentaire(),
                'allergies' => $profile->getAllergies(),
                'id_user' => $profile->getIdUser()
            ]);
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    // ─── PROCESS ADD PROFILE (Registration Step 2) ──────────────────────────
    public function processAddProfile()
    {
        require_once __DIR__ . "/csrf.php";
        require_once __DIR__ . "/../Model/user.php";
        require_once __DIR__ . "/../Model/profile.php";
        require_once __DIR__ . "/userC.php";
        require_once __DIR__ . "/Mailer.php";
        csrf_verify();

        // Profile fields
        $telephone              = trim($_POST['telephone'] ?? '');
        $date_naissance         = trim($_POST['date_naissance'] ?? '');
        $sexe                   = trim($_POST['sexe'] ?? '');
        $poids                  = isset($_POST['poids'])  && $_POST['poids']  !== '' ? (float)$_POST['poids']  : 0.0;
        $taille                 = isset($_POST['taille']) && $_POST['taille'] !== '' ? (float)$_POST['taille'] : 0.0;
        $objectif_nutritionnel  = trim($_POST['objectif_nutritionnel'] ?? '');
        $preference_alimentaire = trim($_POST['preference_alimentaire'] ?? '');
        $allergies              = trim($_POST['allergies'] ?? '');

        $errors = [];
        if (empty($telephone))               $errors[] = 'telephone_required';
        if (empty($date_naissance))          $errors[] = 'date_required';
        if (empty($sexe))                    $errors[] = 'sexe_required';
        if ($poids <= 0)                     $errors[] = 'poids_required';
        if ($taille <= 0)                    $errors[] = 'taille_required';
        if (empty($objectif_nutritionnel))   $errors[] = 'objectif_required';
        if (empty($preference_alimentaire))  $errors[] = 'preference_required';

        if (!empty($errors)) {
            header("Location: registerP.php?errors=" . implode(',', $errors));
            exit();
        }

        // ── Create user in DB (first time a record is inserted) ───────────────
        $pu    = $_SESSION['pending_user'];
        $uC    = new userC();
        $uObj  = new user($pu['nom'], $pu['prenom'], $pu['email'], $pu['mot_de_passe'],
                          'utilisateur', '', 'pending', $pu['avatar']);
        $newId = $uC->addUser($uObj);

        // ── Create profile ────────────────────────────────────────────────────
        $profile = new profile(
            $telephone, $date_naissance, $sexe,
            $poids, $taille,
            $objectif_nutritionnel, $preference_alimentaire,
            $allergies, $newId
        );
        $this->addProfile($profile);

        // ── Send OTP ──────────────────────────────────────────────────────────
        $email  = $_SESSION['email'];
        $name   = $_SESSION['prenom'] . ' ' . $_SESSION['nom'];
        $code   = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        $uC->saveVerificationCode($newId, $code, $expiry);
        mailer_send_verification_code($email, $name, $code);

        // ── Update session ────────────────────────────────────────────────────
        unset($_SESSION['pending_user']); // clean up step-1 data
        $_SESSION['pending_id'] = $newId;
        $_SESSION['otp']        = $code;
        $_SESSION['step']       = 'otp';

        header("Location: otp_verification.php");
        exit();
    }
}

