<?php
include 'auth_admin.php';  // Ensure admin authentication
include 'db.php';  // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash password
    $role = isset($_POST['is_admin']) ? 'admin' : 'registered user'; // Default to "normal" unless the checkbox is checked

    $stmt = $pdo->prepare('INSERT INTO Users (username, email, password, role) VALUES (?, ?, ?, ?)');
    $stmt->execute([$username, $email, $password, $role]);

    header('Location: view_users.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="admin_dashboard.css">
    <title>Add New User</title>
</head>
<body>
    <h1>Add New User</h1>

    <form method="post" action="">
        <label for="username">Username:</label>
        <input type="text" name="username" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br>

        <label for="is_admin">
            <input type="checkbox" name="is_admin" id="is_admin">
            Make Admin
        </label><br>

        <button type="submit">Add User</button>
    </form>
</body>
</html>
