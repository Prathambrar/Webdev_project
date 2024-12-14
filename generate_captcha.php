<?php
session_start();

header('Content-Type: image/png');

// Generate random CAPTCHA text
$captcha_text = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'), 0, 6);
$_SESSION['captcha'] = $captcha_text;

// Create image
$image = imagecreate(120, 40);
$background_color = imagecolorallocate($image, 255, 255, 255); // White background
$text_color = imagecolorallocate($image, 0, 0, 0); // Black text

// Add text to image
imagestring($image, 5, 10, 10, $captcha_text, $text_color);

// Output image
imagepng($image);
imagedestroy($image);
?>
