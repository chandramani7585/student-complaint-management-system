<?php
session_start();
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

// Expire session cookie by setting its expiration to an hour ago
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

header("Location: login.php"); // Redirect to the login page
exit();
?>
