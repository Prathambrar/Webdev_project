<?php
include 'auth_admin.php';  // Ensure admin authentication
include 'db.php';  // Include database connection

// Check if 'id' is provided in the URL to delete a car
if (isset($_GET['id'])) {
    $car_id = $_GET['id'];  // Get car_id from the URL

    try {
        // Begin a transaction
        $pdo->beginTransaction();

        // Delete related comments
        $stmt = $pdo->prepare('DELETE FROM comments WHERE car_id = :car_id');
        $stmt->bindValue(':car_id', $car_id);
        $stmt->execute();

        // Delete the car
        $stmt = $pdo->prepare('DELETE FROM Cars WHERE car_id = :car_id');
        $stmt->bindValue(':car_id', $car_id);
        $stmt->execute();

        // Commit the transaction
        $pdo->commit();

        // Redirect to the admin dashboard after successful deletion
        header('Location: admin_dashboard.php');
        exit;
    } catch (PDOException $e) {
        // Rollback transaction in case of error
        $pdo->rollBack();
        echo 'Error deleting listing: ' . $e->getMessage();
    }
}
?>
