<?php
include 'auth_admin.php';  // Ensure the admin is authenticated
include 'db.php';  // Include the database connection

// Fetch the car details if the 'id' is provided in the URL
if (isset($_GET['id'])) {
    $car_id = $_GET['id'];
    $stmt = $pdo->prepare('
        SELECT Cars.*, Images.image_id, Images.filename AS image_filename 
        FROM Cars 
        LEFT JOIN Images ON Cars.image_id = Images.image_id 
        WHERE Cars.car_id = :car_id
    ');
    $stmt->bindValue(':car_id', $car_id);
    $stmt->execute();
    $car = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$car) {
        echo 'Car not found!';
        exit;
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_image'])) {
        // Handle image deletion
        if (!empty($car['image_filename']) && file_exists('uploads/' . $car['image_filename'])) {
            unlink('uploads/' . $car['image_filename']);
        }

        // Remove the image reference in the database
        $stmt = $pdo->prepare('
            UPDATE Cars 
            SET image_id = NULL 
            WHERE car_id = :car_id
        ');
        $stmt->bindValue(':car_id', $car_id);
        $stmt->execute();

        if (!empty($car['image_id'])) {
            $stmt = $pdo->prepare('
                DELETE FROM Images 
                WHERE image_id = :image_id
            ');
            $stmt->bindValue(':image_id', $car['image_id']);
            $stmt->execute();
        }

        // Reload the page
        header('Location: edit_listing.php?id=' . $car_id);
        exit;
    } elseif (isset($_POST['update'])) {
        // Handle update
        $car_id = $_POST['car_id'];
        $model = $_POST['model'];
        $year = $_POST['year'];
        $price = $_POST['price'];
        $description = $_POST['description'];

        // Update car details
        $stmt = $pdo->prepare('
            UPDATE Cars 
            SET model = :model, year = :year, price = :price, description = :description 
            WHERE car_id = :car_id
        ');
        $stmt->bindValue(':model', $model);
        $stmt->bindValue(':year', $year);
        $stmt->bindValue(':price', $price);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':car_id', $car_id);
        $stmt->execute();

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $image_name = uniqid() . '-' . basename($_FILES['image']['name']);
            $image_path = 'uploads/' . $image_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                // Delete old image file if it exists
                if (!empty($car['image_filename']) && file_exists('uploads/' . $car['image_filename'])) {
                    unlink('uploads/' . $car['image_filename']);
                }

                // Insert or update image in the Images table
                if (!empty($car['image_id'])) {
                    // Update existing image
                    $stmt = $pdo->prepare('
                        UPDATE Images 
                        SET filename = :filename 
                        WHERE image_id = :image_id
                    ');
                    $stmt->bindValue(':filename', $image_name);
                    $stmt->bindValue(':image_id', $car['image_id']);
                    $stmt->execute();
                } else {
                    // Insert new image and link it to the car
                    $stmt = $pdo->prepare('
                        INSERT INTO Images (filename) 
                        VALUES (:filename)
                    ');
                    $stmt->bindValue(':filename', $image_name);
                    $stmt->execute();
                    $new_image_id = $pdo->lastInsertId();

                    $stmt = $pdo->prepare('
                        UPDATE Cars 
                        SET image_id = :image_id 
                        WHERE car_id = :car_id
                    ');
                    $stmt->bindValue(':image_id', $new_image_id);
                    $stmt->bindValue(':car_id', $car_id);
                    $stmt->execute();
                }
            }
        }

        // Redirect to admin dashboard
        header('Location: admin_dashboard.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Edit Listing</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .edit-container {
            background-color: #ffffff;
            padding: 20px;
            width: 100%;
            max-width: 500px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
            color: #333;
        }

        input, textarea, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        button {
            margin-top: 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        img {
            display: block;
            margin: 10px 0;
            max-width: 100%;
            border-radius: 5px;
        }

        .delete-btn {
            background-color: #dc3545;
            color: #fff;
            border: none;
            margin-top: 10px;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="edit-container">
        <h2>Edit Car Listing</h2>
        <form method="post" action="edit_listing.php?id=<?= $car['car_id'] ?>" enctype="multipart/form-data">
            <input type="hidden" name="car_id" value="<?= htmlspecialchars($car['car_id']) ?>">
            <label>Model:</label>
            <input type="text" name="model" value="<?= htmlspecialchars($car['model']) ?>" required>
            <label>Year:</label>
            <input type="number" name="year" value="<?= htmlspecialchars($car['year']) ?>" required>
            <label>Price:</label>
            <input type="number" name="price" step="0.01" value="<?= htmlspecialchars($car['price']) ?>" required>
            <label>Description:</label>
            <textarea name="description" required><?= htmlspecialchars($car['description']) ?></textarea>

            <!-- Display current image -->
            <?php if (!empty($car['image_filename'])): ?>
                <img src="uploads/<?= htmlspecialchars($car['image_filename']) ?>" alt="Car Image">
                <button class="delete-btn" type="submit" name="delete_image" onclick="return confirm('Are you sure you want to delete this image?')">Delete Image</button>
            <?php endif; ?>

            <!-- Upload new image -->
            <label>Change Image:</label>
            <input type="file" name="image" accept="image/*">

            <button type="submit" name="update">Update</button>
        </form>
    </div>
</body>
</html>
