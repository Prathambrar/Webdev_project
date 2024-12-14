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
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .register-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .register-container h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .register-container label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        .register-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .register-container button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .register-container button:hover {
            background-color: #0056b3;
        }

        .register-container p {
            text-align: center;
            margin-top: 15px;
        }

        .register-container a {
            color: #007bff;
            text-decoration: none;
        }

        .register-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <form method="post" action="register.php">
            <h2>Register</h2>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Register</button>
            <p>Already have an account? <a href="index.php">Login here</a></p>
        </form>
    </div>
</body>
</html>
