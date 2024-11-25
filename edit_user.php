<?php
include 'auth_admin.php';  // Ensure admin authentication
include 'db.php';  // Include database connection

// Fetch the user's details
if (!isset($_GET['id'])) {
    header('Location: view_users.php'); // Redirect if no user ID is provided
    exit;
}

$user_id = $_GET['id'];
$stmt = $pdo->prepare('SELECT * FROM Users WHERE user_id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit;
}

// Handle form submission for updating user details (excluding password)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    
    // Set the role based on whether the admin checkbox is checked
    $role = isset($_POST['is_admin']) && $_POST['is_admin'] === 'on' ? 'admin' : 'registered user';
    
    // Debug: Check the role value before executing the query
    // var_dump($role); // This should output either 'admin' or 'registered user'

    // Update user details
    $stmt = $pdo->prepare('UPDATE Users SET username = ?, email = ?, role = ? WHERE user_id = ?');
    $stmt->execute([$username, $email, $role, $user_id]);

    header('Location: view_users.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="admin_dashboard.css">
    <title>Edit User</title>
</head>
<body>
    <h1>Edit User</h1>

    <form method="post" action="">
        <label for="username">Username:</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br>

        <label for="is_admin">
            <input type="checkbox" name="is_admin" id="is_admin" <?= $user['role'] === 'admin' ? 'checked' : '' ?>>
            Make Admin
        </label><br>

        <button type="submit">Update User</button>
    </form>

    <a href="view_users.php">Back to User List</a>
</body>
</html>
