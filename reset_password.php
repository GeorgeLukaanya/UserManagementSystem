<?php
session_start();
include 'db.php';
include 'includes/functions.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Generate a unique token for password reset
        $token = bin2hex(random_bytes(50));
        $expires = date("Y-m-d H:i:s", time() + 3600); // Token expires in 1 hour

        // Store the token in the database
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires) VALUES (?, ?, ?)");
        $stmt->execute([$email, $token, $expires]);

        // Send the reset link to the user's email
        $reset_link = "http://yourdomain.com/reset_password.php?token=$token";
        $subject = "Password Reset Request";
        $message = "Click the following link to reset your password: $reset_link";
        $headers = "From: no-reply@yourdomain.com";

        if (mail($email, $subject, $message, $headers)) {
            $message = "A password reset link has been sent to your email.";
        } else {
            $error = "Failed to send the reset link. Please try again.";
        }
    } else {
        $error = "No user found with that email address.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <!-- <link rel="stylesheet" href="css/styles.css"> -->
    <style>
   /* Reset Password Container */
.reset-password-container {
    background-color: #ffffff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 500px; /* Set fixed width */
    margin: 100px auto; /* Center the form and add top margin */
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Header Style */
.reset-password-container h2 {
    text-align: center;
    margin-bottom: 20px;
}

/* Input Style */
.reset-password-container input {
    width: 100%; /* Set width to 100% of the container */
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

/* Button Style */
.reset-password-container button {
    width: 100%; /* Set width to 100% of the container */
    padding: 10px;
    border: none;
    background-color: #333;
    color: white;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
}

.reset-password-container button:hover {
    background-color: #555;
}

body{
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(179, 23, 23, 0.1);
    min-height: 600px;
}

    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="reset-password-container">
<h2>Reset Password</h2>
<?php if ($message): ?>
    <p style="color: green;"><?php echo $message; ?></p>
<?php endif; ?>
<?php if ($error): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>
<form method="POST">
    <input type="email" name="email" placeholder="Enter your email" required>
    <button type="submit">Reset Password</button>
</form>
</div>
<?php include 'includes/footer.php'; ?>
</body>
</html>