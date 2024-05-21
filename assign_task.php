<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Form</title>
    <script>
        function redirectToPreviousPage() {
            setTimeout(function() {
                history.back();
            }, 2000); 
        }
    </script>
</head>
<body>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "Littlesun";
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        function checkUserTask($conn, $userID, $taskID) {
            $sql = "SELECT * FROM UserTasks WHERE UserID = $userID AND TaskID = $taskID";
            $result = $conn->query($sql);
            return ($result->num_rows > 0);
        }
        function checkUserTaskOverlap($conn, $userID, $startSlot, $endSlot, $date) {
            $sql = "SELECT * FROM Time_slots 
                    WHERE UserID = $userID 
                    AND Date = '$date' 
                    AND (
                        (StartSlot <= '$startSlot' AND EndSlot > '$startSlot') 
                        OR (StartSlot < '$endSlot' AND EndSlot >= '$endSlot')
                        OR (StartSlot >= '$startSlot' AND EndSlot <= '$endSlot')
                    )";
            $result = $conn->query($sql);
            return ($result->num_rows > 0);
        }

        function checkUserTimeOff($conn, $userID, $date, $startTime, $endTime) {
            $sql = "SELECT * FROM Timeoff 
                    WHERE UserID = $userID 
                    AND (
                        (Start_date = '$date' AND End_date IS NULL)
                        OR ('$date' BETWEEN Start_date AND End_date)
                    )
                    AND (
                        (Start_Time IS NULL AND End_Time IS NULL) 
                        OR (Start_Time <= '$endTime' AND (End_Time >= '$startTime' OR End_Time IS NULL))
                    )
                    AND Status = 1";
            $result = $conn->query($sql);
            return ($result->num_rows > 0);
        }
        function addTaskToTimeSlot($conn, $userID, $taskID, $startSlot, $endSlot, $date) {
            $startTime = $startSlot . ":00"; 
            $endTime = $endSlot . ":00";
            $response = "";

            if (checkUserTaskOverlap($conn, $userID, $startSlot, $endSlot, $date)) {
                $response = "User is already assigned a task during this Time slot. Task cannot be assigned.";
            } elseif (checkUserTimeOff($conn, $userID, $date, $startTime, $endTime)) {
                $response = "User has Time-off at this Time. Task cannot be assigned.";
            } elseif (!checkUserTask($conn, $userID, $taskID)) {
                $response = "User does not have access to this task.";
            } else {
                $sql = "INSERT INTO Time_slots (UserID, TaskID, StartSlot, EndSlot, Date, Sick) VALUES ('$userID', '$taskID', '$startSlot', '$endSlot', '$date', 0)";
                if ($conn->query($sql) === TRUE) {
                    $response = "Task successfully assigned to user.";
                } else {
                    $response = "Error: " . $sql . "<br>" . $conn->error;
                }
            }
            echo "<div id='response'>$response</div>";
            echo "<script>redirectToPreviousPage();</script>";
        }

        $userID = $_POST["userID"];
        $taskID = $_POST["taskID"];
        $startSlot = $_POST["startSlot"];
        $endSlot = $_POST["endSlot"];
        $date = $_POST["date"];
        addTaskToTimeSlot($conn, $userID, $taskID, $startSlot, $endSlot, $date);
        $conn->close();
    }
    ?>
</body>
</html>
