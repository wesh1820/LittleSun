<?php

require_once "config.php";
require_once './classes/Task.class.php'; 
require_once './classes/User.class.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>User Tasks and Time Slots</title>
</head>
<body>
    <div class="sidebar">
        <?php include 'sidebar.php'; ?>
    </div>
<div class="container">
    <?php


    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $userTasks = new User($conn);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['mark_sick'])) {
                $Timeslot_id = intval($_POST['Timeslot_id']);
                $userTasks->setSick($Timeslot_id);
            }

            if (isset($_POST['mark_sick_day'])) {
                $date = $_POST['date'];
                $userTasks->setSickForDay($user_id, $date);
            }
        }

        echo "<h2>My Tasks</h2>";
        echo "<form method='POST' action=''>
                <label for='date'>Date:</label>
                <input type='date' name='date' required>
                <button class='view-button' type='submit' name='mark_sick_day'>Mark as Sick for the Whole Day</button>
              </form>";

        $userTasks->getUserTasksAndTimeSlots($user_id);
    } else {
        echo "You are not logged in!";
    }

    $conn->close();
    ?>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
        $(document).ready(function(){
            $('#close-popup').click(function(){
                $('#tasks-popup').hide();
            });
        });
$(document).ready(function() {
    $(".hamburger-icon").click(function() {
        $(".sidebar").toggleClass("sidebar-open");
    });
    $(".add-button").click(function() {
        $("#popup-content").load("add_user.php");
        $("#myModal").css("display", "block");
    });
    $(".close, .modal").click(function() {
        $("#myModal").css("display", "none");
    });
    $(".modal-content").click(function(event) {
        event.stopPropagation();
    });
});
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="./js/script.js"></script>

</body>
</html>
