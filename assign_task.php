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

// Functie om een taak toe te voegen aan de time_slots tabel
function addTaskToTimeSlot($conn, $userID, $taskID, $startSlot, $endSlot, $date) {
    if (checkUserTask($conn, $userID, $taskID)) {
        $sql = "INSERT INTO time_slots (UserID, TaskID, StartSlot, EndSlot, Date) VALUES ('$userID', '$taskID', '$startSlot', '$endSlot', '$date')";
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
