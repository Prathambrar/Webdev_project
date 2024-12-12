<?php
session_start();
include 'db.php'; // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: index.php");
    exit;
}

// Initialize search and sort parameters
$search = $_GET['search'] ?? '';
$sort_by = $_GET['sort_by'] ?? 'car_id'; // Default sorting by ID
$order = $_GET['order'] ?? 'ASC';       // Default order is ascending

// Validate sort_by and order inputs
$valid_sort_columns = ['car_id', 'model', 'year', 'price'];
if (!in_array($sort_by, $valid_sort_columns)) {
    $sort_by = 'car_id';
}

$order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

// Prepare SQL query with search and sorting
$sql = "SELECT * FROM Cars WHERE model LIKE :search OR description LIKE :search OR year LIKE :search ORDER BY $sort_by $order";
$stmt = $pdo->prepare($sql);
$stmt->execute(['search' => "%$search%"]);
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch comments for all cars
$comments_stmt = $pdo->query("SELECT * FROM Comments ORDER BY created_at ASC");
$all_comments = $comments_stmt->fetchAll(PDO::FETCH_ASSOC);

// Group comments by car_id
$comments_by_car = [];
foreach ($all_comments as $comment) {
    $comments_by_car[$comment['car_id']][] = $comment;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <title>AutoHub</title>
</head>
<body>
    <header>
        <h1>Welcome to AutoHub</h1>
        <nav>
            <ul>
                <?php if (isset($_SESSION['username'])): ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="index.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>About AutoHub</h2>
            <p>AutoHub is your go-to platform for buying and selling cars. Explore various car models, read reviews, and make informed decisions. Whether you're a car enthusiast or just looking for your next vehicle, AutoHub is here to help!</p>
        </section>

        <section>
            <h2>Car Listings</h2>

            <!-- Search and Sort -->
            <form method="get" action="" style="margin-bottom: 20px;">
                <input type="text" name="search" placeholder="Search by model, description, or year" value="<?= htmlspecialchars($search) ?>">
                <button type="submit">Search</button>
            </form>
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

            <!-- Display the car listings -->
            <table border="1" style="width: 100%; margin-bottom: 20px;">
    <tr>
        <th>Model</th>
        <th>Year</th>
        <th>Price</th>
        <th>Description</th>
    </tr>
    <?php foreach ($cars as $car): ?>
        <tr>
            <td><a href="car_details.php?car_id=<?= $car['car_id'] ?>"><?= htmlspecialchars($car['model']) ?></a></td>
            <td><?= $car['year'] ?></td>
            <td><?= $car['price'] ?></td>
            <td><?= htmlspecialchars($car['description']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 AutoHub. All rights reserved.</p>
    </footer>
</body>
</html>

