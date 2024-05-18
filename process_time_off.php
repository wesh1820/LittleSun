<?php
require_once 'config.php'; // Include database configuration
require_once './classes/user.class.php';
require_once './classes/db.class.php';
require_once './classes/Session.class.php';
// Validate input and process the form
// Validate input and process the form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required fields are filled
    if (isset($_POST["user_id"], $_POST["reason"], $_POST["time_off_type"])) {
        // Retrieve and sanitize user ID from form data
        $user_id = intval($_POST["user_id"]); // Convert to integer
        echo "User ID: " . $user_id; // Debugging statement

        if ($user_id <= 0) {
            echo "Invalid user ID: " . $user_id;
            exit; // Exit script
        }
        
        // Additional checks based on time off type
        // Remaining code...

        
        // Additional checks based on time off type
        if ($_POST["time_off_type"] === "single_day" && isset($_POST["start_date"], $_POST["start_time_slot"], $_POST["end_time_slot"])) {
            // Single day off form fields
            $reason = $_POST["reason"];
            $start_date = $_POST["start_date"];
            $start_time = $_POST["start_time_slot"];
            $end_time = $_POST["end_time_slot"];
            $status = 0; // 'Pending' status

            // Insert single day off data into the database
            $sql = "INSERT INTO timeoff (UserID, Timeoff_reason, Start_date, Start_time, End_time, Status) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issssi", $user_id, $reason, $start_date, $start_time, $end_time, $status);
            if ($stmt->execute()) {
                echo "Single day time off request submitted successfully.";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } elseif ($_POST["time_off_type"] === "multiple_days" && isset($_POST["start_date"], $_POST["end_date"])) {
            // Multiple days off form fields
            $reason = $_POST["reason"];
            $start_date = $_POST["start_date"];
            $end_date = $_POST["end_date"];
            $status = 0; // 'Pending' status

            // Insert multiple days off data into the database
            $sql = "INSERT INTO timeoff (UserID, Timeoff_reason, Start_date, End_date, Status) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssi", $user_id, $reason, $start_date, $end_date, $status);
            if ($stmt->execute()) {
                echo "Multiple days time off request submitted successfully.";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
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
