<?php
session_start();
include 'db.php'; // Include the database connection

// Query to fetch all car listings from the database
$stmt = $pdo->query('SELECT * FROM Cars');
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                <!-- <li><a href="view_listings.php">View Listings</a></li> -->
                <?php if (isset($_SESSION['username'])): ?>
                    <li><a href="logout.php">Logout</a></li>
                    <!-- <li><a href="<?php echo $_SESSION['role'] == 'admin' ? 'admin_dashboard.php' : 'user_dashboard.php'; ?>">
                        Dashboard
                    </a></li> -->
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
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
            <h2>Featured Listings</h2>
            <p>Check out the latest car listings below and find your perfect match.</p>

            <!-- Display the car listings -->
            <table border="1">
                <tr>
                    <th>Model</th>
                    <th>Year</th>
                    <th>Price</th>
                    <th>Description</th>
                </tr>
                <?php foreach ($cars as $car): ?>
                    <tr>
                        <td><?= htmlspecialchars($car['model']) ?></td>
                        <td><?= $car['year'] ?></td>
                        <td><?= $car['price'] ?></td>
                        <td><?= htmlspecialchars($car['description']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <!-- <a href="view_listings.php" class="button">View All Listings</a> -->
        </section>
    </main>

    <footer>
        <p>&copy; 2024 AutoHub. All rights reserved.</p>
    </footer>
</body>
</html>
