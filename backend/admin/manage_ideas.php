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
        $stmt = $pdo->query('SELECT i.id, i.title, u.username, i.is_approved 
                             FROM ideas i 
                             JOIN users u ON i.user_id = u.id');
        $ideas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($ideas);
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
$idea_id = filter_input(INPUT_POST, 'idea_id', FILTER_SANITIZE_NUMBER_INT);

if (!$action || !$idea_id) {
    echo json_encode(['success' => false, 'message' => 'Action and idea ID are required']);
    exit;
}

try {
    if ($action === 'approve') {
        $stmt = $pdo->prepare('UPDATE ideas SET is_approved = NOT is_approved WHERE id = ?');
        $stmt->execute([$idea_id]);
        $message = $stmt->rowCount() ? 'Idea status updated' : 'No changes made';

        if ($stmt->rowCount()) {
            $stmt = $pdo->prepare('INSERT INTO notifications (user_id, message, type, created_at) 
                                   SELECT user_id, ?, ?, NOW() FROM ideas WHERE id = ?');
            $stmt->execute(['Your idea status has been updated!', 'success', $idea_id]);
        }
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare('SELECT file_path FROM ideas WHERE id = ?');
        $stmt->execute([$idea_id]);
        $idea = $stmt->fetch();

        if ($idea['file_path'] && file_exists('../../' . $idea['file_path'])) {
            unlink('../../' . $idea['file_path']);
        }

        $stmt = $pdo->prepare('DELETE FROM ideas WHERE id = ?');
        $stmt->execute([$idea_id]);
        $message = $stmt->rowCount() ? 'Idea deleted' : 'No changes made';
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => $message]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>