<?php
session_start();

// Destroy the session to log the user out
session_unset(); // Removes all session variables
session_destroy(); // Destroys the session

// Redirect to the home page or login page
header("Location: main.php");
exit;
?>
