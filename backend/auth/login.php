<?php
session_start();
require_once '../config.php';
require_once 'csrf.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!verifyCsrfToken($_POST['csrf_token'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id, username, password, is_admin FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit;
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['is_admin'] = $user['is_admin'];

    // Handle "Remember Me" functionality
    if (isset($_POST['remember']) && $_POST['remember'] === 'on') {
        $rememberToken = bin2hex(random_bytes(32));
        $hashedToken = password_hash($rememberToken, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE users SET remember_token = ? WHERE id = ?');
        $stmt->execute([$hashedToken, $user['id']]);
        setcookie('remember_token', $rememberToken, time() + (30 * 24 * 60 * 60), '/');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'user_id' => $user['id']
    ]);
} catch (PDOException $e) {
    error_log('Login error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>