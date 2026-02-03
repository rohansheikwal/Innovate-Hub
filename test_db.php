<?php
require 'php/config.php';
try {
    $pdo->query("SELECT 1");
    echo "Database connected successfully!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>