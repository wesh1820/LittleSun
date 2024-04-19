<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$email = $_SESSION['email'];
$sql = "SELECT typeOfUser FROM users WHERE email = '$email'";
$result = $conn->query($sql);

if ($result) {
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_role = $row['typeOfUser'];
    } else {
        $user_role = "Unknown";
    }
} else {
    $user_role = "Unknown";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        h2 {
            margin-top: 0;
            color: #333;
        }

        .dashboard-link {
            display: block;
            padding: 10px 20px;
            margin-bottom: 10px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .dashboard-link:hover {
            background-color: #0056b3;
        }

        .user-info {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Dashboard</h2>

        <div class="user-info">
            Logged in as: <?php echo $_SESSION['username']; ?> (<?php echo $user_role; ?>)
        </div>

        <a href="add_hub_location.php" class="dashboard-link">Add Hub Location</a>
    </div>
</body>
</html>
