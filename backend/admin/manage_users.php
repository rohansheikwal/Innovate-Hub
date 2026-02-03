<?php
session_start();
require_once '../config.php';
require_once '../constants.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'list') {
    try {
        $stmt = $pdo->query('SELECT id, username, email, is_banned FROM users WHERE is_admin = 0');
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($users);
    } catch (PDOException $e) {
        echo json_encode([]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$csrf_token = $_POST['csrf_token'] ?? '';
if (!isset($_SESSION['csrf_token']) || $csrf_token !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

$action = $_POST['action'] ?? '';
$user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);

if (!$action || !$user_id) {
    echo json_encode(['success' => false, 'message' => 'Action and user ID are required']);
    exit;
}

try {
    if ($action === 'ban') {
        $stmt = $pdo->prepare('UPDATE users SET is_banned = NOT is_banned WHERE id = ? AND is_admin = 0');
        $stmt->execute([$user_id]);
        $message = $stmt->rowCount() ? 'User status updated' : 'No changes made';
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ? AND is_admin = 0');
        $stmt->execute([$user_id]);
        $message = $stmt->rowCount() ? 'User deleted' : 'No changes made';
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => $message]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>