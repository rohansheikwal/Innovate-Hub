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

if (!$idea_id) {
    echo json_encode(['success' => false, 'message' => 'Idea ID is required']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id FROM likes WHERE user_id = ? AND idea_id = ?');
    $stmt->execute([$_SESSION['user_id'], $idea_id]);
    $existing_like = $stmt->fetch();

    if ($existing_like) {
        $stmt = $pdo->prepare('DELETE FROM likes WHERE id = ?');
        $stmt->execute([$existing_like['id']]);
        $liked = false;
        $message = 'Idea unliked';
    } else {
        $stmt = $pdo->prepare('INSERT INTO likes (user_id, idea_id, created_at) VALUES (?, ?, NOW())');
        $stmt->execute([$_SESSION['user_id'], $idea_id]);
        $liked = true;
        $message = 'Idea liked';

        // Add points and notification
        $stmt = $pdo->prepare('UPDATE users SET points = points + 5 WHERE id = (SELECT user_id FROM ideas WHERE id = ?)');
        $stmt->execute([$idea_id]);

        $stmt = $pdo->prepare('INSERT INTO notifications (user_id, message, type, created_at) 
                               SELECT user_id, ?, ?, NOW() FROM ideas WHERE id = ?');
        $stmt->execute(['Your idea was liked!', 'success', $idea_id]);
    }

    $stmt = $pdo->prepare('SELECT COUNT(*) as like_count FROM likes WHERE idea_id = ?');
    $stmt->execute([$idea_id]);
    $like_count = $stmt->fetchColumn();

    echo json_encode([
        'success' => true,
        'message' => $message,
        'liked' => $liked,
        'like_count' => $like_count
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
