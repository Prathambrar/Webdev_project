<?php
include 'auth_admin.php';  // Ensure admin authentication
include 'db.php';  // Include database connection

// Initialize search and sort parameters
$search = $_GET['search'] ?? '';  // General search term
$sort_by = $_GET['sort_by'] ?? 'car_id';    // Default sorting by ID
$order = $_GET['order'] ?? 'ASC';          // Default order is ascending

// Validate sort_by and order inputs
$valid_sort_columns = ['car_id', 'model', 'year', 'price'];
if (!in_array($sort_by, $valid_sort_columns)) {
    $sort_by = 'car_id';
}

$order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

// Prepare the base SQL query
$sql = "SELECT * FROM Cars WHERE (model LIKE :search OR description LIKE :search OR CAST(year AS CHAR) LIKE :search) ORDER BY $sort_by $order";
$params = ['search' => "%$search%"];

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="admin_dashboard.css">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Welcome, Admin!</h1>

    <!-- Logout Button -->
    <form method="post" action="logout.php">
        <button type="submit">Logout</button>
    </form>

    <a class="create-listing-button" href="create_listing.php">Create New Listing</a>
    <a class="manage-users-button" href="view_users.php">View All Users</a>

    <h2>Manage Car Listings</h2>

    <!-- Search Form -->
    <form method="get" action="" style="margin-bottom: 20px;">
        <input type="text" name="search" placeholder="Search by model, description, or year" value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

    <!-- Sort Options -->
    <form method="get" action="" style="margin-bottom: 20px;">
        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
        <label for="sort_by">Sort by:</label>
        <select name="sort_by" id="sort_by">
            <option value="car_id" <?= $sort_by == 'car_id' ? 'selected' : '' ?>>ID</option>
            <option value="model" <?= $sort_by == 'model' ? 'selected' : '' ?>>Model</option>
            <option value="year" <?= $sort_by == 'year' ? 'selected' : '' ?>>Year</option>
            <option value="price" <?= $sort_by == 'price' ? 'selected' : '' ?>>Price</option>
        </select>
        <label for="order">Order:</label>
        <select name="order" id="order">
            <option value="ASC" <?= $order == 'ASC' ? 'selected' : '' ?>>Ascending</option>
            <option value="DESC" <?= $order == 'DESC' ? 'selected' : '' ?>>Descending</option>
        </select>
        <button type="submit">Sort</button>
    </form>

    <!-- Car Listings Table -->
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
