<?php
include 'auth_admin.php';  // Ensure admin authentication
include 'db.php';  // Include database connection

$user_id = $_GET['id'];
$stmt = $pdo->prepare('DELETE FROM Users WHERE user_id = ?');
$stmt->execute([$user_id]);

header('Location: view_users.php');
exit;
?>
