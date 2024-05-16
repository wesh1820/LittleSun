<?php
require_once './classes/Clock.class.php'; // Include the ClockManager class
require_once './classes/Task.class.php'; 
require_once './classes/db.class.php'; // Include the Database class
require_once './classes/SessionManager.class.php';
require_once './classes/User.class.php';
include 'sidebar.php';

$email = SessionManager::getSession('email');
$user = new User($db->getConnection());
$user_role = $user->getUserRole($email);
SessionManager::setSession('firstname', $user->getFirstName($email));

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$clockManager = new Clock($db->getConnection());

if(isset($_POST['clock_in'])) {
    $message = $clockManager->clockIn($user_id);
}

if(isset($_POST['clock_out'])) {
    $message = $clockManager->clockOut($user_id);
}

$result = $clockManager->getUserRecords($user_id);

// Close the database connection
$db->closeConnection();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Clock In/Out System</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Clock In/Out System</h2>
        
        <?php if(isset($message)) echo "<p>$message</p>"; ?>
        
        <form method="post" action="">
            <input type="submit" name="clock_in" value="Clock In">
            <input type="submit" name="clock_out" value="Clock Out" style="background-color: red;">
        </form>

        <h3>Your Clock-In/Out Records</h3>
        <table>
            <tr>
                <th>Clock In Time</th>
                <th>Clock Out Time</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr><td>" . $row["clock_in_time"] . "</td><td>" . ($row["clock_out_time"] ? $row["clock_out_time"] : "Not clocked out yet") . "</td></tr>";
                }
            } else {
                echo "<tr><td colspan='2'>No records found</td></tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>
