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

$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
$recipient_id = filter_input(INPUT_POST, 'recipient_id', FILTER_SANITIZE_NUMBER_INT);

if (!$message || !$recipient_id) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO messages (sender_id, recipient_id, message, created_at) VALUES (?, ?, ?, NOW())');
    $stmt->execute([$_SESSION['user_id'], $recipient_id, $message]);

    echo json_encode(['success' => true, 'message' => 'Message sent']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
