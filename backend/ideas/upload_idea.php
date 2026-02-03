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

$title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
$category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);

if (!$title || !$description || !$category) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

$file_path = null;
if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
    $max_size = 5 * 1024 * 1024; // 5MB

    if (!in_array($_FILES['file']['type'], $allowed_types) || $_FILES['file']['size'] > $max_size) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type or size']);
        exit;
    }

    $file_ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    $file_name = uniqid() . '.' . $file_ext;
    $file_path = 'uploads/ideas/' . $file_name;

    if (!move_uploaded_file($_FILES['file']['tmp_name'], '../../' . $file_path)) {
        echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
        exit;
    }
}

try {
    $stmt = $pdo->prepare('INSERT INTO ideas (user_id, title, description, category, file_path, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
    $stmt->execute([$_SESSION['user_id'], $title, $description, $category, $file_path]);

    $stmt = $pdo->prepare('UPDATE users SET points = points + 10 WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);

    echo json_encode(['success' => true, 'message' => 'Idea shared successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
