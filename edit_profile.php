<?php
session_start();
include('db.php');

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Fetch user details
$sql = "SELECT username, email, profile_picture FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = trim($_POST["username"]);
    $new_email = trim($_POST["email"]);
    $profile_picture = $_FILES["profile_picture"];

    $update_sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssi", $new_username, $new_email, $user_id);

    if ($stmt->execute()) {
        $_SESSION["username"] = $new_username; // Update session data
        echo "<p style='color:green;'>Profile updated successfully!</p>";
    } else {
        echo "<p style='color:red;'>Error updating profile.</p>";
    }

    // Handle profile picture upload
    if ($profile_picture["size"] > 0) {
        $allowed_types = ["image/jpeg", "image/png"];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($profile_picture["type"], $allowed_types)) {
            echo "<p style='color:red;'>Only JPG and PNG files are allowed.</p>";
        } elseif ($profile_picture["size"] > $max_size) {
            echo "<p style='color:red;'>File size must be less than 5MB.</p>";
        } else {
            $ext = pathinfo($profile_picture["name"], PATHINFO_EXTENSION);
            $file_name = "uploads/profile_" . $user_id . "." . $ext;
            
            if (move_uploaded_file($profile_picture["tmp_name"], $file_name)) {
                $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
                $stmt->bind_param("si", $file_name, $user_id);
                $stmt->execute();
                echo "<p style='color:green;'>Profile picture updated successfully!</p>";
            } else {
                echo "<p style='color:red;'>Failed to upload profile picture.</p>";
            }
        }
    }
}

// Close DB connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <!-- <link rel="stylesheet" href="css/styles.css"> -->
    <style>
        /* Center the form on the page */
form {
    background-color: #ffffff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 500px; /* Set the width to 500px */
    margin: 100px auto; /* Center the form horizontally and give it top margin */
    display: flex;
    flex-direction: column;
}

/* Header style */
h2 {
    text-align: center;
    margin-bottom: 20px;
}

/* Label style */
form label {
    margin-bottom: 5px;
    font-weight: bold;
}

/* Input field styles */
form input {
    width: 100%; /* Make the input fields take the full width of the container */
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

/* Button style */
form button {
    width: 100%; /* Make the button take the full width of the container */
    padding: 10px;
    border: none;
    background-color: #333;
    color: white;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
}

form button:hover {
    background-color: #555;
}

.delete a{
    display: block;
    width: 100%; /* Makes the link take the full width */
    padding: 10px;
    background-color: #ff4d4d; /* Red color */
    color: white;
    text-align: center;
    border-radius: 5px;
    font-size: 16px;
    text-decoration: none;
}
p {
    margin-top: 10px;
    margin-bottom: 10px;
    text-align: left;
}
.delete a:hover {
    background-color: #ff1a1a; /* Darker red on hover */
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
<h2>Edit Profile</h2>
<!-- Profile Update Form -->
<form method="POST" enctype="multipart/form-data">
    <label for="username">Username:</label>
    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

    <label for="email">Email:</label>
    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

    <label for="profile_picture">Profile Picture:</label>
    <input type="file" name="profile_picture" accept="image/jpeg, image/png">

    <button type="submit">Update Profile</button>
    <p>Want to delete your account?</p>
    <div class="delete">
    <a href="delete_account.php">Delete Account</a>
    </div>
</form>
<?php include 'includes/footer.php'; ?>
</body>
</html>
