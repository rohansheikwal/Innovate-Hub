<?php
session_start();
require_once '../config.php';
require_once '../constants.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id, username, email, bio, avatar, points FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare('SELECT id, title, description, created_at FROM ideas WHERE user_id = ? AND is_approved = 1 ORDER BY created_at DESC');
    $stmt->execute([$_SESSION['user_id']]);
    $ideas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare('SELECT name FROM badges WHERE user_id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $badges = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'user' => $user,
        'ideas' => $ideas,
        'badges' => $badges
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>