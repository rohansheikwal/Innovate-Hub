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

$search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING) ?? '';
$category = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_STRING) ?? '';
$sort = filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_STRING) ?? 'newest';

try {
    $query = 'SELECT i.*, u.username, 
              (SELECT COUNT(*) FROM likes WHERE idea_id = i.id) as like_count,
              (SELECT COUNT(*) FROM comments WHERE idea_id = i.id) as comment_count
              FROM ideas i 
              JOIN users u ON i.user_id = u.id 
              WHERE i.is_approved = 1';

    $params = [];
    if ($search) {
        $query .= ' AND (i.title LIKE ? OR i.description LIKE ?)';
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    if ($category) {
        $query .= ' AND i.category = ?';
        $params[] = $category;
    }

    switch ($sort) {
        case 'most_liked':
            $query .= ' ORDER BY like_count DESC';
            break;
        case 'most_commented':
            $query .= ' ORDER BY comment_count DESC';
            break;
        default:
            $query .= ' ORDER BY i.created_at DESC';
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $ideas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($ideas);
} catch (PDOException $e) {
    echo json_encode([]);
}
