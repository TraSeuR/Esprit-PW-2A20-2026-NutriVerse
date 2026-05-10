<?php
/**
 * GoogleAuthC.php  (Controller)
 * ──────────────────────────────────────────────────────
 * Google OAuth2 helper functions for NutriVerse.
 *
 * SETUP STEPS:
 *   1. Go to https://console.cloud.google.com
 *   2. Create a project → APIs & Services → Credentials
 *   3. Create OAuth 2.0 Client ID (Web Application)
 *   4. Set Authorized redirect URI to:
 *      http://localhost/NutriVerse/View/FrontOffice/google_callback.php
 *   5. Copy your Client ID and Secret into the defines below.
 *
 * SQL (run once in phpMyAdmin):
 *   ALTER TABLE user ADD COLUMN google_id VARCHAR(100) NULL DEFAULT NULL;
 */

require_once __DIR__ . '/../config/config.php';

// ── Your Google OAuth2 credentials ───────────────────
// ⚠️  Replace these with your real values from Google Console
define('GOOGLE_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_ID');
define('GOOGLE_CLIENT_SECRET', 'YOUR_GOOGLE_CLIENT_SECRET');
define('GOOGLE_REDIRECT_URI', 'http://localhost/NutriVerse/View/FrontOffice/utilisateur/google_callback.php');

// ─────────────────────────────────────────────────────
// google_get_auth_url()
// Returns the Google login URL to redirect the user to.
// ─────────────────────────────────────────────────────
function google_get_auth_url()
{
    // Generate a random state to protect against CSRF in OAuth
    $state = bin2hex(random_bytes(16));
    $_SESSION['google_oauth_state'] = $state;

    // Build the authorization URL
    $params = [
        'client_id' => GOOGLE_CLIENT_ID,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'response_type' => 'code',
        'scope' => 'openid email profile',
        'state' => $state,
        'access_type' => 'online',
        'prompt' => 'select_account', // always show account chooser
    ];

    return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
}

// ─────────────────────────────────────────────────────
// google_exchange_code($code)
// Exchanges the authorization code from Google
// for an access token. Returns the token string or null.
// ─────────────────────────────────────────────────────
function google_exchange_code($code)
{
    // POST to Google's token endpoint
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query([
                'code' => $code,
                'client_id' => GOOGLE_CLIENT_ID,
                'client_secret' => GOOGLE_CLIENT_SECRET,
                'redirect_uri' => GOOGLE_REDIRECT_URI,
                'grant_type' => 'authorization_code',
            ]),
        ],
    ]);

    $response = @file_get_contents('https://oauth2.googleapis.com/token', false, $context);
    if (!$response)
        return null;

    $data = json_decode($response, true);
    return $data['access_token'] ?? null;
}

// ─────────────────────────────────────────────────────
// google_get_user_info($access_token)
// Uses the access token to fetch the user's Google profile.
// Returns an array with: sub, email, given_name, family_name
// ─────────────────────────────────────────────────────
function google_get_user_info($access_token)
{
    $context = stream_context_create([
        'http' => [
            'header' => 'Authorization: Bearer ' . $access_token,
        ],
    ]);

    $response = @file_get_contents('https://www.googleapis.com/oauth2/v3/userinfo', false, $context);
    if (!$response)
        return null;

    return json_decode($response, true);
}

// ─────────────────────────────────────────────────────
// google_find_or_create_user($google_id, $email, $prenom, $nom)
//
// Checks if user already exists in DB (by email).
//   → If YES: logs them in.
//   → If NO:  creates a new active account (no OTP needed).
//
// Returns the user row as an array, or null on failure.
// ─────────────────────────────────────────────────────
function google_find_or_create_user($google_id, $email, $prenom, $nom)
{
    $db = config::getConnexion();

    // ── Check if user already exists by email ────────
    $stmt = $db->prepare("SELECT * FROM user WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        // User exists — save google_id if it wasn't stored before
        if (empty($user['google_id'])) {
            $db->prepare("UPDATE user SET google_id = :gid WHERE id_user = :id")
                ->execute(['gid' => $google_id, 'id' => $user['id_user']]);
            $user['google_id'] = $google_id;
        }
        return $user;
    }

    // ── New user — create account automatically ──────
    // No password needed (Google handles auth).
    // Account is immediately active — no OTP needed.
    $stmt = $db->prepare("
        INSERT INTO user
            (nom, prenom, email, mot_de_passe, role, remember_me, etat_compte, avatar, google_id)
        VALUES
            (:nom, :prenom, :email, '', 'utilisateur', '', 'actif', 'avatar1.png', :google_id)
    ");
    $stmt->execute([
        'nom' => $nom,
        'prenom' => $prenom,
        'email' => $email,
        'google_id' => $google_id,
    ]);

    // Fetch the newly created user to return it
    $newId = (int) $db->lastInsertId();
    $stmt = $db->prepare("SELECT * FROM user WHERE id_user = :id LIMIT 1");
    $stmt->execute(['id' => $newId]);
    return $stmt->fetch();
}

// ─────────────────────────────────────────────────────
// google_has_profile($user_id)
// Returns true if the user already has a profile row.
// New Google users need to complete their profile first.
// ─────────────────────────────────────────────────────
function google_has_profile($user_id)
{
    $db = config::getConnexion();
    $stmt = $db->prepare("SELECT id_user FROM profile WHERE id_user = :id LIMIT 1");
    $stmt->execute(['id' => $user_id]);
    return (bool) $stmt->fetch();
}
