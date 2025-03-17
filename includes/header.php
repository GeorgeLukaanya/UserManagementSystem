<!-- <?php
session_start();
?> -->
<!DOCTYPE html>
<html>
<head>
    <title>User Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php
                    session_start();
                    if (isset($_SESSION["user_id"])) {
                        include('db.php');

                        $user_id = $_SESSION["user_id"];
                        $sql = "SELECT profile_picture FROM users WHERE id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $user_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if ($row = $result->fetch_assoc()) {
                            $profile_picture = !empty($row["profile_picture"]) ? $row["profile_picture"] : "default.png"; // Use a default image if none is uploaded
                        } else {
                            $profile_picture = "default.png";
                        }

                        $conn->close();
                    ?>
                        <li style="display: flex; align-items: center; gap: 10px;">
                            <a href="edit_profile.php">
                                <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="profile_picture" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                            </a>
                        </li>
                    <?php
                    }
                    ?>

                <li>
                    <a href="logout.php" style="display: inline-block; background-color: red; color: white; padding: 5px 25px; border-radius: 5px; text-decoration: none; font-weight: bold; transition: 0.3s;">
                        Logout
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <a href="login.php" style="display: inline-block; background-color: #007bff; color: white; padding: 5px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; transition: 0.3s;">
                        Login
                    </a>
                </li>
                <li>
                    <a href="register.php" style="display: inline-block; background-color: green; color: white; padding: 5px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; transition: 0.3s;">
                        Register
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
<main>