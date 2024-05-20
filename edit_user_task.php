<?php

require_once './classes/db.class.php';
require_once './classes/User.class.php';
require_once './classes/Session.class.php';

// Instantiate the database
$db = Database::getInstance();
$conn = $db->getConnection();
if (isset($_GET['userID'])) {
    $userID = intval($_GET['userID']);



    // Controleren op verbinding
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query om taken op te halen die aan de gebruiker zijn toegewezen
    $sql = "SELECT tasks.TaskID, tasks.TaskName FROM tasks
            JOIN user_tasks ON tasks.TaskID = user_tasks.TaskID
            WHERE user_tasks.UserID = $userID";
    $result = $conn->query($sql);

    // Gebruikerstaken weergeven
    echo "<h3>Tasks for User ID: $userID</h3>";
    if ($result->num_rows > 0) {
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>" . $row["TaskName"] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "No tasks assigned to this user.";
    }

    // Formulier om nieuwe taak toe te voegen
    echo "<h4>Assign New Task</h4>";
    echo "<form action='assign_task.php' method='post'>
            <input type='hidden' name='userID' value='$userID'>
            <label for='taskID'>Select Task:</label>
            <select name='taskID' id='taskID'>";
    
    // Alle beschikbare taken ophalen
    $sql = "SELECT TaskID, TaskName FROM tasks";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row["TaskID"] . "'>" . $row["TaskName"] . "</option>";
        }
    } else {
        echo "<option value=''>No tasks available</option>";
    }
    echo "</select><br><br>
            <label for='timeSlot'>Select Time Slot:</label>
            <input type='text' name='timeSlot' id='timeSlot'><br><br>
            <input type='submit' value='Assign Task'>
          </form>";

    // Sluit de databaseverbinding
    $conn->close();
}
?>
