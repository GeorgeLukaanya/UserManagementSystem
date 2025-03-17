<?php
require_once 'includes/header.php';
?>
<?php
session_start(); // Start session

// Function to check if a user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']); // Check if user_id exists in session
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>

<h2>Welcome to User Management System</h2>

<?php if (isLoggedIn()): ?>
    <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>! You are logged in.</p>
    <p>You can <a href="edit_profile.php">edit your details</a>.</p>
<?php else: ?>
    <p>Please <a href="login.php">login</a> or <a href="register.php">register</a> to access your account.</p>
<?php endif; ?>

</body>
</html>


<?php
require_once 'includes/footer.php';
?>