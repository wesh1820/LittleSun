<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Form</title>
    <script>
        // Function to redirect to previous page after a delay
        function redirectToPreviousPage() {
            setTimeout(function() {
                history.back();
            }, 2000); // 2000 milliseconds = 2 seconds
        }
    </script>
</head>
<body>


    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Verbinding met de database maken (vervang de waarden door je eigen databasegegevens)


        // Maak verbinding met de database

        require_once './classes/db.class.php';
        require_once './classes/User.class.php';
        require_once './classes/Session.class.php';
        
        // Instantiate the database
        $db = Database::getInstance();
        $conn = $db->getConnection();

        // Controleren op verbinding
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Functie om te controleren of een gebruiker toegang heeft tot een taak
        function checkUserTask($conn, $userID, $taskID) {
            $sql = "SELECT * FROM UserTasks WHERE UserID = $userID AND TaskID = $taskID";
            $result = $conn->query($sql);
            return ($result->num_rows > 0);
        }

        // Functie om te controleren of een gebruiker time-off heeft op een specifieke datum en tijdstip
        function checkUserTaskOverlap($conn, $userID, $startSlot, $endSlot, $date) {
            $sql = "SELECT * FROM time_slots 
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
            $sql = "SELECT * FROM timeoff 
                    WHERE UserID = $userID 
                    AND (
                        (Start_date = '$date' AND End_date IS NULL)
                        OR ('$date' BETWEEN Start_date AND End_date)
                    )
                    AND (
                        (Start_time IS NULL AND End_time IS NULL) 
                        OR (Start_time <= '$endTime' AND (End_time >= '$startTime' OR End_time IS NULL))
                    )
                    AND Status = 1";
            $result = $conn->query($sql);
            return ($result->num_rows > 0);
        }

        // Functie om een taak toe te voegen aan de time_slots tabel en de Sick-veld bij te werken
        function addTaskToTimeSlot($conn, $userID, $taskID, $startSlot, $endSlot, $date) {
            $startTime = $startSlot . ":00"; // Add seconds to the start time slot
            $endTime = $endSlot . ":00"; // Add seconds to the end time slot
            $response = "";

            // Check if the user is already assigned a task during this time slot
            if (checkUserTaskOverlap($conn, $userID, $startSlot, $endSlot, $date)) {
                $response = "User is already assigned a task during this time slot. Task cannot be assigned.";
            } elseif (checkUserTimeOff($conn, $userID, $date, $startTime, $endTime)) {
                $response = "User has time-off at this time. Task cannot be assigned.";
            } elseif (!checkUserTask($conn, $userID, $taskID)) {
                $response = "User does not have access to this task.";
            } else {
                $sql = "INSERT INTO time_slots (UserID, TaskID, StartSlot, EndSlot, Date, Sick) VALUES ('$userID', '$taskID', '$startSlot', '$endSlot', '$date', 0)";
                if ($conn->query($sql) === TRUE) {
                    $response = "Task successfully assigned to user.";
                } else {
                    $response = "Error: " . $sql . "<br>" . $conn->error;
                }
            }
            echo "<div id='response'>$response</div>";
            // Redirect to previous page after a delay
            echo "<script>redirectToPreviousPage();</script>";
        }

        // Extract form data
        $userID = $_POST["userID"];
        $taskID = $_POST["taskID"];
        $startSlot = $_POST["startSlot"];
        $endSlot = $_POST["endSlot"];
        $date = $_POST["date"];

        // Add the task to the time slot and get the message
        addTaskToTimeSlot($conn, $userID, $taskID, $startSlot, $endSlot, $date);

        // Sluit de databaseverbinding
        $conn->close();
    }
    ?>
</body>
</html>
