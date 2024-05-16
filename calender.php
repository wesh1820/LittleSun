<?php
require_once './classes/Task.class.php'; 
require_once './classes/db.class.php';
require_once './classes/SessionManager.class.php';
require_once './classes/User.class.php';

$email = SessionManager::getSession('email');
$user = new User($db->getConnection());
$user_role = $user->getUserRole($email);
SessionManager::setSession('firstname', $user->getFirstName($email));

$db->closeConnection();
include 'sidebar.php';

// Function to fetch user tasks from the database
function fetchUserTasks($conn) {
    $sql_user_tasks = "SELECT UserTasks.UserTaskID, UserTasks.UserID, UserTasks.TaskID, UserTasks.Date, UserTasks.StartTime, UserTasks.EndTime, users.firstname, users.lastname, tasks.TaskName FROM UserTasks 
    INNER JOIN users ON UserTasks.UserID = users.id 
    INNER JOIN tasks ON UserTasks.TaskID = tasks.TaskID";
    $result_user_tasks = $conn->query($sql_user_tasks);

    $tasks = array();

    if ($result_user_tasks->num_rows > 0) {
        // Organize tasks by date
        while ($row = $result_user_tasks->fetch_assoc()) {
            $date = date("Y-m-d", strtotime($row['Date']));
            $tasks[$date][] = array(
                'start_time' => $row['StartTime'],
                'end_time' => $row['EndTime'],
                'task_name' => $row['TaskName'],
                'user_id' => $row['UserID'], // Store user ID directly
                'user_name' => $row['firstname'] . ' ' . $row['lastname']
            );
        }
    }

    return $tasks;
}

// Fetch user tasks from the database
$tasks = fetchUserTasks($conn);

// Get the current month and year
$month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Get the number of days in the month and the first day of the month
$numDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$firstDay = date("N", strtotime("$year-$month-01"));

// Fetch user days off from the database
$days_off = array();
$sql_days_off = "SELECT user_id, date_off FROM user_days_off";
$result_days_off = $conn->query($sql_days_off);

if ($result_days_off->num_rows > 0) {
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
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

    h2 {
        text-align: center;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px auto;
    }

    th, td {
        padding: 10px;
        text-align: left;
        border: 1px solid #ccc;
    }

    th {
        background-color: #f2f2f2;
    }

    td {
        vertical-align: top;
    }

    .navigation {
        text-align: center;
        margin-bottom: 20px;
    }

    .navigation a {
        padding: 5px 10px;
        margin: 0 5px;
        text-decoration: none;
        color: #333;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #f2f2f2;
    }

    .navigation a:hover {
        background-color: #ddd;
    }

    .task-container {
        margin-bottom: 20px;
    }

    .task {
        margin-bottom: 10px;
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f9f9f9;
    }

    .task strong {
        color: #333;
    }

    .task p {
        margin: 5px 0;
    }
</style>
</head>
<body>
<div class="container">
<h2>Task Calendar - <?php echo date('F Y', mktime(0, 0, 0, $month, 1, $year)); ?></h2>

<div class="navigation">
    <a href="?month=<?php echo ($month == 1) ? 12 : ($month - 1); ?>&year=<?php echo ($month == 1) ? ($year - 1) : $year; ?>">Previous Month</a>
    <a href="?month=<?php echo ($month == 12) ? 1 : ($month + 1); ?>&year=<?php echo ($month == 12) ? ($year + 1) : $year; ?>">Next Month</a>
</div>

<table>
    <tr>
        <th>Sun</th>
        <th>Mon</th>
        <th>Tue</th>
        <th>Wed</th>
        <th>Thu</th>
        <th>Fri</th>
        <th>Sat</th>
    </tr>
    <?php
    $start_date = date('Y-m-01', strtotime("$year-$month"));
    $end_date = date('Y-m-t', strtotime("$year-$month"));

    $current_date = $start_date;
    $days_in_month = intval(date('t', strtotime($current_date)));
    $first_day_of_week = intval(date('N', strtotime($start_date)));
    $current_day_of_week = $first_day_of_week;

    for ($i = 1; $i <= $days_in_month; $i++) {
        if ($current_day_of_week === 1) {
            echo "<tr>";
        }

        echo "<td>";
        echo "<strong>$i</strong><br>";

        $current_date_tasks = isset($tasks[$current_date]) ? $tasks[$current_date] : array();

        $tasks_to_display = array();

        if (!empty($current_date_tasks)) {
            foreach ($current_date_tasks as $task) {
                $start_time = $task['start_time'];
                $end_time = $task['end_time'];
                $task_name = $task['task_name'];
                $user_id = $task['user_id'];
                $user_name = $task['user_name'];
                
                // Check if the user is on a day off
                $is_day_off = isset($days_off[$user_id]) && in_array($current_date, $days_off[$user_id]);

                // Add the task to the list of tasks to display if the user is not on a day off
                if (!$is_day_off) {
                    $tasks_to_display[] = $task;
                }
            }
        }

        // Display tasks
        if (!empty($tasks_to_display)) {
            foreach ($tasks_to_display as $task) {
                $task_name = $task['task_name'];
                $start_time = $task['start_time'];
                $end_time = $task['end_time'];
                $user_name = $task['user_name'];
                
                echo "<div class='task'><strong>$task_name</strong><br>";
                echo "<p><strong>Time:</strong> $start_time - $end_time</p>";
                echo "<p><strong>User:</strong> $user_name</p></div>";
            }
        } else {
            echo "<div class='task'>No tasks</div>";
        }

        echo "</td>";

        if ($current_day_of_week === 7 || $i === $days_in_month) {
            echo "</tr>";
            $current_day_of_week = 1;
        } else {
            $current_day_of_week++;
        }

        $current_date = date('Y-m-d', strtotime("$current_date +1 day"));
    }
    ?>
</table>
</div>
</div>
</body>
</html>
