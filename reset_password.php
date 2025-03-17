<?php
session_start();
include('db.php');

// Check if the token exists
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token is valid
    $stmt = $conn->prepare("SELECT id, reset_token_expiry FROM users WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $reset_token_expiry);
        $stmt->fetch();

        // Check if the token has expired
        if (new DateTime() > new DateTime($reset_token_expiry)) {
            echo "<p style='color:red;'>This password reset link has expired.</p>";
        } else {
            // Allow the user to reset their password
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $new_password = $_POST["new_password"];
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

                // Update the password in the database
                $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
                $stmt->bind_param("si", $new_password_hash, $user_id);
                $stmt->execute();

                echo "<p style='color:green;'>Your password has been reset successfully! Redirecting to login...</p>";
                header("refresh:2; url=login.php"); // Redirect to login
                exit();
            }
        }
    } else {
        echo "<p style='color:red;'>Invalid token.</p>";
    }
} else {
    echo "<p style='color:red;'>No token provided.</p>";
}

$conn->close();
?>

<div class="login">
    <h2>Reset Password</h2>
    <form method="POST" action="reset_password.php?token=<?php echo $_GET['token']; ?>">
        <input type="password" name="new_password" placeholder="New Password" required><br>
        <button type="submit">Reset Password</button>
    </form>
</div>
