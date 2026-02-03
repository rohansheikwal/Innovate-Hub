```php
<?php
session_start();
require_once '../config.php';
require_once '../constants.php';

header('Content-Type: application/json');

$is_authenticated = isset($_SESSION['user_id']);
$is_admin = $is_authenticated && isset($_SESSION['is_admin']) && $_SESSION['is_admin'];

if (!$is_authenticated && isset($_COOKIE['remember_token'])) {
    try {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE remember_token = ?');
        $stmt->execute([$_COOKIE['remember_token']]);
        $user = $stmt->fetch();

        if ($user && password_verify($_COOKIE['remember_token'], $user['remember_token'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];
            $is_authenticated = true;
            $is_admin = $user['is_admin'];
        }
    } catch (PDOException $e) {
        // Silent fail
    }
}

echo json_encode([
    'is_authenticated' => $is_authenticated,
    'is_admin' => $is_admin
]);
