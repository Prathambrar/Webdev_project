<?php
include 'auth_admin.php';
include 'db.php';

// Function to validate image-ness
function is_valid_image($file) {
    $mime_types = ['image/jpeg', 'image/png', 'image/gif'];
    $image_info = getimagesize($file['tmp_name']);
    return $image_info && in_array($image_info['mime'], $mime_types);
}

// Function to resize and save image
function resize_image($file, $target_width, $target_height) {
    $image_info = getimagesize($file['tmp_name']);
    $image_type = $image_info[2];

    switch ($image_type) {
        case IMAGETYPE_JPEG:
            $src_image = imagecreatefromjpeg($file['tmp_name']);
            break;
        case IMAGETYPE_PNG:
            $src_image = imagecreatefrompng($file['tmp_name']);
            break;
        case IMAGETYPE_GIF:
            $src_image = imagecreatefromgif($file['tmp_name']);
            break;
        default:
            return false;
    }

    list($width, $height) = $image_info;
    $dst_image = imagecreatetruecolor($target_width, $target_height);
    imagecopyresampled($dst_image, $src_image, 0, 0, 0, 0, $target_width, $target_height, $width, $height);

    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    $filename = uniqid() . '.jpg'; // Save as JPEG
    $file_path = $upload_dir . $filename;
    imagejpeg($dst_image, $file_path, 90); // 90% quality

    imagedestroy($src_image);
    imagedestroy($dst_image);

    return $filename;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $model = $_POST['model'];
    $year = $_POST['year'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image_id = null;

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image = $_FILES['image'];

        if (is_valid_image($image)) {
            $filename = resize_image($image, 500, 500);
            if ($filename) {
                // Save image information to Images table
                $stmt = $pdo->prepare('INSERT INTO Images (filename) VALUES (:filename)');
                $stmt->execute(['filename' => $filename]);
                $image_id = $pdo->lastInsertId();
            } else {
                echo "Failed to resize and save image.";
                exit;
            }
        } else {
            echo "Invalid image file.";
            exit;
        }
    }

    // Insert car listing
    $stmt = $pdo->prepare('INSERT INTO Cars (model, year, price, description, image_id) VALUES (:model, :year, :price, :description, :image_id)');
    $stmt->execute([
        'model' => $model,
        'year' => $year,
        'price' => $price,
        'description' => $description,
        'image_id' => $image_id,
    ]);

    header('Location: admin_dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Create Listing</title>
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

        .form-container {
            background-color: #ffffff;
            padding: 20px;
            max-width: 500px;
            width: 100%;
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
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Create New Car Listing</h2>
        <form method="post" action="create_listing.php" enctype="multipart/form-data">
            <label>Model:</label>
            <input type="text" name="model" required>
            <label>Year:</label>
            <input type="number" name="year" required>
            <label>Price:</label>
            <input type="number" name="price" step="0.01" required>
            <label>Description:</label>
            <textarea name="description" required></textarea>
            <label>Image (optional):</label>
            <input type="file" name="image" accept="image/*">
            <button type="submit">Create Listing</button>
        </form>
    </div>
</body>
</html>
