<?php
session_start();
include 'db.php';
include 'includes/functions.php';

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $profile_picture = handleFileUpload($_FILES['profile_picture']);

    if ($profile_picture) {
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, profile_picture = ? WHERE id = ?");
        $stmt->execute([$username, $email, $profile_picture, $user_id]);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->execute([$username, $email, $user_id]);
    }
    $_SESSION['message'] = "Profile updated successfully!";
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <link rel="stylesheet" href="css/style.css">
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
        .profile-container {
            width: 500px; /* Controls form width */
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin: 50px auto; /* Centers the form */
        }
        .profile-container h2 {
            margin-bottom: 20px;
        }
        .profile-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .profile-container button {
            width: 100%;
            padding: 10px;
            background: #333;
            border: none;
            color: white;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
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
            text-align: left;
        }
        .delete a:hover {
            background-color: #ff1a1a; /* Darker red on hover */
        }

        .profile-container button:hover {
            background: #555;
        }
        .profile-container a {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            color: white;
            font-size: 14px;
        }
        .profile-container a:hover {
            text-decoration: underline;
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

<div class="profile-container">
    <h2>Profile</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="username" value="<?php echo $user['username']; ?>" required><br>
        <input type="email" name="email" value="<?php echo $user['email']; ?>" required><br>
        <input type="file" name="profile_picture" accept="image/jpeg, image/png"><br>
        <button type="submit">Update Profile</button>
    </form>
   
</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>
