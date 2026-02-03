```php
<?php
session_start();
require_once '../config.php';
require_once '../constants.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in']);
    exit;
}

$csrf_token = $_POST['csrf_token'] ?? '';
if (!isset($_SESSION['csrf_token']) || $csrf_token !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$bio = filter_input(INPUT_POST, 'bio', FILTER_SANITIZE_STRING);

if (!$username || !$email) {
    echo json_encode(['success' => false, 'message' => 'Username and email are required']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE (email = ? OR username = ?) AND id != ?');
    $stmt->execute([$email, $username, $_SESSION['user_id']]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email or username already exists']);
        exit;
    }

    $avatar_path = null;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png'];
        $max_size = 2 * 1024 * 1024; // 2MB

        if (!in_array($_FILES['avatar']['type'], $allowed_types) || $_FILES['avatar']['size'] > $max_size) {
            echo json_encode(['success' => false, 'message' => 'Invalid avatar type or size']);
            exit;
        }

        $file_ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $file_ext;
        $avatar_path = 'uploads/avatars/' . $file_name;

        if (!move_uploaded_file($_FILES['avatar']['tmp_name'], '../../' . $avatar_path)) {
            echo json_encode(['success' => false, 'message' => 'Failed to upload avatar']);
            exit;
        }
    }

    $stmt = $pdo->prepare('UPDATE users SET username = ?, email = ?, bio = ?, avatar = COALESCE(?, avatar) WHERE id = ?');
    $stmt->execute([$username, $email, $bio, $avatar_path, $_SESSION['user_id']]);

    $_SESSION['username'] = $username;

    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
