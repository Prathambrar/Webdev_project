<?php
session_start();
include 'db.php'; // Include database connection

// Check if the user is logged in as admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Check if the comment_id is provided
if (isset($_POST['comment_id'])) {
    $comment_id = $_POST['comment_id'];

    // Delete the comment
    $stmt = $pdo->prepare('DELETE FROM Comments WHERE comment_id = :comment_id');
    $stmt->bindValue(':comment_id', $comment_id);

    if ($stmt->execute()) {
        // Redirect back to the car details page
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    } else {
        echo 'Error deleting comment.';
    }
} else {
    echo 'Comment ID is required.';
}
?>
