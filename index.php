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

    // Prepare and execute the query to check the user credentials
    $stmt = $pdo->prepare("SELECT user_id, password, role FROM Users WHERE username = ?");
    $stmt->execute([$username]);

    // Fetch the result
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Store session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $username;

        // Redirect to the appropriate dashboard
        if ($user['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: main.php");
        }
        exit;
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Login - AutoHub</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-image: url('backphoto.jpg'); /* Background photo */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: #333;
        }

        header {
            background-color: rgba(0, 123, 255, 0.85); /* Slight transparency */
            color: white;
            text-align: center;
            padding: 20px;
        }

        .login-container {
            margin: 100px auto;
            padding: 20px;
            max-width: 400px;
            background-color: rgba(255, 255, 255, 0.9); /* Light transparent background */
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .login-container h2 {
            margin-bottom: 20px;
            font-size: 24px;
        }

        .login-container label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .login-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .login-container button:hover {
            background-color: #0056b3;
        }

        .login-container p {
            margin-top: 15px;
        }

        .login-container a {
            color: #007bff;
            text-decoration: none;
        }

        .login-container a:hover {
            text-decoration: underline;
        }

        footer {
            text-align: center;
            margin-top: 50px;
            color: #777;
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome to AutoHub</h1>
    </header>

    <div class="login-container">
        <form method="post" action="index.php">
            <h2>Login</h2>
            <?php if (isset($error)): ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php endif; ?>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Login</button>
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </form>
    </div>

    <footer>
        <p>&copy; 2024 AutoHub. All rights reserved.</p>
    </footer>
</body>
</html>
