<?php require_once './config.php'; // Assuming this file initializes $conn
?>

<div class="sidebar">
        <h2><i class="fas fa-columns"></i></h2>
        <?php 
        $sql = "SELECT * FROM locations";
        $result = $conn->query($sql);
        
        $user_role = ""; 
        if (isset($_SESSION['email'])) {
            $email = $_SESSION['email'];
            $sql_role = "SELECT typeOfUser FROM users WHERE email = '$email'";

            // $role = User::getRole($email)
            $result_role = $conn->query($sql_role);
            if ($result_role && $result_role->num_rows > 0) {
                $row_role = $result_role->fetch_assoc();
                $user_role = $row_role['typeOfUser'];
            }
        }

    if(1===1)

    if ($user_role === 'admin') {
        echo '<div class="sidebar">';
        echo '<h2><i class="fas fa-columns"></i></h2>';
        echo '<a href="manager.php"><i class="fas fa-user">Managers</i></a>';
        echo '<a href="hub_location.php"><i class="fas fa-map-marker-alt">Locations</i></a>';
        echo '<a href="task.php"><i class="fas fa-tasks">Tasks</i></a>';
        echo '<a href="sick.php"><i class="fas fa-tasks">Sick</i></a>';
        echo '<a href="logout.php"><i class="fas fa-sign-out-alt">Logout</i></a>';
        echo '</div>';
    } elseif ($user_role === 'manager') {
        echo '<div class="sidebar">';
        echo '<h2><i class="fas fa-columns"></i></h2>';
        echo '<a href="user.php"><i class="fas fa-user">Users</i></a>';
        echo '<a href="Tasky.php"><i class="fa fa-tasks"> tasks</i></a>';
        echo '<a href="calender.php"><i class="fa fa-calendar-alt"> calender</i></a>';
        echo '<a href="view_time_off.php"><i class="fa fa-calendar-alt"> Time off</i></a>';
        echo '<a href="logout.php"><i class="fas fa-sign-out-alt">Logout</i></a>';
        echo '</div>';
} elseif ($user_role === 'user') {
    echo '<div class="sidebar">';
    echo '<h2><i class="fas fa-columns"></i></h2>';
    echo '<a href="clockin.php"><i class="fas fa-tasks">Clockin</i></a>';
    echo '<a href="taskasuser.php"><i class="fas fa-tasks">Tasks</i></a>';
    echo '<a href="user_calender.php"><i class="fas fa-tasks">Calender</i></a>';
    echo '<a href="ask_time_off.php"><i class="fas fa-tasks">Time Off</i></a>';
    echo '<a href="logout.php"><i class="fas fa-sign-out-alt">Logout</i></a>';
    echo '</div>';
}
?>
    </div>