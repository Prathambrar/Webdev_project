<?php
session_start();

// Database connection
$pdo = new PDO('mysql:host=localhost;dbname=autohub', 'root', ''); // Adjust according to your credentials
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Function to generate CAPTCHA
function generateCaptcha() {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $captchaText = '';
    for ($i = 0; $i < 6; $i++) {
        $captchaText .= $characters[rand(0, strlen($characters) - 1)];
    }

    // Store the captcha text in the session to verify later
    $_SESSION['captcha'] = $captchaText;

    // Create an image
    $image = imagecreatetruecolor(150, 50);
    $bgColor = imagecolorallocate($image, 255, 255, 255); // white background
    $textColor = imagecolorallocate($image, 0, 0, 0); // black text
    imagefilledrectangle($image, 0, 0, 150, 50, $bgColor);

    // Add random noise (lines)
    for ($i = 0; $i < 10; $i++) {
        imageline($image, rand() % 150, rand() % 50, rand() % 150, rand() % 50, $textColor);
    }

    // Add the text
    imagestring($image, 5, 50, 15, $captchaText, $textColor);

    // Output the image
    header('Content-Type: image/png');
    imagepng($image);
    imagedestroy($image);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Safely access POST values using null coalescing operator
    $userCaptcha = $_POST['captcha'] ?? ''; 
    $comment = $_POST['comment'] ?? ''; 
    $pageId = $_POST['page_id'] ?? 0; // Default to 0 if not set

    // Validate CAPTCHA
    if ($userCaptcha == $_SESSION['captcha']) {

        // Check if the page_id exists in the database (or car_id if applicable)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM cars WHERE car_id = :page_id"); // Change this if using page_id
        $stmt->bindParam(':page_id', $pageId);
        $stmt->execute();
        $exists = $stmt->fetchColumn();

        if ($exists > 0) {
            // Insert the comment into the database
            $stmt = $pdo->prepare("INSERT INTO comments (comment, page_id) VALUES (:comment, :page_id)");
            $stmt->bindParam(':comment', $comment);
            $stmt->bindParam(':page_id', $pageId);
            $stmt->execute();

            // Success message
            echo "Comment submitted successfully!";
            unset($_SESSION['comment']); // Clear session comment after successful submission
        } else {
            // Error message if page_id does not exist
            echo "Error: Invalid car_id or page_id. Please try again.";
        }

    } else {
        // CAPTCHA failed, show an error message
        echo "Incorrect CAPTCHA. Please try again.";

        // Retain the comment in the session to avoid retyping
        $_SESSION['comment'] = $comment;
    }
}

// Display the comment form with CAPTCHA
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Comment</title>
</head>
<body>
    <h1>Submit Your Comment</h1>
    
    <form action="add_comment.php" method="post">
        <textarea name="comment" required><?php echo htmlspecialchars($_SESSION['comment'] ?? ''); ?></textarea><br>
        
        <!-- CAPTCHA Image -->
        <img src="generate_captcha.php" alt="CAPTCHA"><br>
        
        <!-- CAPTCHA Input -->
        <input type="text" name="captcha" required><br>
        
        <input type="hidden" name="page_id" value="<?php echo $_GET['page_id'] ?? 0; ?>"> <!-- Assuming page_id is passed via URL -->
        
        <button type="submit">Submit Comment</button>
    </form>
    
    <h2>Comments</h2>
    <?php
    // Fetch and display the comments for the specific page
    $pageId = $_GET['page_id'] ?? 0; // Default to 0 if not set
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE page_id = :page_id ORDER BY created_at DESC");
    $stmt->bindParam(':page_id', $pageId);
    $stmt->execute();
    
    $comments = $stmt->fetchAll();
    
    foreach ($comments as $comment) {
        echo "<p>" . htmlspecialchars($comment['comment']) . "</p>";
    }
    ?>
</body>
</html>
