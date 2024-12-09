<?php
session_start();

// Check if the user is logged in and has the 'admin' role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Redirect to login page if not logged in or not an admin
    header('Location: index.php');
    exit;
}
?>
