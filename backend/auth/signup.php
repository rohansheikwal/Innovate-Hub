<?php
session_start();
require_once '../config.php';
require_once 'csrf.php';
require_once '../../vendor/autoload.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!verifyCsrfToken($_POST['csrf_token'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';

if (!$username || !$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

$password_regex = '/^(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}$/';
if (!preg_match($password_regex, $password)) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters with uppercase, number, and special character']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? OR username = ?');
    $stmt->execute([$email, $username]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email or username already exists']);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
    $stmt->execute([$username, $email, $hashedPassword]);

    $userId = $pdo->lastInsertId();
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username;
    $_SESSION['is_admin'] = 0;

    // Note: Uncomment and implement sendEmail() if available
    /*
    $subject = 'Welcome to Innocascade!';
    $body = "Hi $username,\n\nWelcome to Innocascade! Your account has been created successfully. Start sharing and exploring ideas now!\n\nBest,\nThe Innocascade Team";
    sendEmail($email, $username, $subject, $body);
    */

    echo json_encode([
        'success' => true,
        'message' => 'Account created successfully',
        'user_id' => $userId
    ]);
} catch (PDOException $e) {
    error_log('Signup error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>