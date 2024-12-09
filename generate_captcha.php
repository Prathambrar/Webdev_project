<?php
session_start();

function generateCaptcha() {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $captchaText = '';
    for ($i = 0; $i < 6; $i++) {
        $captchaText .= $characters[rand(0, strlen($characters) - 1)];
    }

    $_SESSION['captcha'] = $captchaText;

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

    header('Content-Type: image/png');
    imagepng($image);
    imagedestroy($image);
}

generateCaptcha();
