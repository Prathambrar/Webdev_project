<?php
include 'auth_admin.php';  // Ensure admin authentication
include 'db.php';  // Include database connection

// Fetch all car listings from the database
$stmt = $pdo->query('SELECT * FROM Cars');
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Welcome, Admin!</h1>

    <!-- Logout Button -->
    <form method="post" action="logout.php">
        <button type="submit">Logout</button>
    </form>

    <a href="create_listing.php">Create New Listing</a>
    
    <h2>Manage Car Listings</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Model</th>
            <th>Year</th>
            <th>Price</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($cars as $car): ?>
            <tr>
                <td><?= $car['car_id'] ?></td>
                <td><?= htmlspecialchars($car['model']) ?></td>
                <td><?= $car['year'] ?></td>
                <td><?= $car['price'] ?></td>
                <td><?= htmlspecialchars($car['description']) ?></td>
                <td>
                    <a href="edit_listing.php?id=<?= $car['car_id'] ?>">Edit</a>
                    <a href="delete_listing.php?id=<?= $car['car_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
