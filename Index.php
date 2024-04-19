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
$sql = "SELECT firstname, typeOfUser FROM users WHERE email = '$email'";
$result = $conn->query($sql);

if ($result) {
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['firstname'] = $row['firstname']; // Store first name in session
        $user_role = $row['typeOfUser'];
    } else {
        $_SESSION['firstname'] = "Unknown";
        $user_role = "Unknown";
    }
} else {
    $_SESSION['firstname'] = "Unknown";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <div class="sidebar">
        <h2><i class="fas fa-columns"></i></h2>
        <?php 
    // Perform role check here
    if ($user_role === 'admin') {
        echo '<div class="sidebar">';
        echo '<h2><i class="fas fa-columns"></i></h2>';
        echo '<a href="manager.php"><i class="fas fa-user"></i></a>';
        echo '<a href="hub_location.php"><i class="fas fa-map-marker-alt"></i></a>';
        echo '<a href="logout.php"><i class="fas fa-sign-out-alt"></i></a>';
        echo '</div>';
    } elseif ($user_role === 'manager') {
        echo '<div class="sidebar">';
        echo '<h2><i class="fas fa-columns"></i></h2>';
        echo '<a href="logout.php"><i class="fas fa-sign-out-alt"></i></a>';
        echo '</div>';
    }
?>
    </div>
    <div class="content">
        <h2> Logged in as: <?php echo $_SESSION['firstname']; ?> (<?php echo $user_role; ?>)</h2>
    </div>
</div>


</body>
</html>
