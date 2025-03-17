<?php
// logout.php
session_start();
include('db.php');  // Include your database connection

// Remove user session data
unset($_SESSION["user_id"]);
unset($_SESSION["username"]);
session_destroy();

// Delete the Remember Me cookie if it exists
if (isset($_COOKIE["remember_me"])) {
    setcookie("remember_me", "", time() - 3600, "/", "", false, true);  // Expire the cookie
}

// Redirect to login page
header("Location: login.php");
exit;

$conn->close();
?>
