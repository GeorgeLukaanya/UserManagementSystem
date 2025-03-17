<?php
session_start();
include('db.php');

// Check if 'Remember Me' cookie is set and automatically log the user in
if (isset($_COOKIE['remember_me'])) {
    $token = $_COOKIE['remember_me'];

    // Verify the token in the database
    $stmt = $conn->prepare("SELECT user_id, username, email FROM users WHERE remember_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $username, $email);
        $stmt->fetch();

        // Automatically log the user in
        $_SESSION["user_id"] = $user_id;
        $_SESSION["username"] = $username;
        $_SESSION["email"] = $email;
        echo "<p style='color:green;'>You are already logged in! Redirecting...</p>";
        header("refresh:2; url=index.php"); // Redirect to dashboard
        exit();
    } else {
        // Invalid token, clear the cookie
        setcookie('remember_me', '', time() - 3600, '/');
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Check if the user exists
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $hashed_password);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $id;
            $_SESSION["username"] = $username;
            $_SESSION["email"] = $email;

            // Check if 'Remember Me' checkbox is checked
            if (isset($_POST['remember_me'])) {
                // Generate a random token
                $token = bin2hex(random_bytes(64)); // 64 bytes = 128 chars

                // Update the database with the generated token
                $stmt = $conn->prepare("UPDATE users SET remember_token = ? WHERE email = ?");
                $stmt->bind_param("ss", $token, $email);
                $stmt->execute();

                // Set a cookie for 30 days
                setcookie('remember_me', $token, time() + (30 * 24 * 60 * 60), '/'); // 30 days
            }

            echo "<p style='color:green;'>Login successful! Redirecting...</p>";
            header("refresh:2; url=index.php"); // Redirect to dashboard
            exit();
        } else {
            echo "<p style='color:red;'>Incorrect password.</p>";
        }
    } else {
        echo "<p style='color:red;'>No account found with this email.</p>";
    }
    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <!-- <link rel="stylesheet" href="css/style.css"> -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .login {
            width: 500px; /* Controls form width */
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin: 80px auto; /* Centers the form */
        }
        .login h2 {
            margin-bottom: 20px;
        }
        .login input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .login button {
            width: 100%;
            padding: 10px;
            background: #333;
            border: none;
            color: white;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .login button:hover {
            background: #555;
        }
        p {
            margin-top: 10px;
            text-align: left;
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
<div class="login">
        <h2>Login</h2>
        <form method="POST" action="login.php">
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="checkbox" name="remember_me" value="1"> Remember Me
            <button type="submit">Login</button>
            <p>Don't have an account? <a href="register.php">Register</a></p>
            <p><a href="forgot_password.php">Forgot your password?</a></p>
        </form>
    </div>
</body>
</html>