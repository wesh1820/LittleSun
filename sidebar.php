<div class="sidebar">
        <h2><i class="fas fa-columns"></i></h2>
        <?php 
        $sql = "SELECT * FROM locations";
        $result = $conn->query($sql);
        
        $user_role = ""; 
        if (isset($_SESSION['email'])) {
            $email = $_SESSION['email'];
            $sql_role = "SELECT typeOfUser FROM users WHERE email = '$email'";
            $result_role = $conn->query($sql_role);
            if ($result_role && $result_role->num_rows > 0) {
                $row_role = $result_role->fetch_assoc();
                $user_role = $row_role['typeOfUser'];
            }
        }

    if ($user_role === 'admin') {
        echo '<div class="sidebar">';
        echo '<h2><i class="fas fa-columns"></i></h2>';
        echo '<a href="manager.php"><i class="fas fa-user">Managers</i></a>';
        echo '<a href="hub_location.php"><i class="fas fa-map-marker-alt">Locations</i></a>';
        echo '<a href="tasks.php"><i class="fas fa-tasks">Tasks</i></a>';
        echo '<a href="logout.php"><i class="fas fa-sign-out-alt">Logout</i></a>';
        echo '</div>';
    } elseif ($user_role === 'manager') {
        echo '<div class="sidebar">';
        echo '<h2><i class="fas fa-columns"></i></h2>';
        echo '<a href="user.php"><i class="fas fa-user">Users</i></a>';
        echo '<a href="user_tasks.php"><i class="fa fa-tasks"> tasks</i></a>';
        echo '<a href="calender.php"><i class="fa fa-calendar-alt"> calender</i></a>';
        echo '<a href="logout.php"><i class="fas fa-sign-out-alt">Logout</i></a>';
        echo '</div>';
} elseif ($user_role === 'user') {
    echo '<div class="sidebar">';
    echo '<h2><i class="fas fa-columns"></i></h2>';
    echo '<a href="clockin.php"><i class="fas fa-tasks">Clockin</i></a>';
    echo '<a href="taskasuser.php"><i class="fas fa-tasks">Tasks</i></a>';
    echo '<a href="logout.php"><i class="fas fa-sign-out-alt">Logout</i></a>';
    echo '</div>';
}
?>
    </div>