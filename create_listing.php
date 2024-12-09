<?php
include 'auth_admin.php';
include 'db.php';

// Function to test for "image-ness"
function is_valid_image($file) {
    // Check if the file is an image by checking the MIME type
    $mime_types = ['image/jpeg', 'image/png', 'image/gif'];
    $image_info = getimagesize($file['tmp_name']);
    
    // Ensure it's an image and has a valid MIME type
    if ($image_info && in_array($image_info['mime'], $mime_types)) {
        return true;
    }
    return false;
}

// Function to resize image
function resize_image($file, $target_width, $target_height) {
    // Get image type
    $image_info = getimagesize($file['tmp_name']);
    $image_type = $image_info[2];

    // Create image resource from the file
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

    // Get original dimensions
    list($width, $height) = getimagesize($file['tmp_name']);

    // Create a new true color image with target dimensions
    $dst_image = imagecreatetruecolor($target_width, $target_height);

    // Resample the original image to the new image
    imagecopyresampled($dst_image, $src_image, 0, 0, 0, 0, $target_width, $target_height, $width, $height);

    // Save the resized image in the uploads directory
    $upload_dir = 'uploads/';
    $filename = uniqid() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    $file_path = $upload_dir . $filename;

    switch ($image_type) {
        case IMAGETYPE_JPEG:
            imagejpeg($dst_image, $file_path);
            break;
        case IMAGETYPE_PNG:
            imagepng($dst_image, $file_path);
            break;
        case IMAGETYPE_GIF:
            imagegif($dst_image, $file_path);
            break;
    }

    // Free up memory
    imagedestroy($src_image);
    imagedestroy($dst_image);

    return $filename;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get data from form submission
    $model = $_POST['model'];
    $year = $_POST['year'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image_filename = null;

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image'];

        // Check for "image-ness"
        if (is_valid_image($image)) {
            // Resize and move image
            $image_filename = resize_image($image, 500, 500); // Resize to 500x500 or your preferred size
            if ($image_filename === false) {
                echo "Error resizing image.";
                exit;
            }
        } else {
            echo "Uploaded file is not a valid image.";
            exit;
        }
    }

    // Prepare the SQL statement for the car listing
    $stmt = $pdo->prepare('INSERT INTO Cars (model, year, price, description, image) VALUES (:model, :year, :price, :description, :image)');
    
    // Bind the values to the prepared statement
    $stmt->bindValue(':model', $model);
    $stmt->bindValue(':year', $year);
    $stmt->bindValue(':price', $price);
    $stmt->bindValue(':description', $description);
    $stmt->bindValue(':image', $image_filename); // Store the filename of the image

    // Execute the statement
    if ($stmt->execute()) {
        header('Location: admin_dashboard.php');
        exit;
    } else {
        echo 'Error creating listing.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Create Listing</title>
</head>
<body>
    <h2>Create New Car Listing</h2>
    <form method="post" action="create_listing.php" enctype="multipart/form-data">
        <label>Model:</label>
        <input type="text" name="model" required><br>
        <label>Year:</label>
        <input type="number" name="year" required><br>
        <label>Price:</label>
        <input type="number" name="price" step="0.01" required><br>
        <label>Description:</label>
        <textarea name="description" required></textarea><br>
        <label>Image (optional):</label>
        <input type="file" name="image" accept="image/*"><br>
        <button type="submit">Create Listing</button>
    </form>
</body>
</html>
