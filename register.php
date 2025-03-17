<?php
session_start();
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $profile_picture = $_FILES["profile_picture"];

    // Validate and sanitize user input
    $username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<p style='color:red;'>Invalid email format.</p>";
        exit();
    }

    if (empty($username) || empty($email) || empty($password)) {
        echo "<p style='color:red;'>All fields are required.</p>";
        exit();
    }

    // Validate file upload
    $allowed_types = ['image/jpeg', 'image/png'];
    $max_size = 5 * 1024 * 1024; // 5MB

    $file_info = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($file_info, $profile_picture["tmp_name"]);
    finfo_close($file_info);

    $file_extension = strtolower(pathinfo($profile_picture["name"], PATHINFO_EXTENSION));
    $valid_extensions = ['jpg', 'jpeg', 'png'];

    if (!in_array($mime_type, $allowed_types) || !in_array($file_extension, $valid_extensions)) {
        echo "<p style='color:red;'>Only JPG and PNG files are allowed.</p>";
        exit();
    }

    if ($profile_picture["size"] > $max_size) {
        echo "<p style='color:red;'>File size must be less than 5MB.</p>";
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Save profile picture securely
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Create directory if not exists
    }
    $file_name = uniqid('', true) . "." . $file_extension; // Unique filename
    $file_path = $upload_dir . $file_name;

    if (move_uploaded_file($profile_picture["tmp_name"], $file_path)) {
        // Use prepared statements for SQL injection prevention
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, profile_picture) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            echo "<p style='color:red;'>Error preparing statement.</p>";
            exit();
        }

        $stmt->bind_param("ssss", $username, $email, $hashed_password, $file_name);

        if ($stmt->execute()) {
            echo "<p style='color:green;'>Registration successful!</p>";
            header("Location: login.php");
            exit();
        } else {
            echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='color:red;'>Failed to upload profile picture.</p>";
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
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
        .register {
            width: 500px; /* Controls form width */
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin: 50px auto; /* Centers the form */
        }
        .register h2 {
            margin-bottom: 20px;
        }
        .register input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .register button {
            width: 100%;
            padding: 10px;
            background: #333;
            border: none;
            color: white;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .register button:hover {
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
<div class="register">
    <h2>Register</h2>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="file" name="profile_picture" accept="image/jpeg, image/png" required><br>
        <button type="submit">Register</button>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </form>
</div>

</body>
</html>