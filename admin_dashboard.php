<?php
include 'auth_admin.php';  // Ensure admin authentication
include 'db.php';  // Include database connection

// Determine which tab is active
$active_tab = $_GET['tab'] ?? 'cars';  // Default to 'cars'

// Common variables for both tabs
$search = $_GET['search'] ?? '';       // General search term
$sort_by = $_GET['sort_by'] ?? 'car_id'; // Default sorting column
$order = $_GET['order'] ?? 'ASC';      // Default order is ascending

// Validate sorting inputs
if ($active_tab === 'cars') {
    $valid_sort_columns = ['car_id', 'model', 'year', 'price'];
    if (!in_array($sort_by, $valid_sort_columns)) {
        $sort_by = 'car_id';
    }
    $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

    // Fetch car data
    $sql = "SELECT * FROM Cars WHERE (model LIKE :search OR description LIKE :search OR CAST(year AS CHAR) LIKE :search) ORDER BY $sort_by $order";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['search' => "%$search%"]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($active_tab === 'users') {
    $valid_sort_columns = ['user_id', 'username', 'role'];
    if (!in_array($sort_by, $valid_sort_columns)) {
        $sort_by = 'user_id';
    }
    $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

    // Fetch user data
    $sql = "SELECT * FROM Users WHERE (username LIKE :search OR email LIKE :search OR CAST(user_id AS CHAR) LIKE :search) ORDER BY $sort_by $order";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['search' => "%$search%"]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
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

    <!-- Navigation Tabs -->
    <nav>
        <?php if ($active_tab === 'cars'): ?>
            <a class="manage-button" href="?tab=users&search=<?= urlencode($search) ?>&sort_by=<?= urlencode($sort_by) ?>&order=<?= urlencode($order) ?>">View All Users</a>
        <?php elseif ($active_tab === 'users'): ?>
            <a class="manage-button" href="?tab=cars&search=<?= urlencode($search) ?>&sort_by=<?= urlencode($sort_by) ?>&order=<?= urlencode($order) ?>">Manage Cars</a>
        <?php endif; ?>
    </nav>

    <hr>

    <!-- Content for Cars -->
    <?php if ($active_tab === 'cars'): ?>
        <h2>Manage Car Listings</h2>
        <a class="create-listing-button" href="create_listing.php">Create New Listing</a>

        <!-- Search and Sort -->
        <form method="get" action="">
            <input type="hidden" name="tab" value="cars">
            <input type="text" name="search" placeholder="Search by model, description, or year" value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>

            <label for="sort_options">Sort by:</label>
            <select name="sort_by" id="sort_options" onchange="this.form.submit()">
                <option value="car_id" <?= $sort_by == 'car_id' ? 'selected' : '' ?>>ID</option>
                <option value="model" <?= $sort_by == 'model' ? 'selected' : '' ?>>Model</option>
                <option value="year" <?= $sort_by == 'year' ? 'selected' : '' ?>>Year</option>
                <option value="price" <?= $sort_by == 'price' ? 'selected' : '' ?>>Price</option>
            </select>

            <label for="order_options">Order by:</label>
            <select name="order" id="order_options" onchange="this.form.submit()">
                <option value="ASC" <?= $order == 'ASC' ? 'selected' : '' ?>>Ascending ↑</option>
                <option value="DESC" <?= $order == 'DESC' ? 'selected' : '' ?>>Descending ↓</option>
            </select>
        </form>

        <!-- Display Cars -->
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Model</th>
                <th>Year</th>
                <th>Price</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($items as $car): ?>
                <tr>
                    <td><?= $car['car_id'] ?></td>
                    <td><a href="car_details.php?car_id=<?= $car['car_id'] ?>"><?= htmlspecialchars($car['model']) ?></a></td>
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
    <?php endif; ?>

    <!-- Content for Users -->
    <?php if ($active_tab === 'users'): ?>
        <h2>Manage Users</h2>
        <a class="add-user-button" href="add_user.php">Add New User</a>

        <!-- Search and Sort -->
        <form method="get" action="">
            <input type="hidden" name="tab" value="users">
            <input type="text" name="search" placeholder="Search by username, ID, or email" value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>

            <label for="sort_options">Sort by:</label>
            <select name="sort_by" id="sort_options" onchange="this.form.submit()">
                <option value="user_id" <?= $sort_by == 'user_id' ? 'selected' : '' ?>>ID</option>
                <option value="username" <?= $sort_by == 'username' ? 'selected' : '' ?>>Username</option>
                <option value="role" <?= $sort_by == 'role' ? 'selected' : '' ?>>Role</option>
            </select>

            <label for="order_options">Order by:</label>
            <select name="order" id="order_options" onchange="this.form.submit()">
                <option value="ASC" <?= $order == 'ASC' ? 'selected' : '' ?>>Ascending ↑</option>
                <option value="DESC" <?= $order == 'DESC' ? 'selected' : '' ?>>Descending ↓</option>
            </select>
        </form>

        <!-- Display Users -->
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($items as $user): ?>
                <tr>
                    <td><?= $user['user_id'] ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td>
                        <a href="edit_user.php?id=<?= $user['user_id'] ?>">Edit</a>
                        <a href="delete_user.php?id=<?= $user['user_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
