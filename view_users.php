<?php
include 'auth_admin.php';  // Ensure admin authentication
include 'db.php';  // Include database connection

// Fetch all users from the database
$stmt = $pdo->query('SELECT * FROM Users');
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="admin_dashboard.css">
    <title>View All Users</title>
</head>
<body>
    <h1>All Registered Users</h1>

    <!-- Add User Button -->
    <a class="add-user-button" href="add_user.php">Add New User</a>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($users as $user): ?>
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
</body>
</html>
