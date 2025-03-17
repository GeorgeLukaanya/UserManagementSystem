<!-- main-dashboard.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom CSS */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .sidebar {
            height: 100vh;
            background-color: #333;
            padding-top: 20px;
            position: fixed;
            left: 0;
            top: 0;
            width: 220px;
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }

        .sidebar ul li {
            padding: 10px;
            text-align: center;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
        }

        .sidebar ul li a:hover {
            background-color: #575757;
        }

        .main-content {
            margin-left: 240px;
            padding: 20px;
        }

        .card-container {
            display: flex;
            gap: 20px;
            justify-content: space-between;
            margin-top: 20px;
        }

        .card {
            width: 22%;
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .card h5 {
            margin-bottom: 20px;
        }

        footer {
            background-color: #f1f1f1;
            padding: 10px;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <!-- Include Header -->
    <?php include('dashboard/header.php'); ?>

    <!-- Sidebar Section -->
    <?php include('dashboard/sidebar.php'); ?>

    <!-- Main Content Section -->
    <div class="main-content">
        <!-- Include Topbar -->
        <?php include('dashboard/topbar.php'); ?>

        <!-- Dashboard Cards -->
        <div class="card-container">
            <div class="card">
                <h5>Card 1</h5>
                <p>Content for card 1</p>
            </div>
            <div class="card">
                <h5>Card 2</h5>
                <p>Content for card 2</p>
            </div>
            <div class="card">
                <h5>Card 3</h5>
                <p>Content for card 3</p>
            </div>
            <div class="card">
                <h5>Card 4</h5>
                <p>Content for card 4</p>
            </div>
        </div>
    </div>

    <!-- Footer Section -->
    <?php include('dashboard/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
