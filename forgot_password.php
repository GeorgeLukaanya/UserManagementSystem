<?php
session_start();
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Generate a unique token
        $token = bin2hex(random_bytes(50)); // 50 bytes = 100 chars

        // Store the token in the database
        $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?");
        $stmt->bind_param("ss", $token, $email);
        $stmt->execute();

        // Send email to the user with the reset link
        $reset_link = "http://yourwebsite.com/reset_password.php?token=$token";

        // Send the email (using PHP mail function or any email service)
        $subject = "Password Reset Request";
        $message = "Click the link to reset your password: $reset_link";
        $headers = "From: no-reply@yourwebsite.com";

        if (mail($email, $subject, $message, $headers)) {
            echo "<p style='color:green;'>An email has been sent to your address with a password reset link.</p>";
        } else {
            echo "<p style='color:red;'>Failed to send the reset email. Please try again later.</p>";
        }
    } else {
        echo "<p style='color:red;'>No account found with this email.</p>";
    }
    $stmt->close();
}

$conn->close();
?>

<div class="login">
    <h2>Forgot Password</h2>
    <form method="POST" action="forgot_password.php">
        <input type="email" name="email" placeholder="Enter your email" required><br>
        <button type="submit">Submit</button>
    </form>
</div>
