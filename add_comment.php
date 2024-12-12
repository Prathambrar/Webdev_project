<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] === 'admin') {
    die('Unauthorized action.');
}

$car_id = $_POST['car_id'] ?? null;
$comment = $_POST['comment'] ?? null;

if (!$car_id || !$comment) {
    die('Car ID and comment are required.');
}

$stmt = $pdo->prepare("INSERT INTO Comments (car_id, username, comment, created_at) VALUES (:car_id, :username, :comment, NOW())");
$stmt->execute([
    'car_id' => $car_id,
    'username' => $_SESSION['username'],
    'comment' => $comment,
]);

header("Location: car_details.php?car_id=$car_id");
exit;
