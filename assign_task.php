<?php
// Verbinding met de database maken (vervang de waarden door je eigen databasegegevens)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "littlesun";

// Maak verbinding met de database
$conn = new mysqli($servername, $username, $password, $dbname);

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
function checkUserTimeOff($conn, $userID, $date, $startTime, $endTime) {
    $sql = "SELECT * FROM timeoff 
            WHERE UserID = $userID 
            AND Start_date = '$date'
            AND (
                (Start_time <= '$endTime' AND End_time >= '$startTime')
                OR (Start_time <= '$startTime' AND End_time >= '$startTime')
                OR (Start_time >= '$startTime' AND End_time <= '$endTime')
            )
            AND Status = 1"; // Include status condition
    echo "SQL Query: " . $sql . "<br>"; // Debugging statement
    $result = $conn->query($sql);
    echo "Number of Rows: " . $result->num_rows . "<br>"; // Debugging statement
    return ($result->num_rows > 0);
}

// Functie om een taak toe te voegen aan de time_slots tabel en de Sick-veld bij te werken
function addTaskToTimeSlot($conn, $userID, $taskID, $startSlot, $endSlot, $date) {
    $startTime = $startSlot . ":00"; // Voeg seconden toe aan het starttijdslot
    $endTime = $endSlot . ":00"; // Voeg seconden toe aan het eindtijdslot

    // Controleer of de gebruiker time-off heeft op dit tijdstip
    if (checkUserTimeOff($conn, $userID, $date, $startTime, $endTime)) {
        echo "User has time-off at this time. Task cannot be assigned."; // Debugging statement
        return; // Stop de functie als er time-off is
    }

    // Voeg de taak toe aan het tijdvak
    if (checkUserTask($conn, $userID, $taskID)) {
        $sql = "INSERT INTO time_slots (UserID, TaskID, StartSlot, EndSlot, Date, Sick) VALUES ('$userID', '$taskID', '$startSlot', '$endSlot', '$date', 0)";
        if ($conn->query($sql) === TRUE) {
            echo "Task successfully assigned to user.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "User does not have access to this task.";
    }
}

// Verwerk het formulier
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userID = $_POST["userID"];
    $taskID = $_POST["taskID"];
    $startSlot = $_POST["startSlot"];
    $endSlot = $_POST["endSlot"];
    $date = $_POST["date"]; // Haal de geselecteerde datum op uit het formulier

    // Voeg de taak toe aan het tijdvak
    addTaskToTimeSlot($conn, $userID, $taskID, $startSlot, $endSlot, $date);
}

// Sluit de databaseverbinding
$conn->close();
?>
