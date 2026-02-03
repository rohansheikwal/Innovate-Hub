<?php
session_start();
require_once '../config.php';

/**
 * Generates a CSRF token and stores it in the session.
 * @return string The CSRF token.
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifies a CSRF token against the session token.
 * @param string $token The CSRF token to verify.
 * @return bool True if the token is valid, false otherwise.
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

header('Content-Type: application/json');
echo json_encode(['csrf_token' => generateCsrfToken()]);
?>