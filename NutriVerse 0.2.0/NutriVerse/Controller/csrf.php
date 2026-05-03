<?php
// ============================================================
// CSRF Helper
// Usage:
//   In form: <?php echo csrf_field(); 
//   In action: csrf_verify();
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        // Fallback for older PHP versions without random_bytes
        if (function_exists('random_bytes')) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } else {
            $_SESSION['csrf_token'] = md5(uniqid(rand(), true));
        }
    }
    return $_SESSION['csrf_token'];
}

function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

function csrf_verify()
{
    $session_token = isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '';
    $post_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

    if (empty($post_token) || !hash_equals($session_token, $post_token)) {
        http_response_code(403);
        die('Invalid CSRF token. Please go back and try again.');
    }

    // Rotate token after successful use
    if (function_exists('random_bytes')) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    } else {
        $_SESSION['csrf_token'] = md5(uniqid(rand(), true));
    }
}

?>