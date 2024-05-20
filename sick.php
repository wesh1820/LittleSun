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
$user_role = $user->getUserRole($email);

// Check if the user is an admin
if ($user_role !== 'manager') {
    echo "Access denied. Only manager can view this page.";
    exit;
}

// Get the selected day from the request or use the current day
$selected_day = isset($_GET['day']) ? $_GET['day'] : date('Y-m-d');

// Ensure $selected_day is a valid date
if (!strtotime($selected_day)) {
    $selected_day = date('Y-m-d');
}

// Fetch sick users from the database for the selected day, including task IDs
$sql = "SELECT users.firstname, users.lastname, time_slots.StartSlot, time_slots.EndSlot, time_slots.TaskID
        FROM time_slots
        INNER JOIN users ON time_slots.UserID = users.id
        WHERE time_slots.Sick = 1 AND time_slots.Date = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $selected_day);
$stmt->execute();
$result = $stmt->get_result();

$sick_users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sick_users[] = $row;
    }
}

// Instantiate Task class
$task = new Task($conn);
// Get all tasks
$tasks = $task->getAllTasks();
// Map task IDs to task names
$task_map = array_column($tasks, 'TaskName', 'TaskID');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sick Users</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<style>
/* Styles for the "Report" button */
.report-button {
    position: fixed;
    top: 20px;
    right: 20px;
}

.report-button button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

.report-button button:hover {
    background-color: #0056b3;
}
</style>
<body>
<div class="sidebar">
    <?php include 'sidebar.php'; ?>
</div>
<div class="container">
    <h2>Sick Users on <?php echo date('F j, Y', strtotime($selected_day)); ?></h2>
    <form method="GET" action="">
        <label for="day">Select Date:</label>
        <input type="date" id="day" name="day" value="<?php echo $selected_day; ?>">
        <button class="view-button" type="submit">View</button>
    </form>
    <div id="myModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <div id="popup-content"></div>
  </div>
</div>

    <?php if (!empty($sick_users)): ?>
        <table>
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Task</th> <!-- Changed from Task ID to Task -->
                    <th>Start Time</th>
                    <th>End Time</
                    <th>End Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sick_users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['firstname']); ?></td>
                        <td><?php echo htmlspecialchars($user['lastname']); ?></td>
                        <td><?php echo htmlspecialchars($task_map[$user['TaskID']]); ?></td> <!-- Display task name -->
                        <td><?php echo htmlspecialchars($user['StartSlot']); ?></td>
                        <td><?php echo htmlspecialchars($user['EndSlot']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No users were sick on this day.</p>
    <?php endif; ?>
</div>

</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function() {

    $(".add-button").click(function() {

        $("#popup-content").load("add_hub_location.php");

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
</body>
</html>
