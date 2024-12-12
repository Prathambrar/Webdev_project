<?php
session_start();
include 'db.php'; // Include the database connection

// Get car_id from the query parameter
$car_id = $_GET['car_id'] ?? null;

if (!$car_id) {
    die('Car ID is required.');
}

// Fetch car details
$stmt = $pdo->prepare("
    SELECT Cars.*, Images.filename AS image_filename 
    FROM Cars 
    LEFT JOIN Images ON Cars.image_id = Images.image_id 
    WHERE Cars.car_id = :car_id
");
$stmt->execute(['car_id' => $car_id]);
$car = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$car) {
    die('Car not found.');
}

// Fetch comments for the car
$comments_stmt = $pdo->prepare("SELECT * FROM Comments WHERE car_id = :car_id ORDER BY created_at ASC");
$comments_stmt->execute(['car_id' => $car_id]);
$comments = $comments_stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <title><?= htmlspecialchars($car['model']) ?> Details</title>
</head>
<body>
    <header>
        <h1><?= htmlspecialchars($car['model']) ?> Details</h1>
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
            <h2>Car Details</h2>
            <?php if ($car['image_id']): ?>
                <img src="uploads/<?= htmlspecialchars($car['image_filename']) ?>" alt="Car Image" width="500">
            <?php endif; ?>
            <p><strong>Model:</strong> <?= htmlspecialchars($car['model']) ?></p>
            <p><strong>Year:</strong> <?= $car['year'] ?></p>
            <p><strong>Price:</strong> <?= $car['price'] ?></p>
            <p><strong>Description:</strong> <?= htmlspecialchars($car['description']) ?></p>
        </section>

        <section>
    <h2>Comments</h2>
    <?php if ($comments): ?>
        <table border="1" style="width: 100%; margin-bottom: 20px;">
            <tr>
                <th>Username</th>
                <th>Comment</th>
                <th>Date</th>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <th>Action</th>
                <?php endif; ?>
            </tr>
            <?php foreach ($comments as $comment): ?>
                <tr>
                    <td><?= htmlspecialchars($comment['username']) ?></td>
                    <td><?= htmlspecialchars($comment['comment']) ?></td>
                    <td><?= htmlspecialchars($comment['created_at']) ?></td>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <td>
                            <form method="post" action="delete_comment.php" style="display: inline;">
                                <input type="hidden" name="comment_id" value="<?= $comment['comment_id'] ?>">
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this comment?')">Delete</button>
                            </form>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No comments yet.</p>
    <?php endif; ?>

    <!-- Add Comment Form (Users Only) -->
    <?php if (isset($_SESSION['username']) && $_SESSION['role'] !== 'admin'): ?>
        <form method="post" action="add_comment.php">
            <input type="hidden" name="car_id" value="<?= $car['car_id'] ?>">
            <textarea name="comment" placeholder="Add a comment..." required></textarea><br>
            <button type="submit">Submit</button>
        </form>
    <?php endif; ?>
</section>

    </main>

    <footer>
        <p>&copy; 2024 AutoHub. All rights reserved.</p>
    </footer>
</body>
</html>
