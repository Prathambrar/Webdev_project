<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=your_database', 'username', 'password');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Generate CAPTCHA function
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

// Display comments for a specific page
function displayComments($pageId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE page_id = :page_id ORDER BY created_at DESC");
    $stmt->bindParam(':page_id', $pageId);
    $stmt->execute();
    $comments = $stmt->fetchAll();

    foreach ($comments as $comment) {
        echo "<p>" . htmlspecialchars($comment['comment']) . "</p>";
    }
}

// Handle comment form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userCaptcha = $_POST['captcha'];
    $comment = $_POST['comment'];
    $pageId = $_GET['page_id']; // Assuming page_id is passed in the URL

    // Check if the CAPTCHA is correct
    if ($userCaptcha == $_SESSION['captcha']) {
        // Insert the comment into the database
        $stmt = $pdo->prepare("INSERT INTO comments (comment, page_id) VALUES (:comment, :page_id)");
        $stmt->bindParam(':comment', $comment);
        $stmt->bindParam(':page_id', $pageId);
        $stmt->execute();

        echo "Comment submitted successfully!";
        $_SESSION['comment'] = ''; // Clear the comment session after submission
    } else {
        echo "Incorrect CAPTCHA. Please try again.";

        // Retain the comment in the session so the user doesn't have to retype it
        $_SESSION['comment'] = $comment;
    }
}

// Display the comment form and CAPTCHA
$pageId = $_GET['page_id']; // Assuming page_id is passed in the URL
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
    
    <form action="comment.php?page_id=<?php echo $pageId; ?>" method="post">
        <textarea name="comment" required><?php echo htmlspecialchars($_SESSION['comment'] ?? ''); ?></textarea><br>
        
        <img src="generate_captcha.php" alt="CAPTCHA"><br>
        
        <input type="text" name="captcha" required><br>
        
        <button type="submit">Submit Comment</button>
    </form>

    <h2>Comments</h2>
    <?php displayComments($pageId); ?>
</body>
</html>
