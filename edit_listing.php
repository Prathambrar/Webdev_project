<?php
include 'auth_admin.php';  // Ensure the admin is authenticated
include 'db.php';  // Include the database connection

// Fetch the car details if the 'id' is provided in the URL
if (isset($_GET['id'])) {
    $car_id = $_GET['id'];
    $stmt = $pdo->prepare('SELECT * FROM Cars WHERE car_id = :car_id');
    $stmt->bindValue(':car_id', $car_id);
    $stmt->execute();
    $car = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$car) {
        echo 'Car not found!';
        exit;
    }
}

// Update the car details when the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $car_id = $_POST['car_id'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    $stmt = $pdo->prepare('UPDATE Cars SET model = :model, year = :year, price = :price, description = :description WHERE car_id = :car_id');
    $stmt->bindValue(':model', $model);
    $stmt->bindValue(':year', $year);
    $stmt->bindValue(':price', $price);
    $stmt->bindValue(':description', $description);
    $stmt->bindValue(':car_id', $car_id);

    if ($stmt->execute()) {
        header('Location: admin_dashboard.php');
        exit;
    } else {
        echo 'Error updating listing.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Edit Listing</title>
</head>
<body>
    <h2>Edit Car Listing</h2>
    <form method="post" action="edit_listing.php">
        <input type="hidden" name="car_id" value="<?= htmlspecialchars($car['car_id']) ?>">
        <label>Model:</label>
        <input type="text" name="model" value="<?= htmlspecialchars($car['model']) ?>" required><br>
        <label>Year:</label>
        <input type="number" name="year" value="<?= htmlspecialchars($car['year']) ?>" required><br>
        <label>Price:</label>
        <input type="number" name="price" step="0.01" value="<?= htmlspecialchars($car['price']) ?>" required><br>
        <label>Description:</label>
        <textarea name="description" required><?= htmlspecialchars($car['description']) ?></textarea><br>
        <button type="submit">Update</button>
    </form>
</body>
</html>
