<?php
include 'auth_admin.php';
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get data from form submission
    $model = $_POST['model'];
    $year = $_POST['year'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Prepare the SQL statement
    $stmt = $pdo->prepare('INSERT INTO Cars (model, year, price, description) VALUES (:model, :year, :price, :description)');
    
    // Bind the values to the prepared statement
    $stmt->bindValue(':model', $model);
    $stmt->bindValue(':year', $year);
    $stmt->bindValue(':price', $price);
    $stmt->bindValue(':description', $description);

    // Execute the statement
    if ($stmt->execute()) {
        header('Location: admin_dashboard.php');
        exit;
    } else {
        echo 'Error creating listing.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Create Listing</title>
</head>
<body>
    <h2>Create New Car Listing</h2>
    <form method='post' action='create_listing.php'>
        <label>Model:</label>
        <input type='text' name='model' required><br>
        <label>Year:</label>
        <input type='number' name='year' required><br>
        <label>Price:</label>
        <input type='number' name='price' step='0.01' required><br>
        <label>Description:</label>
        <textarea name='description' required></textarea><br>
        <button type='submit'>Create</button>
    </form>
</body>
</html>
