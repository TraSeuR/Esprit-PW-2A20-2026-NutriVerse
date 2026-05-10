<?php
if (session_status() === PHP_SESSION_NONE) session_start();

/**
 * RBAC Guard
 * Blocks access to pages if the user role doesn't match the required permissions.
 */

function rbac_check($allowed_roles) {
    $user_role = strtolower($_SESSION['user_role'] ?? $_SESSION['role'] ?? 'guest');

    // Admin always has access
    if ($user_role === 'admin' || $user_role === 'administrateur') {
        return true;
    }

    // Convert allowed roles to lowercase for comparison
    $allowed_roles = array_map('strtolower', $allowed_roles);

    if (!in_array($user_role, $allowed_roles)) {
        // Use a relative path for the redirect
        header('Location: ../nutri_back.php?unauthorized=1');
        exit();
    }
}
