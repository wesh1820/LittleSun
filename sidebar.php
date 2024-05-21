<?php
require_once './config.php';

$user_role = ""; 
$profile_pic = "";
$user_firstname = "";

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql_role = "SELECT typeOfUser, profilepic, firstname FROM users WHERE id = '$user_id'";

    $result_role = $conn->query($sql_role);
    if ($result_role && $result_role->num_rows > 0) {
        $row_role = $result_role->fetch_assoc();
        $user_role = $row_role['typeOfUser'];
        $profile_pic = $row_role['profilepic'];
        $user_firstname = $row_role['firstname'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Page Title</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<div class="hamburger-icon">
    <i class="fas fa-bars"></i>
</div>
<div class="sidebar">
    <div class="logo-sidebar">
        <img class="logoimg" src="./uploads/Logo.svg" alt="Logo">
    </div> 
    <div class="profile-pic">
        <div class="profile-img-container">
            <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture" class="profile-img">
            <p href="#" class="edit-profile-link" onclick="openPopup()"><i class="fas fa-pencil-alt"></i></p>
        </div>
        <?php if ($user_role) { ?>
            <div class="profile-info">
                <p><?php echo htmlspecialchars($user_role); ?>:</p>
                <p><?php echo htmlspecialchars($user_firstname); ?></p>
            </div>
        <?php } ?>
    </div>

    <div class="sidebar-content">
        <?php 
if ($user_role === 'admin') {
    echo '<a href="manager.php"><i class="fas fa-users"></i> Managers</a>';
    echo '<a href="hub_location.php"><i class="fas fa-map-marked-alt"></i> Locations</a>';
    echo '<a href="task.php"><i class="fas fa-tasks"></i> Tasks</a>';
    echo '<a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>';
} elseif ($user_role === 'manager') {
    echo '<a href="user.php"><i class="fas fa-user"></i> Users</a>';
    echo '<a href="tasky.php"><i class="fas fa-tasks"></i> Tasks</a>';
    echo '<a href="calender.php"><i class="fas fa-calendar-alt"></i> Calendar</a>';
    echo '<a href="report.php"><i class="fas fa-chart-bar"></i> Report</a>';
    echo '<a href="sick.php"><i class="fas fa-bed"></i> Sick</a>';
    echo '<a href="view_time_off.php"><i class="fas fa-hourglass-half"></i> Time Off</a>';
    echo '<a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>';
} elseif ($user_role === 'user') {
    echo '<a href="clockin.php"><i class="fas fa-clock"></i> Clockin</a>';
    echo '<a href="taskasuser.php"><i class="fas fa-tasks"></i> Tasks</a>';
    echo '<a href="user_calender.php"><i class="fas fa-calendar-alt"></i> Calendar</a>';
    echo '<a href="ask_time_off.php"><i class="fas fa-hourglass-half"></i> Time Off</a>';
    echo '<a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>';
}
        ?>
    </div>
</div>
<div id="editProfilePopup" class="popup">
    <div class="popup-content">
        <div id="editProfileContent"></div>
    </div>
</div>
<script src="./js/script.js"></script>
</body>
</html>
