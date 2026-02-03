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

$recipient_id = filter_input(INPUT_GET, 'recipient_id', FILTER_SANITIZE_NUMBER_INT);
$online = isset($_GET['online']);

try {
    if ($online) {
        $stmt = $pdo->prepare('SELECT id, username FROM users WHERE last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE) AND id != ?');
        $stmt->execute([$_SESSION['user_id']]);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($users);
    } else {
        $stmt = $pdo->prepare('SELECT m.*, s.username as sender_username 
                               FROM messages m 
                               JOIN users s ON m.sender_id = s.id 
                               WHERE (m.sender_id = ? AND m.recipient_id = ?) 
                               OR (m.sender_id = ? AND m.recipient_id = ?) 
                               ORDER BY m.created_at ASC');
        $stmt->execute([$_SESSION['user_id'], $recipient_id, $recipient_id, $_SESSION['user_id']]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($messages);
    }
} catch (PDOException $e) {
    echo json_encode([]);
}
