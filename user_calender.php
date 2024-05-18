<?php
require_once './classes/Task.class.php'; 
require_once './classes/db.class.php';
require_once './classes/Session.class.php';
require_once './classes/User.class.php';

// Start session


$email = Session::getSession('email');

// Instantiate DB class

$conn = $db->getConnection();

$user = new User($conn);
$user_role = $user->getUserRole($email);
Session::setSession('firstname', $user->getFirstName($email));

include 'sidebar.php';

// Fetch tasks and time slots from the database
$sql = "SELECT UserTasks.UserTaskID, UserTasks.UserID, UserTasks.TaskID, users.firstname, users.lastname, tasks.TaskName, time_slots.StartSlot, time_slots.EndSlot, time_slots.Date, time_slots.Sick
        FROM UserTasks 
        INNER JOIN users ON UserTasks.UserID = users.id 
        INNER JOIN tasks ON UserTasks.TaskID = tasks.TaskID 
        INNER JOIN time_slots ON UserTasks.UserID = time_slots.UserID AND UserTasks.TaskID = time_slots.TaskID
        WHERE time_slots.Sick = 0
        AND users.email = '$email'"; // Filter by the user's email

$result = $conn->query($sql);

// Initialize an associative array to store tasks and time slots by date
$tasks_and_slots = array();

if ($result) {
    // Fetch and organize tasks and time slots by date
    while ($row = $result->fetch_assoc()) {
        $date = $row['Date']; // Use the date from time_slots
        $tasks_and_slots[$date][] = $row;
    }
}

// Function to display tasks and time slots for a specific date
function displayTasksAndSlots($date, $tasks_and_slots) {
    // Check if there are any tasks and time slots for the given date
    if (isset($tasks_and_slots[$date])) {
        // Loop through tasks and time slots for the date
        foreach ($tasks_and_slots[$date] as $task_slot) {
            echo "<div class='task'>";
            echo "<strong>{$task_slot['TaskName']}</strong><br>";
            echo "Time: {$task_slot['StartSlot']} - {$task_slot['EndSlot']}<br>";
            echo "Assigned to: {$task_slot['firstname']} {$task_slot['lastname']}";
            echo "</div>";
        }
    }
}

// Establish database connection
$view = isset($_GET['view']) ? $_GET['view'] : 'month';

// Get the current week and year
$current_week = isset($_GET['week']) ? intval($_GET['week']) : date('W');
$year = date('Y');

// Calculate the start and end date of the selected week
$week_start = new DateTime();
$week_start->setISODate($year, $current_week);
$week_end = clone $week_start;
$week_end->modify('+6 days');

$week_start_date = $week_start->format('Y-m-d');
$week_end_date = $week_end->format('Y-m-d');

// Get the current month and year
$month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Get the number of days in the month and the first day of the month
$numDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$firstDay = date("N", strtotime("$year-$month-01"));

// Get the selected day for the daily view
$selected_day = isset($_GET['day']) ? $_GET['day'] : date('Y-m-d');

// Ensure $selected_day is a valid date
if (!strtotime($selected_day)) {
    // If $selected_day is not a valid date, set it to the current date
    $selected_day = date('Y-m-d');
}

// Fetch user days off from the database
$days_off = array();
$sql_days_off = "SELECT user_id, date_off FROM user_days_off";
$result_days_off = $conn->query($sql_days_off);

if ($result_days_off) {
    while ($row = $result_days_off->fetch_assoc()) {
        $days_off[$row['user_id']][] = $row['date_off'];
    }
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

<style>
/* Calendar grid */
.calendar {
    border: 1px solid #ccc;
    display: grid;
    grid-template-columns: repeat(7, 1fr); /* 7 columns for each day of the week */
    gap: 5px; /* Gap between cells */
}

/* Day header */
.day-header {
    text-align: center;
    font-weight: bold;
}

/* Day cell */
.day-cell {
    border: 1px solid #ccc;
    padding: 5px;
    text-align: center;
}

/* Empty cell */
.empty-cell {
    border: none;
}

/* Weekdays */
.weekdays {
    display: flex;
    background-color: #e6e6e6;
}

.weekday {
    flex: 1;
    padding: 10px;
    text-align: center;
}

/* Days */
.days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 2px;
}

.day {
    padding: 10px;
    text-align: center;
    border: 1px solid #ccc;
}

/* Empty cell */
.empty-cell {
    border: none;
}

.task {
    background-color: #f2f2f2;
    padding: 5px;
    margin-bottom: 5px;
}

</style>
</head>
<body>
    
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
</h2><div class="navigation">
<a href="?view=month">Month View</a>
<a href="?view=week">Week View</a>
<a href="?view=day">Day View</a>
<?php if ($view === 'month'): ?>
    <a href="?view=month&year=<?php echo $year; ?>&month=<?php echo $month - 1; ?>">Previous Month</a>
    <a href="?view=month&year=<?php echo $year; ?>&month=<?php echo $month + 1; ?>">Next Month</a>
<?php endif; ?>
<?php if ($view === 'week'): ?>
    <a href="?view=week&week=<?php echo $current_week - 1; ?>">Previous Week</a>
    <a href="?view=week&week=<?php echo $current_week + 1; ?>">Next Week</a>
<?php endif; ?>
<?php if ($view === 'day'): ?>
    <a href="?view=day&day=<?php echo date('Y-m-d', strtotime("-1 day", strtotime($selected_day))); ?>">Previous Day</a>
    <a href="?view=day&day=<?php echo date('Y-m-d', strtotime("+1 day", strtotime($selected_day))); ?>">Next Day</a>
<?php endif; ?>

</div>
<?php if ($view === 'month' || !isset($view)): ?>
<!-- Monthly View -->
<!-- Monthly View -->
<div class="monthly-view">
    <h2>Month View</h2>
    <div class="calendar">
        <!-- Calendar grid for the month -->
        <div class="calendar-header">
            <!-- Header containing month and year -->
            <?php echo date('F Y', mktime(0, 0, 0, $month, 1, $year)); ?>
        </div>
        <!-- Days of the week headers -->
        <div class="day-header">Sun</div>
        <div class="day-header">Mon</div>
        <div class="day-header">Tue</div>
        <div class="day-header">Wed</div>
        <div class="day-header">Thu</div>
        <div class="day-header">Fri</div>
        <div class="day-header">Sat</div>
        <!-- Loop through each day of the month -->
        <?php
        for ($i = 1; $i <= $numDays; $i++) {
            $date = date("Y-m-d", strtotime("$year-$month-$i"));
            // Calculate the day of the week for the current day
            $dayOfWeek = date('N', strtotime("$year-$month-$i"));
            // Add empty cells for days before the first day of the month
            if ($i === 1 && $dayOfWeek !== 7) {
                for ($j = 0; $j < $dayOfWeek; $j++) {
                    echo "<div class='empty-cell'></div>";
                }
            }
            // Add the day cell
            echo "<div class='day-cell'>$i";
            // Display tasks and time slots for the current day
            displayTasksAndSlots($date, $tasks_and_slots);
            echo "</div>";
            // If the last day of the week, close the row
            if ($dayOfWeek === 7 || $i === $numDays) {
                // If it's the last day of the month and not Saturday, add empty cells
                if ($i === $numDays && $dayOfWeek !== 6) {
                    for ($j = $dayOfWeek; $j < 6; $j++) {
                        echo "<div class='empty-cell'></div>";
                    }
                }
                echo "</div>"; // Close the row
                // If not the last day of the month, start a new row
                if ($i !== $numDays) {
                    echo "<div class='calendar-row'>";
                }
            }
        }
        ?>
    </div>
</div>

<?php elseif ($view === 'week'): ?>
        <!-- Weekly View -->
       <!-- Weekly View -->
<div class="weekly-view">
    <div class="weekly-calendar">
        <!-- Loop through each day of the week -->
        <?php
        for ($i = 0; $i < 7; $i++) {
            // Calculate the date for the current day
            $current_day = date('Y-m-d', strtotime("+$i days", strtotime("Monday this week", strtotime("now"))));
        ?>
            <div class="day">
                <h4><?php echo date('D', strtotime($current_day)); ?></h4>
                <!-- Display tasks and time slots for the current day -->
                <?php
                displayTasksAndSlots($current_day, $tasks_and_slots);
                ?>
            </div>
        <?php
        }
        ?>
    </div>
</div>

<?php else: ?>
    <!-- Daily View -->
    <!-- Daily View -->
<div class="daily-view">
    <!-- Display tasks and time slots for the selected day -->
    <h2><?php echo date('D, F j, Y', strtotime($selected_day)); ?></h2>
    <?php
    displayTasksAndSlots($selected_day, $tasks_and_slots);
    ?>
</div>

<?php endif; ?>
</div>
</body>
</html>
