<?php
session_start();
include 'db.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $car_id = $_POST['car_id'] ?? null;
    $comment = $_POST['comment'] ?? '';
    $user_captcha = $_POST['captcha'] ?? '';
    $captcha = $_SESSION['captcha'] ?? '';

    // Validate CAPTCHA
    if (strtoupper($user_captcha) !== strtoupper($captcha)) {
        die('CAPTCHA validation failed. Please try again.');
    }

    // Clear CAPTCHA from session
    unset($_SESSION['captcha']);

    // Validate car_id and comment
    if (!$car_id || empty($comment)) {
        die('Car ID and comment are required.');
    }

    // Insert the comment into the database
    $sql = "INSERT INTO Comments (car_id, username, comment, created_at) VALUES (:car_id, :username, :comment, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'car_id' => $car_id,
        'username' => $_SESSION['username'],
        'comment' => $comment,
    ]);

    // Redirect back to the car details page
    header("Location: car_details.php?car_id=" . urlencode($car_id));
    exit; // Ensure no further processing occurs
}
?>
