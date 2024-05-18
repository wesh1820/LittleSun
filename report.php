<?php
require_once './classes/db.class.php';
require_once './classes/Session.class.php';
require_once './classes/User.class.php';
require './sidebar.php';

// Instantiate DB class
$conn = $db->getConnection();

$email = Session::getSession('email');

// Fetch user role to check if the user is admin
$user = new User($conn);
$user_role = $user->getUserRole($email);

// Check if the user is an admin
if ($user_role !== 'admin') {
    echo "Access denied. Only admin can view this page.";
    exit;
}

// Get the current month and year
$month = isset($_GET['month']) ? intval(date('n', strtotime($_GET['month']))) : date('n');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Calculate the first and last date of the month
$first_day_of_month = date('Y-m-01', strtotime("$year-$month-01"));
$last_day_of_month = date('Y-m-t', strtotime("$year-$month-01"));

// Fetch sick time slots and user information from the database for the selected month
$sql = "SELECT u.firstname, u.lastname, ts.StartSlot, ts.EndSlot, ts.Date
        FROM time_slots ts
        INNER JOIN users u ON ts.UserID = u.id
        WHERE ts.Sick = 1 AND ts.Date BETWEEN ? AND ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $first_day_of_month, $last_day_of_month);
$stmt->execute();
$result = $stmt->get_result();

$sick_users = array();
$total_sick_hours = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $start_time = new DateTime($row['StartSlot']);
        $end_time = new DateTime($row['EndSlot']);
        $interval = $start_time->diff($end_time);
        $hours = $interval->h + ($interval->i / 60); // Convert minutes to fractional hours
        
        // Add user to sick_users array or update their total sick hours
        $user_name = $row['firstname'] . ' ' . $row['lastname'];
        if (!isset($sick_users[$user_name])) {
            $sick_users[$user_name] = $hours;
        } else {
            $sick_users[$user_name] += $hours;
        }

        // Update total sick hours
        $total_sick_hours += $hours;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sick Hours Report</title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<div class="container">
    <h2>Sick Hours Report for <?php echo date('F Y', strtotime("$year-$month-01")); ?></h2>
    <form method="GET" action="">
        <label for="month">Select Month:</label>
        <input type="month" id="month" name="month" value="<?php echo isset($_GET['month']) ? htmlspecialchars($_GET['month']) : date('Y-m'); ?>">
        <label for="year">Select Year:</label>
        <input type="number" id="year" name="year" min="1970" max="2100" value="<?php echo isset($_GET['year']) ? htmlspecialchars($_GET['year']) : date('Y'); ?>">
        <button type="submit">View</button>
    </form>

    <h2>Sick Users and Total Sick Hours:</h2>
    <h3><strong>Total Sick Hours for All Users:</strong> <?php echo number_format($total_sick_hours, 2) . ' hours'; ?></h3>
    <ul>
        <?php foreach ($sick_users as $user_name => $total_hours): ?>
            <li><?php echo $user_name . ': ' . number_format($total_hours, 2) . ' hours'; ?></li>
        <?php endforeach; ?>
       
    </ul>
</div>
</body>
</html>
