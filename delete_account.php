<?php
session_start();
include('db.php');
// Ensure user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Fetch the profile picture path
$sql = "SELECT profile_picture FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Delete profile picture if it exists
if (!empty($user["profile_picture"]) && file_exists($user["profile_picture"])) {
    unlink($user["profile_picture"]); // Remove file from server
}

// Delete user from database
$sql = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
if ($stmt->execute()) {
    session_destroy(); // Log out user
    echo "<p style='color:green;'>Account deleted successfully.</p>";
    header("Location: login.php"); // Redirect after deletion
    exit();
} else {
    echo "<p style='color:red;'>Error deleting account.</p>";
}

// Close connection
$conn->close();
?>
