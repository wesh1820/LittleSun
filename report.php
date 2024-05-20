<?php
require_once './classes/db.class.php';
require_once './classes/User.class.php';
require_once './classes/Session.class.php';
require './sidebar.php';

// Instantiate the database
$db = Database::getInstance();
$conn = $db->getConnection();

// Fetch user email from session
$email = Session::getSession('email');
$user = new User($conn);

// Vervolg van je code...


// Get the ID van de ingelogde gebruiker
$loggedInUserId = $_SESSION['user_id']; // Pas dit aan op basis van hoe je de gebruiker identificeert

// Haal de locatie ID van de ingelogde gebruiker op
$loggedInUserLocationId = $user->getLocationId($loggedInUserId);

// Haal alleen de gebruikers op van de locatie van de ingelogde gebruiker
$users = $user->getUsersByLocation($loggedInUserLocationId);

// Database verbinding instellen
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

// Function to get Sick hours
function getSickHours($pdo, $date, $userId) {
    $query = "SELECT SUM(TIMESTAMPDIFF(HOUR, Start_time, End_time)) AS total_sick_hours
              FROM timeoff
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

// Function to get Time Off hours
function getTimeOffHours($pdo, $date, $userId) {
    $query = "SELECT SUM(TIMESTAMPDIFF(HOUR, StartSlot, EndSlot)) AS total_time_off_hours
              FROM time_slots
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
    return $result['total_time_off_hours'] ?? 0;
}

// Functie om rapporten weer te geven met filters// Functie om rapporten weer te geven met filters
function displayReport($pdo, $filters) {
    $query = "SELECT u.firstname, u.lastname, ts.TaskID, t.TaskName, ts.Date, ts.StartSlot, ts.EndSlot, ts.UserID
              FROM users u
              JOIN Time_slots ts ON u.id = ts.UserID
              JOIN Tasks t ON ts.TaskID = t.TaskID
              WHERE 1=1";
    
    // Add dynamic filters
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
    
    // Initialize total sick and time off hours variables
    $total_sick_hours = 0;
    $total_time_off_hours = 0;
    
    if (count($results) > 0) {
        echo "<table border='1'>";
        echo "<tr><th>User</th><th>Task</th><th>Date</th><th>Start Time</th><th>End Time</th><th>Sick Hours</th><th>Time off Hours</th><th>Overtime (hours)</th></tr>";
        foreach ($results as $row) {
            // Calculate sick hours for the current user and task
            $sickHours = getSickHours($pdo, $row['Date'], $row['UserID']);
            $total_sick_hours += $sickHours; // Add to total sick hours
            
            // Calculate time off hours for the current user and task
            $timeOffHours = getTimeOffHours($pdo, $row['Date'], $row['UserID']);
            $total_time_off_hours += $timeOffHours; // Add to total time off hours
            
            // Display the row with data
            echo "<tr>
                    <td>{$row['firstname']} {$row['lastname']}</td>
                    <td>{$row['TaskName']}</td>
                    <td>{$row['Date']}</td>
                    <td>{$row['StartSlot']}</td>
                    <td>{$row['EndSlot']}</td>
                    <td>{$timeOffHours}</td>
                    <td>{$sickHours}</td>
                    <td>Calculated overtime hours</td>
                  </tr>";
        }
        // Display the total sick and time off hours at the bottom of the table
        echo "<tr><td colspan='5'><strong>Total time Off:</strong></td><td><strong>{$total_sick_hours}</strong></td></tr>";
        echo "<tr><td colspan='5'><strong>Total Sick Hours Hours:</strong></td><td><strong>{$total_time_off_hours}</strong></td></tr>";
        echo "</table>";
    } else {
        echo "<p>No records found for the given criteria.</p>";
    }
}


// Haal de gebruikers en taaktypen op voor dropdowns
function getUsers($pdo) {
    $stmt = $pdo->prepare("SELECT id, firstname, lastname FROM users WHERE status = 1");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTaskTypes($pdo) {
// Function to get Task Types
$stmt = $pdo->prepare("SELECT TaskID, TaskName FROM Tasks");
$stmt->execute();
return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Functie om overuren te berekenen
function calculateOvertime($pdo, $userId, $month) {
$query = "SELECT SUM(TIMESTAMPDIFF(HOUR, ts.StartSlot, ts.EndSlot)) AS total_hours
          FROM Time_slots ts
          WHERE ts.UserID = :user_id
          AND MONTH(ts.Date) = :month
          AND ts.EndSlot > '17:00:00'"; // 17:00:00 is het einde van de werkdag

$stmt = $pdo->prepare($query);
$stmt->execute([
    ':user_id' => $userId,
    ':month' => $month,
]);

$result = $stmt->fetch(PDO::FETCH_ASSOC);
$total_hours = $result['total_hours'] ?? 0;

// Standaard uren per maand, aan te passen aan de daadwerkelijke standaard uren
$standard_hours_per_month = 160;

// Bereken overuren
$overtime_hours = $total_hours - $standard_hours_per_month;

return $overtime_hours;
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
<h2> Let's start generating a report.</h2>
<form method="GET" style="display: flex; align-items: center;">
    <label for="user_id">User:</label>
    <select name="user_id" id="user_id">
        <option value="">All</option>
        <?php
        // Retrieving users from the database
        $users = getUsers($pdo);
        // Displaying user options in the dropdown
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
        // Retrieving task types from the database
        $taskTypes = getTaskTypes($pdo);
        // Displaying task type options in the dropdown
        foreach ($taskTypes as $type) {
            echo "<option value=\"{$type['TaskID']}\">{$type['TaskName']}</option>";
        }
        ?>
    </select>
    
    <button class="view-button" type="submit">Generate Report</button>
</form>

<?php
// Checking if the form is submitted and filters are set
if ($_SERVER['REQUEST_METHOD'] == 'GET' && (isset($_GET['user_id']) || isset($_GET['start_date']) || isset($_GET['end_date']) || isset($_GET['task_type']))) {
    // Creating an array with filters based on user input
    $filters = [
        'user_id' => $_GET['user_id'] ?? '',
        'start_date' => $_GET['start_date'] ?? '',
        'end_date' => $_GET['end_date'] ?? '',
        'task_type' => $_GET['task_type'] ?? '',
    ];
    // Calling the function to display the report with applied filters
    displayReport($pdo, $filters);
} else {
    // Displaying a message if no filters are set
    echo "<h3>Please select filters to generate the report.</h3>";
}
?>
    </select>
</form>

</nav>
<div>

        </div>
        </div>
        </body>
        </html>
