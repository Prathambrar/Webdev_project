<?php
include 'auth_admin.php';  // Ensure admin authentication
include 'db.php';  // Include database connection

// Check if 'id' is provided in the URL to delete a car
if (isset($_GET['id'])) {
    $car_id = $_GET['id'];  // Get car_id from the URL

    // Prepare and execute the DELETE query
    $stmt = $pdo->prepare('DELETE FROM Cars WHERE car_id = :car_id');
    $stmt->bindValue(':car_id', $car_id);

    if ($stmt->execute()) {
        // Redirect to the admin dashboard after successful deletion
        header('Location: admin_dashboard.php');
        exit;
    } else {
        echo 'Error deleting listing.';  // Display error if deletion fails
    }
}
?>
