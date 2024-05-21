<?php
require_once 'config.php';
require_once './classes/Location.class.php';
require_once './classes/db.class.php';
require_once './classes/Session.class.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    if (isset($_POST["user_id"], $_POST["reason"], $_POST["time_off_type"])) {
        $user_id = intval($_POST["user_id"]); 
        if ($user_id <= 0) {
            echo "Invalid user ID: " . $user_id;
            exit; 
        }
        
        if ($_POST["time_off_type"] === "single_day" && isset($_POST["single_day_date"], $_POST["single_day_start_time"], $_POST["single_day_end_time"])) {
    
            echo "Single day selected";
            $reason = $_POST["reason"];
            $start_date = $_POST["single_day_date"];
            $start_time = $_POST["single_day_start_time"];
            $end_time = $_POST["single_day_end_time"];
            $status = 0; 

            echo "UserID: $user_id<br>";
            echo "Reason: $reason<br>";
            echo "Start Date: $start_date<br>";
            echo "Start Time: $start_time<br>";
            echo "End Time: $end_time<br>";

            try {
                $sql = "INSERT INTO timeoff (UserID, Timeoff_reason, Start_date, Start_time, End_time, Status) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("issssi", $user_id, $reason, $start_date, $start_time, $end_time, $status);
                if ($stmt->execute()) {
                    echo "Single day time off request submitted successfully.";
                } else {
                    echo "Error executing query: " . $stmt->error;
                }
                $stmt->close();
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
        } elseif ($_POST["time_off_type"] === "multiple_days" && isset($_POST["start_date"], $_POST["end_date"])) {
            
            echo "Multiple days selected"; 
            $reason = $_POST["reason"];
            $start_date = $_POST["start_date"];
            $end_date = $_POST["end_date"];
            $status = 0;

        } else {
            echo "Not all required fields are filled for the selected time off type.";
        }
    } else {
        echo "Not all required fields are filled.";
    }
} else {
    echo "The form was not submitted.";
}
?>
