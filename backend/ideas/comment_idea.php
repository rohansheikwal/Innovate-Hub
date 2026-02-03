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

$idea_id = filter_input(INPUT_POST, 'idea_id', FILTER_SANITIZE_NUMBER_INT);
$comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);

if (!$idea_id || !$comment) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO comments (user_id, idea_id, comment, created_at) VALUES (?, ?, ?, NOW())');
    $stmt->execute([$_SESSION['user_id'], $idea_id, $comment]);

    // Add points and notification
    $stmt = $pdo->prepare('UPDATE users SET points = points + 3 WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);

    $stmt = $pdo->prepare('INSERT INTO notifications (user_id, message, type, created_at) 
                           SELECT user_id, ?, ?, NOW() FROM ideas WHERE id = ?');
    $stmt->execute(['New comment on your idea!', 'success', $idea_id]);

    echo json_encode(['success' => true, 'message' => 'Comment posted']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
