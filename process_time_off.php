<?php

require_once 'config.php';
require_once './classes/Location.class.php';
require_once './classes/db.class.php';
require_once './classes/Session.class.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["user_id"]) && isset($_POST["reason"]) && isset($_POST["date_off"]) && isset($_POST["start_time_slot"]) && isset($_POST["end_time_slot"])) {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "littlesun";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $user_id = $_POST["user_id"];
        $reason = $_POST["reason"];
        $date_off = $_POST["date_off"];
        $start_time = $_POST["start_time_slot"];
        $end_time = $_POST["end_time_slot"];
        $status = 0; // 'Pending' status represented as 0

        $sql = "INSERT INTO time_off_requests (user_id, reason, date_off, start_time, end_time, status) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssi", $user_id, $reason, $date_off, $start_time, $end_time, $status);

        if ($stmt->execute() === TRUE) {
            echo "Verlofaanvraag succesvol ingediend.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Niet alle vereiste velden zijn ingevuld.";
    }
} else {
    echo "Het formulier is niet verzonden.";
}
?>
