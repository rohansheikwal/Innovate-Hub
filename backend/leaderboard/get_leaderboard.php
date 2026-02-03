<?php
session_start();
require_once '../config.php';
require_once '../constants.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query('SELECT username, points, avatar 
                         FROM users 
                         WHERE is_banned = 0 
                         ORDER BY points DESC 
                         LIMIT 10');
    $leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($leaderboard);
} catch (PDOException $e) {
    echo json_encode([]);
}
?>