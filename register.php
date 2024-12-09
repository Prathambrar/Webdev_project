<?php
session_start();
include 'db.php'; // Include the database connection

// If the user is already logged in, redirect to the appropriate dashboard
if (isset($_SESSION['username'])) {
    header("Location: main.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash the password before storing it in the database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Prepare and execute the query to insert the user into the database
        $stmt = $pdo->prepare("INSERT INTO Users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, $hashed_password, 'registered user']); // Use 'registered user' as the role

        // Redirect to login page after successful registration
        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        // If there is an error, display it
        echo "Error: " . $e->getMessage();
        // Print the SQL query that caused the error
        echo "<br><b>SQL Query:</b> INSERT INTO Users (username, password, role) VALUES ('$username', '$hashed_password', 'registered user')";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Register</title>
</head>
<body>
    <form method="post" action="register.php">
        <h2>Register</h2>
        <label>Username:</label>
        <input type="text" name="username" required><br>
        <label>Password:</label>
        <input type="password" name="password" required><br>
        <button type="submit">Register</button>
        <p>Already have an account? <a href="index.php">Login here</a></p>
    </form>
</body>
</html>
