<?php

require_once __DIR__ . "/../config.php";

class userC
{
    // ─── LIST ──────────────────────────────────────────────────────────────────
    public function listUser()
    {
        $db = config::getConnexion();
        try {
            $stmt = $db->query(
                "SELECT u.id_user, u.nom, u.prenom, u.email, u.role,
                        u.etat_compte, u.date_inscription, u.avatar,
                        p.telephone, p.date_naissance, p.sexe,
                        p.poids, p.taille,
                        p.objectif_nutritionnel,
                        p.preference_alimentaire,
                        p.allergies
                 FROM user u
                 LEFT JOIN profile p ON u.id_user = p.id_user
                 ORDER BY u.id_user ASC"
            );
            return $stmt->fetchAll();
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    // ─── ADD ───────────────────────────────────────────────────────────────────
    public function addUser($user)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare(
                "INSERT INTO user (nom, prenom, email, mot_de_passe, role, remember_me, etat_compte, avatar)
                 VALUES (:nom, :prenom, :email, :mot_de_passe, :role, :remember_me, :etat_compte, :avatar)"
            );
            $req->execute([
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(),
                'mot_de_passe' => $user->getMotDePasse(),
                'role' => $user->getRole(),
                'remember_me' => $user->getRememberMe(),
                'etat_compte' => $user->getEtatCompte(),
                'avatar' => $user->getAvatar(),
            ]);
            return (int) $db->lastInsertId();
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    // ─── LOGIN ─────────────────────────────────────────────────────────────────
    public function userLogin($email, $mot_de_passe)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("SELECT * FROM user WHERE email = :email");
            $req->execute(['email' => $email]);
            $user = $req->fetch();

            if ($user) {
                // Truly disabled accounts – block immediately (no password check)
                if ($user['etat_compte'] === 'desactive') {
                    return 'inactive';
                }
                // Pending accounts – verify password so the resume flow can work
                if (password_verify($mot_de_passe, $user['mot_de_passe'])) {
                    return $user; // caller checks etat_compte === 'pending'
                }
                // Fallback for plain-text passwords during transition
                if ($mot_de_passe === $user['mot_de_passe']) {
                    return $user;
                }
            }
            return false;
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    // ─── GET BY ID ─────────────────────────────────────────────────────────────
    public function getUserById($id)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("SELECT * FROM user WHERE id_user = :id");
            $req->execute(['id' => $id]);
            return $req->fetch();
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    // ─── GET BY EMAIL ──────────────────────────────────────────────────────────
    public function getUserByEmail($email)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("SELECT * FROM user WHERE email = :email");
            $req->execute(['email' => $email]);
            return $req->fetch();
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    // ─── CHECK DUPLICATE EMAIL ─────────────────────────────────────────────────
    public function emailExists($email, $excludeId = null)
    {
        $db = config::getConnexion();
        try {
            if ($excludeId) {
                $req = $db->prepare("SELECT id_user FROM user WHERE email = :email AND id_user != :id");
                $req->execute(['email' => $email, 'id' => $excludeId]);
            } else {
                $req = $db->prepare("SELECT id_user FROM user WHERE email = :email");
                $req->execute(['email' => $email]);
            }
            return $req->fetch() !== false;
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    // ─── UPDATE ────────────────────────────────────────────────────────────────
    public function updateUser($user, $id)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare(
                "UPDATE user
                 SET nom = :nom, prenom = :prenom, email = :email,
                     mot_de_passe = :mot_de_passe, role = :role,
                     remember_me = :remember_me, etat_compte = :etat_compte,
                     avatar = :avatar
                 WHERE id_user = :id"
            );
            $req->execute([
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(),
                'mot_de_passe' => $user->getMotDePasse(),
                'role' => $user->getRole(),
                'remember_me' => $user->getRememberMe(),
                'etat_compte' => $user->getEtatCompte(),
                'avatar' => $user->getAvatar(),
                'id' => $id
            ]);
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    // ─── UPDATE REMEMBER ME TOKEN ──────────────────────────────────────────────
    public function updateRememberMe($id, $token)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("UPDATE user SET remember_me = :token WHERE id_user = :id");
            $req->execute(['token' => $token, 'id' => $id]);
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    // ─── GET BY REMEMBER ME TOKEN ──────────────────────────────────────────────
    public function getUserByRememberToken($token)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("SELECT * FROM user WHERE remember_me = :token AND etat_compte = 'actif'");
            $req->execute(['token' => $token]);
            return $req->fetch();
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    // ─── FORGOT PASSWORD TOKEN ─────────────────────────────────────────────────
    public function saveResetToken($id, $token, $expiry)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare(
                "UPDATE user SET reset_token = :token, reset_token_expiry = :expiry WHERE id_user = :id"
            );
            $req->execute(['token' => $token, 'expiry' => $expiry, 'id' => $id]);
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    public function getUserByResetToken($token)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare(
                "SELECT * FROM user WHERE reset_token = :token AND reset_token_expiry > NOW()"
            );
            $req->execute(['token' => $token]);
            return $req->fetch();
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    public function resetPassword($id, $hashedPassword)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare(
                "UPDATE user SET mot_de_passe = :pwd, reset_token = NULL, reset_token_expiry = NULL WHERE id_user = :id"
            );
            $req->execute(['pwd' => $hashedPassword, 'id' => $id]);
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    // ─── EMAIL VERIFICATION (OTP) ──────────────────────────────────────────────
    public function saveVerificationCode($id, $code, $expiry)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare(
                "UPDATE user SET verify_code = :code, verify_code_expiry = :expiry WHERE id_user = :id"
            );
            $req->execute(['code' => $code, 'expiry' => $expiry, 'id' => $id]);
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    public function activateAccount($id)
    {
        $db = config::getConnexion();
        try {
            $req = $db->prepare(
                "UPDATE user SET etat_compte = 'actif', verify_code = NULL, verify_code_expiry = NULL WHERE id_user = :id"
            );
            $req->execute(['id' => $id]);
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    // ─── PROCESS REGISTER (Step 1) ────────────────────────────────────────────
    public function processRegister()
    {
        require_once __DIR__ . "/../Model/user.php";
        require_once __DIR__ . "/csrf.php";
        csrf_verify();

        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        $mot_de_passe = $_POST['mot_de_passe'] ?? '';
        $confirm = $_POST['confirm_mot_de_passe'] ?? '';
        $avatar = $_POST['avatar'] ?? 'avatar1.png';

        $errors = [];
        if (empty($nom))
            $errors[] = 'nom_required';
        if (empty($prenom))
            $errors[] = 'prenom_required';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            $errors[] = 'email_invalid';
        if (strlen($mot_de_passe) < 8)
            $errors[] = 'password_short';
        if ($mot_de_passe !== $confirm)
            $errors[] = 'password_mismatch';
        if (
            !preg_match('/[A-Z]/', $mot_de_passe) ||
            !preg_match('/[0-9]/', $mot_de_passe) ||
            !preg_match('/[\W_]/', $mot_de_passe)
        ) {
            $errors[] = 'password_weak';
        }

        if (!empty($errors)) {
            header("Location: register.php?errors=" . implode(',', $errors));
            exit();
        }

        if ($this->emailExists($email)) {
            header("Location: register.php?errors=email_taken");
            exit();
        }

        $hashed = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        // Store user data in session — DB insert happens together with profile (step 2)
        $_SESSION['pending_user'] = [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'mot_de_passe' => $hashed,
            'avatar' => $avatar,
        ];
        $_SESSION['email'] = $email;
        $_SESSION['nom'] = $nom;
        $_SESSION['prenom'] = $prenom;
        $_SESSION['step'] = 'profile';

        header("Location: registerP.php");
        exit();
    }

    // ─── PROCESS LOGIN ────────────────────────────────────────────────────────
    public function processLogin()
    {
        require_once __DIR__ . "/csrf.php";
        require_once __DIR__ . "/profileC.php";
        csrf_verify();

        $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        $mot_de_passe = $_POST['mot_de_passe'] ?? '';
        $remember = isset($_POST['remember_me']);

        if (empty($email) || empty($mot_de_passe)) {
            header("Location: login.php?errors=empty_fields");
            exit();
        }

        $result = $this->userLogin($email, $mot_de_passe);

        if ($result === 'inactive') {
            header("Location: login.php?errors=account_inactive");
            exit();
        }
        if ($result === false) {
            header("Location: login.php?errors=invalid_credentials");
            exit();
        }

        // Smart Resume Flow for pending accounts
        if ($result['etat_compte'] === 'pending') {
            $pC = new profileC();
            $profile = $pC->getProfileById($result['id_user']);

            $_SESSION['email'] = $result['email'];
            $_SESSION['nom'] = $result['nom'];
            $_SESSION['prenom'] = $result['prenom'];
            $_SESSION['pending_id'] = $result['id_user'];

            if (!$profile) {
                // No profile yet — send back to step 2
                $_SESSION['step'] = 'profile';
                header("Location: registerP.php");
            } else {
                // Profile exists but email not verified — auto-resend OTP
                require_once __DIR__ . "/Mailer.php";
                $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                $this->saveVerificationCode($result['id_user'], $code, $expiry);
                mailer_send_verification_code($result['email'], $result['prenom'] . ' ' . $result['nom'], $code);
                $_SESSION['otp'] = $code;
                $_SESSION['step'] = 'otp';
                header("Location: otp_verification.php?pending=1");
            }
            exit();
        }

        $user = $result;

        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $this->updateRememberMe($user['id_user'], $token);
            setcookie('remember_token', $token, [
                'expires' => time() + 30 * 24 * 3600,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }

        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['prenom'] = $user['prenom'];
        $_SESSION['avatar'] = $user['avatar'] ?? 'avatar1.png';

        if ($user['role'] === 'admin') {
            header("Location: ../BackOffice/back.php");
        } else {
            header("Location: index.php");
        }
        exit();
    }

    // ─── PROCESS LOGOUT ───────────────────────────────────────────────────────
    public function processLogout()
    {
        session_unset();
        session_destroy();
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', [
                'expires' => time() - 3600,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }
        header("Location: login.php");
        exit();
    }

    // ─── PROCESS FORGOT PASSWORD ──────────────────────────────────────────────
    public function processForgotPassword()
    {
        require_once __DIR__ . "/csrf.php";
        require_once __DIR__ . "/Mailer.php";
        csrf_verify();

        $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: forgot_password.php?error=empty");
            exit();
        }

        $user = $this->getUserByEmail($email);
        if (!$user) {
            header("Location: forgot_password.php?errors=email_not_found");
            exit();
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        $this->saveResetToken($user['id_user'], $code, $expiry);
        mailer_send_password_reset_otp($email, $user['prenom'] . ' ' . $user['nom'], $code);

        $_SESSION['email'] = $email;
        $_SESSION['otp_id'] = $user['id_user'];
        $_SESSION['step'] = 'otp';

        header("Location: otp_verification.php");
        exit();
    }

    // ─── PROCESS RESET PASSWORD ───────────────────────────────────────────────
    public function processResetPassword()
    {
        require_once __DIR__ . "/csrf.php";
        csrf_verify();

        if (empty($_SESSION['reset_authorized_id'])) {
            header("Location: login.php");
            exit();
        }

        $id_user = $_SESSION['reset_authorized_id'];
        $password = $_POST['mot_de_passe'] ?? '';
        $confirm = $_POST['confirm_mot_de_passe'] ?? '';

        $errors = [];
        if (strlen($password) < 8)
            $errors[] = 'pw_short';
        if (
            !preg_match('/[A-Z]/', $password) ||
            !preg_match('/[0-9]/', $password) ||
            !preg_match('/[\W_]/', $password)
        )
            $errors[] = 'pw_weak';
        if ($password !== $confirm)
            $errors[] = 'pw_mismatch';

        if (!empty($errors)) {
            header("Location: new_password.php?errors=" . implode(',', $errors));
            exit();
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $this->resetPassword($id_user, $hashed);

        unset($_SESSION['reset_authorized_id'], $_SESSION['reset_authorized_email']);

        header("Location: login.php?success=password_reset");
        exit();
    }

    // ─── PROCESS OTP VERIFY ───────────────────────────────────────────────────
    // Returns ['error' => string] or exits with redirect on success
    public function processOtpVerify($id, $email)
    {
        require_once __DIR__ . "/csrf.php";
        csrf_verify();

        $user = $this->getUserById($id);
        $inputCode = trim($_POST['verify_code'] ?? '');

        if (!$user) {
            return ['error' => 'Utilisateur introuvable.'];
        }
        if (empty($user['verify_code']) && empty($user['reset_token'])) {
            return ['error' => 'Aucun code en attente.'];
        }

        $savedCode = $user['verify_code'] ?: $user['reset_token'];
        $savedExpiry = $user['verify_code_expiry'] ?: $user['reset_token_expiry'];

        if (strtotime($savedExpiry) < time()) {
            return ['error' => 'Le code a expiré. Veuillez en demander un nouveau.'];
        }
        if (!hash_equals((string) $savedCode, $inputCode)) {
            return ['error' => 'Code incorrect.'];
        }

        // SUCCESS
        if (!empty($user['reset_token'])) {
            $_SESSION['reset_authorized_id'] = $id;
            $_SESSION['reset_authorized_email'] = $email;
            $_SESSION['step'] = 'active';
            header("Location: new_password.php");
            exit();
        } else {
            $this->activateAccount($id);
            unset($_SESSION['pending_id'], $_SESSION['otp'], $_SESSION['step']);
            header("Location: login.php?success=verified");
            exit();
        }
    }

    // ─── PROCESS OTP RESEND ───────────────────────────────────────────────────
    // Returns $msg string to display in the view
    public function processOtpResend($id, $email, $name)
    {
        require_once __DIR__ . "/Mailer.php";

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        $user = $this->getUserById($id);
        if ($user && !empty($user['reset_token'])) {
            $this->saveResetToken($id, $code, $expiry);
            mailer_send_password_reset_otp($email, $name, $code);
        } else {
            $this->saveVerificationCode($id, $code, $expiry);
            mailer_send_verification_code($email, $name, $code);
        }

        $_SESSION['otp'] = $code;
        return 'Un nouveau code a été envoyé à ' . htmlspecialchars($email) . '.';
    }

    // ─── PROCESS UPDATE PROFILE (Front Office) ────────────────────────────────
    public function processUpdateProfile()
    {
        require_once __DIR__ . "/csrf.php";
        require_once __DIR__ . "/../Model/user.php";
        require_once __DIR__ . "/../Model/profile.php";
        require_once __DIR__ . "/profileC.php";
        csrf_verify();

        $id_user = (int) $_SESSION['id_user'];
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));

        $errors = [];
        if (empty($nom))
            $errors[] = 'nom_required';
        if (empty($prenom))
            $errors[] = 'prenom_required';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            $errors[] = 'email_invalid';

        if (!empty($errors)) {
            header("Location: edit_profile.php?errors=" . implode(',', $errors));
            exit();
        }

        if ($this->emailExists($email, $id_user)) {
            header("Location: edit_profile.php?errors=email_taken");
            exit();
        }

        $currentUser = $this->getUserById($id_user);
        $mot_de_passe = !empty($_POST['mot_de_passe'])
            ? password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT)
            : $currentUser['mot_de_passe'];
        $avatar = !empty($_POST['avatar']) ? $_POST['avatar'] : ($currentUser['avatar'] ?? 'avatar1.png');

        $updatedUser = new user(
            $nom,
            $prenom,
            $email,
            $mot_de_passe,
            $currentUser['role'],
            $currentUser['remember_me'] ?? '',
            $currentUser['etat_compte'],
            $avatar
        );
        $this->updateUser($updatedUser, $id_user);

        $_SESSION['nom'] = $nom;
        $_SESSION['prenom'] = $prenom;
        $_SESSION['email'] = $email;
        $_SESSION['avatar'] = $avatar;

        $pC = new profileC();
        $profile = new profile(
            trim($_POST['telephone'] ?? ''),
            trim($_POST['date_naissance'] ?? ''),
            trim($_POST['sexe'] ?? ''),
            !empty($_POST['poids']) ? (float) $_POST['poids'] : 0.0,
            !empty($_POST['taille']) ? (float) $_POST['taille'] : 0.0,
            trim($_POST['objectif_nutritionnel'] ?? ''),
            trim($_POST['preference_alimentaire'] ?? ''),
            trim($_POST['allergies'] ?? ''),
            $id_user
        );

        $existingProfile = $pC->getProfileById($id_user);
        if ($existingProfile) {
            $pC->updateProfile($profile, $id_user);
        } else {
            $pC->addProfile($profile);
        }

        header("Location: edit_profile.php?success=1");
        exit();
    }

    // ─── PROCESS BACK OFFICE: ADD USER ───────────────────────────────────────
    public function processBackAddUser()
    {
        require_once __DIR__ . "/csrf.php";
        require_once __DIR__ . "/../Model/user.php";
        require_once __DIR__ . "/../Model/profile.php";
        require_once __DIR__ . "/profileC.php";
        csrf_verify();

        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        $mot_de_passe = $_POST['mot_de_passe'] ?? '';
        $role = in_array($_POST['role'] ?? '', ['admin', 'utilisateur']) ? $_POST['role'] : 'utilisateur';
        $etat_compte = in_array($_POST['etat_compte'] ?? '', ['actif', 'desactive']) ? $_POST['etat_compte'] : 'actif';
        $avatar = $_POST['avatar'] ?? 'avatar1.png';

        $errors = [];
        if (empty($nom))
            $errors[] = 'Nom requis.';
        if (empty($prenom))
            $errors[] = 'Prénom requis.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            $errors[] = 'Email invalide.';
        if (strlen($mot_de_passe) < 8)
            $errors[] = 'Mot de passe trop court (min. 8 caractères).';
        if ($this->emailExists($email))
            $errors[] = 'Cet email est déjà utilisé.';

        if (!empty($errors)) {
            $_SESSION['back_errors'] = $errors;
            header("Location: back.php#modal-add");
            exit();
        }

        $hashed = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $user = new user($nom, $prenom, $email, $hashed, $role, '', $etat_compte, $avatar);
        $newId = $this->addUser($user);

        $pC = new profileC();
        $profile = new profile(
            trim($_POST['telephone'] ?? ''),
            trim($_POST['date_naissance'] ?? ''),
            trim($_POST['sexe'] ?? ''),
            isset($_POST['poids']) && $_POST['poids'] !== '' ? (float) $_POST['poids'] : 0.0,
            isset($_POST['taille']) && $_POST['taille'] !== '' ? (float) $_POST['taille'] : 0.0,
            trim($_POST['objectif_nutritionnel'] ?? ''),
            trim($_POST['preference_alimentaire'] ?? ''),
            trim($_POST['allergies'] ?? ''),
            $newId
        );
        $pC->addProfile($profile);

        header("Location: back.php?success=user_added");
        exit();
    }

    // ─── PROCESS BACK OFFICE: UPDATE USER ────────────────────────────────────
    public function processBackUpdateUser()
    {
        require_once __DIR__ . "/csrf.php";
        require_once __DIR__ . "/../Model/user.php";
        require_once __DIR__ . "/../Model/profile.php";
        require_once __DIR__ . "/profileC.php";
        csrf_verify();

        $id_user = (int) ($_POST['id_user'] ?? 0);
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        $role = in_array($_POST['role'] ?? '', ['admin', 'utilisateur']) ? $_POST['role'] : 'utilisateur';
        $etat_compte = in_array($_POST['etat_compte'] ?? '', ['actif', 'desactive', 'pending']) ? $_POST['etat_compte'] : 'actif';

        $errors = [];
        if ($id_user <= 0)
            $errors[] = 'ID utilisateur invalide.';
        if (empty($nom))
            $errors[] = 'Nom requis.';
        if (empty($prenom))
            $errors[] = 'Prénom requis.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            $errors[] = 'Email invalide.';
        if ($this->emailExists($email, $id_user))
            $errors[] = 'Cet email est déjà utilisé par un autre compte.';

        if (!empty($errors)) {
            $_SESSION['back_errors'] = $errors;
            header("Location: back.php");
            exit();
        }

        $currentUser = $this->getUserById($id_user);
        $mot_de_passe = !empty($_POST['mot_de_passe'])
            ? password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT)
            : $currentUser['mot_de_passe'];
        $avatar = !empty($_POST['avatar']) ? $_POST['avatar'] : ($currentUser['avatar'] ?? 'avatar1.png');

        $user = new user($nom, $prenom, $email, $mot_de_passe, $role, $currentUser['remember_me'] ?? '', $etat_compte, $avatar);
        $this->updateUser($user, $id_user);

        $pC = new profileC();
        $profile = new profile(
            trim($_POST['telephone'] ?? ''),
            trim($_POST['date_naissance'] ?? ''),
            trim($_POST['sexe'] ?? ''),
            isset($_POST['poids']) && $_POST['poids'] !== '' ? (float) $_POST['poids'] : 0.0,
            isset($_POST['taille']) && $_POST['taille'] !== '' ? (float) $_POST['taille'] : 0.0,
            trim($_POST['objectif_nutritionnel'] ?? ''),
            trim($_POST['preference_alimentaire'] ?? ''),
            trim($_POST['allergies'] ?? ''),
            $id_user
        );

        $existingProfile = $pC->getProfileById($id_user);
        if ($existingProfile) {
            $pC->updateProfile($profile, $id_user);
        } else {
            $pC->addProfile($profile);
        }

        header("Location: back.php?success=user_updated");
        exit();
    }
}

