<?php
require_once './classes/Task.class.php';
require_once './classes/db.class.php';
require_once './classes/Session.class.php';
require_once './classes/User.class.php';

$email = Session::getSession('email');
$conn = $db->getConnection();

$user = new User($conn);
$user_role = $user->getUserRole($email);
Session::setSession('firstname', $user->getID($email));

$sql = "SELECT UserTasks.UserTaskID, UserTasks.UserID, UserTasks.TaskID, users.firstname, users.lastname, Tasks.TaskName, Time_slots.StartSlot, Time_slots.EndSlot, Time_slots.Date, Time_slots.Sick
        FROM UserTasks 
        INNER JOIN users ON UserTasks.UserID = users.id 
        INNER JOIN Tasks ON UserTasks.TaskID = Tasks.TaskID 
        INNER JOIN Time_slots ON UserTasks.UserID = Time_slots.UserID AND UserTasks.TaskID = Time_slots.TaskID
        WHERE Time_slots.Sick = 0
        AND users.email = '$email'";

$result = $conn->query($sql);
$Tasks_and_slots = array();

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $date = $row['Date'];
        $Tasks_and_slots[$date][] = $row;
    }
}

function displayTasksAndSlots($date, $Tasks_and_slots) {
    if (isset($Tasks_and_slots[$date])) {
        foreach ($Tasks_and_slots[$date] as $task_slot) {
            echo "<div class='task'>";
            echo "<strong>{$task_slot['TaskName']}</strong><br>";
            echo "{$task_slot['StartSlot']} - {$task_slot['EndSlot']}<br>";
            echo "</div>";
        }
    }
}

$view = isset($_GET['view']) ? $_GET['view'] : 'month';
$current_week = isset($_GET['week']) ? intval($_GET['week']) : date('W');
$year = date('Y');

$week_start = new DateTime();
$week_start->setISODate($year, $current_week);
$week_end = clone $week_start;
$week_end->modify('+6 days');

$week_start_date = $week_start->format('Y-m-d');
$week_end_date = $week_end->format('Y-m-d');

$month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

$numDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$firstDay = date("N", strtotime("$year-$month-01"));

$selected_day = isset($_GET['day']) ? $_GET['day'] : date('Y-m-d');
if (!strtotime($selected_day)) {
    $selected_day = date('Y-m-d');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Task Calendar</title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <?php include 'sidebar.php'; ?>
    </div>
    <div class="container">
        <h2>
        <?php 
        if ($view === 'week') {
            echo "Week $current_week";
        } elseif ($view === 'day') {
            echo "Day " . date('D, F j, Y', strtotime($selected_day));
        } else {
            echo date('F Y', mktime(0, 0, 0, $month, 1, $year));
        }
        ?>
        </h2>
        <div class="navigation">
            <button class="view-button" onclick="window.location.href='?view=month'">Month</button>
            <button class="view-button" onclick="window.location.href='?view=week'">Week</button>
            <button class="view-button" onclick="window.location.href='?view=day'">Day</button>

            <?php if ($view === 'month'): ?>
                <button class="view-button" onclick="window.location.href='?view=month&year=<?php echo $year; ?>&month=<?php echo $month - 1; ?>'"><</button>
                <button class="view-button" onclick="window.location.href='?view=month&year=<?php echo $year; ?>&month=<?php echo $month + 1; ?>'">></button>
            <?php endif; ?>

            <?php if ($view === 'week'): ?>
                <button class="view-button" onclick="window.location.href='?view=week&week=<?php echo $current_week - 1; ?>'"><</button>
                <button class="view-button" onclick="window.location.href='?view=week&week=<?php echo $current_week + 1; ?>'">></button>
            <?php endif; ?>

            <?php if ($view === 'day'): ?>
                <button class="view-button" onclick="window.location.href='?view=day&day=<?php echo date('Y-m-d', strtotime("-1 day", strtotime($selected_day))); ?>'"><</button>
                <button class="view-button" onclick="window.location.href='?view=day&day=<?php echo date('Y-m-d', strtotime("+1 day", strtotime($selected_day))); ?>'">></button>
            <?php endif; ?>
        </div>

        <?php if ($view === 'month' || !isset($view)): ?>
            <div class="monthly-view">
                <div class="calendar">
                    <div class="calendar-header">
                        <?php echo date('F Y', mktime(0, 0, 0, $month, 1, $year)); ?>
                    </div>
                    <div class="day-header">Sun</div>
                    <div class="day-header">Mon</div>
                    <div class="day-header">Tue</div>
                    <div class="day-header">Wed</div>
                    <div class="day-header">Thu</div>
                    <div class="day-header">Fri</div>
                    <div class="day-header">Sat</div>
                    <?php
                    for ($i = 1; $i <= $numDays; $i++) {
                        $date = date("Y-m-d", strtotime("$year-$month-$i"));
                        $dayOfWeek = date('N', strtotime("$year-$month-$i"));
                        if ($i === 1 && $dayOfWeek !== 7) {
                            for ($j = 0; $j < $dayOfWeek; $j++) {
                                echo "<div class='empty-cell'></div>";
                            }
                        }
                        echo "<div class='day-cell'>$i";
                        displayTasksAndSlots($date, $Tasks_and_slots);
                        echo "</div>";
                        if ($dayOfWeek === 7 || 
                        $i === $numDays) {
                            if ($i === $numDays && $dayOfWeek !== 6) {
                            for ($j = $dayOfWeek; $j < 6; $j++) {
                            echo "<div class='empty-cell'></div>";
                            }
                            }
                            echo "</div>";
                            if ($i !== $numDays) {
                            echo "<div class='calendar-row'>";
                            }
                            }
                            }
                            ?>
                            </div>
                            </div>
                            <?php elseif ($view === 'week'): ?>
                            <div class="weekly-view">
                            <div class="weekly-calendar">
                            <?php
                                             for ($i = 0; $i < 7; $i++) {
                                                 $current_day = date('Y-m-d', strtotime("+$i days", strtotime("Monday this week", strtotime("now"))));
                                             ?>
                            <div class="day">
                            <h4><?php echo date('D', strtotime($current_day)); ?></h4>
                            <?php
                                                     displayTasksAndSlots($current_day, $Tasks_and_slots);
                                                     ?>
                            </div>
                            <?php
                                             }
                                             ?>
                            </div>
                            </div>
                            <?php else: ?>
                            <div class="daily-view">
                            <h2><?php echo date('D, F j, Y', strtotime($selected_day)); ?></h2>
                            <?php
                                         displayTasksAndSlots($selected_day, $Tasks_and_slots);
                                         ?>
                            </div>
                            <?php endif; ?>
                            </div>
                            <script>
                                function addUser(userId) {
                                    window.location.href = 'add_user.php?userId=' + userId;
                                }
                            
                                function viewTasks(userId) {
                                    $.ajax({
                                        url: 'tasks.php',
                                        type: 'GET',
                                        data: { userid: userId },
                                        success: function(response) {
                                            $('#tasks-popup-content').html(response);
                                            $('#tasks-popup').show();
                                        },
                                        error: function(xhr, status, error) {
                                            console.error(xhr.responseText);
                                        }
                                    });
                                }
                            
                                function editUser(userId) {
                                    window.location.href = "edit_user.php?userid=" + userId;
                                }
                            
                                function deleteUser(userId) {
                                    if (confirm("Are you sure you want to delete this user?")) {
                                        $.ajax({
                                            url: "delete_user.php",
                                            type: "POST",
                                            data: { userid: userId },
                                            success: function(response) {
                                                location.reload();
                                            },
                                            error: function(xhr, status, error) {
                                                console.error(xhr.responseText);
                                            }
                                        });
                                    }
                                }
                            
                                $(document).ready(function() {
                                    $('#close-popup').click(function() {
                                        $('#tasks-popup').hide();
                                    });
                                });
                            </script>
                            </body>
                            </html>