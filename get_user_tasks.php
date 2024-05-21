<?php
if (isset($_GET['userID'])) {
    $userID = intval($_GET['userID']);

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "Littlesun";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT Tasks.TaskID, Tasks.TaskName FROM Tasks
            JOIN UserTasks ON Tasks.TaskID = UserTasks.TaskID
            WHERE UserTasks.UserID = $userID";
    
    $result = $conn->query($sql);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    if ($result->num_rows > 0) {

        echo "<h4>Assign New Task</h4>";
        echo "<form action='assign_task.php' method='post'>
                <input type='hidden' name='userID' value='$userID'>
                <label for='taskID'>Select Task:</label>
                <select name='taskID' id='taskID'>";

        $sql = "SELECT Tasks.TaskID, Tasks.TaskName FROM Tasks
                JOIN UserTasks ON Tasks.TaskID = UserTasks.TaskID
                WHERE UserTasks.UserID = $userID";
        
        $result_assigned_Tasks = $conn->query($sql);
        
        if (!$result_assigned_Tasks) {
            die("Query failed: " . $conn->error);
        }

        if ($result_assigned_Tasks->num_rows > 0) {
            while ($row = $result_assigned_Tasks->fetch_assoc()) {
                echo "<option value='" . $row["TaskID"] . "'>" . $row["TaskName"] . "</option>";
            }
        } else {
            echo "<option value='' disabled>No Tasks available</option>";
        }
        echo "</select><br><br>
                <label for='startSlot'>Select Start Time Slot:</label>
                <select name='startSlot' id='startSlot'>
                    <option value='08:00'>08:00</option>
                    <option value='09:00'>09:00</option>
                    <option value='10:00'>10:00</option>
                    <option value='11:00'>11:00</option>
                    <option value='12:00'>12:00</option>
                    <option value='13:00'>13:00</option>
                    <option value='14:00'>14:00</option>
                    <option value='15:00'>15:00</option>
                    <option value='16:00'>16:00</option>
                    <option value='17:00'>17:00</option>
                    <option value='18:00'>18:00</option>
                    <option value='19:00'>19:00</option>
                </select><br><br>
                <label for='endSlot'>Select End Time Slot:</label>
                <select name='endSlot' id='endSlot'>
                    <option value='09:00'>09:00</option>
                    <option value='10:00'>10:00</option>
                    <option value='11:00'>11:00</option>
                    <option value='12:00'>12:00</option>
                    <option value='13:00'>13:00</option>
                    <option value='14:00'>14:00</option>
                    <option value='15:00'>15:00</option>
                    <option value='16:00'>16:00</option>
                    <option value='17:00'>17:00</option>
                    <option value='18:00'>18:00</option>
                    <option value='19:00'>19:00</option>
                </select><br><br>
                <label for='date'>Select Date:</label>
                <input type='date' name='date'><br><br>
                <input type='submit' value='Assign Task'>
              </form>";
    } else {
        echo "No Tasks assigned to this user.";
    }

    $conn->close();
}
?>
