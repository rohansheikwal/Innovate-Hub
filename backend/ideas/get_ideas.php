```php
<?php
session_start();
require_once '../config.php';
require_once '../constants.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$idea_id = filter_input(INPUT_GET, 'idea_id', FILTER_SANITIZE_NUMBER_INT);
$comments = isset($_GET['comments']);

try {
    if ($comments) {
        $stmt = $pdo->prepare('SELECT c.*, u.username 
                               FROM comments c 
                               JOIN users u ON c.user_id = u.id 
                               WHERE c.idea_id = ? 
                               ORDER BY c.created_at ASC');
        $stmt->execute([$idea_id]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($comments);
    } else {
        $stmt = $pdo->prepare('SELECT i.*, u.username, 
                               (SELECT COUNT(*) FROM likes WHERE idea_id = i.id) as like_count,
                               (SELECT COUNT(*) FROM comments WHERE idea_id = i.id) as comment_count
                               FROM ideas i 
                               JOIN users u ON i.user_id = u.id 
                               WHERE i.id = ? AND i.is_approved = 1');
        $stmt->execute([$idea_id]);
        $idea = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($idea ?: []);
    }
} catch (PDOException $e) {
    echo json_encode([]);
}
