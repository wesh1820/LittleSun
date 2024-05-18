<?php
// Database configuration
$servername = "localhost"; // Replace with your server name
$username = "root"; // Replace with your username
$password = ""; // Replace with your password
$dbname = "littlesun"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch tasks assigned to the user from the database
$userID = 1; // Example user ID, replace with actual user ID
$sqlUserTasks = "SELECT ut.TaskID, t.TaskName 
                 FROM user_task ut
                 INNER JOIN tasks t ON ut.TaskID = t.TaskID
                 WHERE ut.UserID = $userID";
$resultUserTasks = $conn->query($sqlUserTasks);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Tasks</title>
</head>
<body>

<h2>Assign Tasks to Users</h2>

<form action="" method="post">
    <label for="taskID">Select Task:</label>
    <select name="taskID" id="taskID">
        <?php
        // Dynamically generate options for tasks dropdown based on tasks assigned to the user
        if ($resultUserTasks->num_rows > 0) {
            while($row = $resultUserTasks->fetch_assoc()) {
                echo "<option value='" . $row['TaskID'] . "'>" . $row['TaskName'] . "</option>";
            }
        } else {
            echo "<option value='' disabled>No tasks assigned</option>";
        }
        ?>
    </select>
    <br><br>
    <label for="timeSlot">Select Time Slot:</label>
    <select name="timeSlot" id="timeSlot">
        <!-- You can populate this dropdown with time slot data from your database -->
        <option value="8:00">8:00 AM</option>
        <option value="9:00">9:00 AM</option>
        <!-- Add more options as needed -->
    </select>
    <br><br>
    <input type="submit" name="assign_task" value="Assign Task">
</form>

</body>
</html>

<?php
// Close connection
$conn->close();
?>
