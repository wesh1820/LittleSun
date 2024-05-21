<?php
require_once './classes/User.class.php';
require_once './classes/db.class.php';

$connection = $db->getConnection();
$user = new User($connection);
$loggedInUserId = $_SESSION['user_id'];
$loggedInUserLocationId = $user->getLocationId($loggedInUserId);
$users = $user->getUsersByLocation($loggedInUserLocationId);

$host = 'localhost';
$db = 'Littlesun';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

function getSickHours($pdo, $date, $userId) {
    $query = "SELECT SUM(TimeSTAMPDIFF(HOUR, Start_Time, End_Time)) AS total_sick_hours
              FROM Timeoff
              WHERE Timeoff_reason = 'Sick'
              AND Start_date <= :date";
    if ($userId !== null) {
        $query .= " AND UserID = :user_id";
    }
    $stmt = $pdo->prepare($query);
    $params = [':date' => $date];
    if ($userId !== null) {
        $params[':user_id'] = $userId;
    }
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total_sick_hours'] ?? 0;
}

function getTimeOffHours($pdo, $date, $userId) {
    $query = "SELECT SUM(TimeSTAMPDIFF(HOUR, StartSlot, EndSlot)) AS total_Time_off_hours
              FROM Time_slots
              WHERE Sick = 1
              AND Date = :date";
    if ($userId !== null) {
        $query .= " AND UserID = :user_id";
    }
    $stmt = $pdo->prepare($query);
    $params = [':date' => $date];
    if ($userId !== null) {
        $params[':user_id'] = $userId;
    }
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total_Time_off_hours'] ?? 0;
}

function displayReport($pdo, $filters) {
    $query = "SELECT u.firstname, u.lastname, ts.TaskID, t.TaskName, ts.Date, ts.StartSlot, ts.EndSlot, ts.UserID
              FROM users u
              JOIN Time_slots ts ON u.id = ts.UserID
              JOIN Tasks t ON ts.TaskID = t.TaskID
              WHERE 1=1";
    
    $params = [];
    
    if (!empty($filters['user_id'])) {
        $query .= " AND u.id = :user_id";
        $params[':user_id'] = $filters['user_id'];
    }
    
    if (!empty($filters['start_date'])) {
        $query .= " AND ts.Date >= :start_date";
        $params[':start_date'] = $filters['start_date'];
    }
    
    if (!empty($filters['end_date'])) {
        $query .= " AND ts.Date <= :end_date";
        $params[':end_date'] = $filters['end_date'];
    }
    
    if (!empty($filters['task_type'])) {
        $query .= " AND t.TaskID = :task_type";
        $params[':task_type'] = $filters['task_type'];
    }
    
    $query .= " ORDER BY ts.Date ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_sick_hours = 0;
    $total_Time_off_hours = 0;
    
    if (count($results) > 0) {
        echo "<table border='1'>";
        echo "<tr><th>User</th><th>Task</th><th>Date</th><th>Start Time</th><th>End Time</th><th>Sick Hours</th><th>Time off Hours</th><th>OverTime (hours)</th></tr>";
        foreach ($results as $row) {
            
            $sickHours = getSickHours($pdo, $row['Date'], $row['UserID']);
            $total_sick_hours += $sickHours; 
            $TimeOffHours = getTimeOffHours($pdo, $row['Date'], $row['UserID']);
            $total_Time_off_hours += $TimeOffHours; 
            
            echo "<tr>
                    <td>{$row['firstname']} {$row['lastname']}</td>
                    <td>{$row['TaskName']}</td>
                    <td>{$row['Date']}</td>
                    <td>{$row['StartSlot']}</td>
                    <td>{$row['EndSlot']}</td>
                    <td>{$TimeOffHours}</td>
                    <td>{$sickHours}</td>
                    <td>Calculated overTime hours</td>
                  </tr>";
        }

        echo "<tr><td colspan='5'><strong>Total Time Off:</strong></td><td><strong>{$total_sick_hours}</strong></td></tr>";
        echo "<tr><td colspan='5'><strong>Total Sick Hours Hours:</strong></td><td><strong>{$total_Time_off_hours}</strong></td></tr>";
        echo "</table>";
    } else {
        echo "<p>No records found for the given criteria.</p>";
    }
}

function getUsers($pdo) {
    $stmt = $pdo->prepare("SELECT id, firstname, lastname FROM users WHERE status = 1");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTaskTypes($pdo) {

$stmt = $pdo->prepare("SELECT TaskID, TaskName FROM Tasks");
$stmt->execute();
return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function calculateOverTime($pdo, $userId, $month) {
$query = "SELECT SUM(TimeSTAMPDIFF(HOUR, ts.StartSlot, ts.EndSlot)) AS total_hours
          FROM Time_slots ts
          WHERE ts.UserID = :user_id
          AND MONTH(ts.Date) = :month
          AND ts.EndSlot > '17:00:00'";

$stmt = $pdo->prepare($query);
$stmt->execute([
    ':user_id' => $userId,
    ':month' => $month,
]);

$result = $stmt->fetch(PDO::FETCH_ASSOC);
$total_hours = $result['total_hours'] ?? 0;
$standard_hours_per_month = 160;
$overTime_hours = $total_hours - $standard_hours_per_month;

return $overTime_hours;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Reports Dashboard</title>
<link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="sidebar">
    <?php include 'sidebar.php'; ?>
</div>
<div class="container">
<nav>
<h2>Report</h2>
<form method="GET" style="display: flex; align-items: center;">
    <label for="user_id">User:</label>
    <select name="user_id" id="user_id">
        <option value="">All</option>
        <?php
        $users = getUsers($pdo);
        foreach ($users as $user) {
            echo "<option value=\"{$user['id']}\">{$user['firstname']} {$user['lastname']}</option>";
        }
        ?>
    </select>
    
    <label for="start_date">Start Date:</label>
    <input type="date" name="start_date" id="start_date">
    
    <label for="end_date">End Date:</label>
    <input type="date" name="end_date" id="end_date">
    
    <label for="task_type">Task Type:</label>
    <select name="task_type" id="task_type">
        <option value="">All</option>
        <?php
        $taskTypes = getTaskTypes($pdo);
        foreach ($taskTypes as $type) {
            echo "<option value=\"{$type['TaskID']}\">{$type['TaskName']}</option>";
        }
        ?>
    </select>
    
    <button class="view-button" type="submit">Generate Report</button>
</form>

<?php

if ($_SERVER['REQUEST_METHOD'] == 'GET' && (isset($_GET['user_id']) || isset($_GET['start_date']) || isset($_GET['end_date']) || isset($_GET['task_type']))) {

    $filters = [
        'user_id' => $_GET['user_id'] ?? '',
        'start_date' => $_GET['start_date'] ?? '',
        'end_date' => $_GET['end_date'] ?? '',
        'task_type' => $_GET['task_type'] ?? '',
    ];

    displayReport($pdo, $filters);
} else {
    echo "<h3>Please select filters to generate the report.</h3>";
}
?>
    </select>
</form>
</nav>
<div>
</div>
</div>
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
        <script src="./js/script.js"></script>
        </body>
        </html>
