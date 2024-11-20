<?php

define('DB_DSN', 'mysql:host=localhost;dbname=autohub;charset=utf8');
define('DB_USER', 'root');
define('DB_PASS', '');

// PDO (PHP Data Objects) is used here for secure database interactions.
try {
    // Attempt to create a new PDO connection to MySQL.
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
} catch (PDOException $e) {
    print "Error: " . $e->getMessage();
    die(); // Stop execution on errors.
    // In production, handle this situation more gracefully.
}
?>
