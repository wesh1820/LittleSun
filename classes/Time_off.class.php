<?php
require_once 'config.php';

class Timeoff {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    
        // Method to retrieve all time-off requests except those with status 1, including user first and last names
        public function getTimeOffRequests($manager_location_id) {
            $sql = "SELECT timeoff.*, users.firstname, users.lastname 
                    FROM timeoff 
                    JOIN users ON timeoff.UserID = users.id 
                    JOIN user_location ON users.id = user_location.user_id
                    WHERE user_location.location_id = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                // Log or handle the SQL error
                die("SQL error: " . $this->conn->error);
            }
            $stmt->bind_param("i", $manager_location_id);
            if (!$stmt->execute()) {
                // Log or handle the SQL error
                die("Error executing query: " . $stmt->error);
            }
            $result = $stmt->get_result();
            if (!$result) {
                // Log or handle the SQL error
                die("Error getting result set: " . $this->conn->error);
            }
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        
        
        
        
        
        
    
        // Method to update the status of a time-off request
        public function updateStatus($request_id, $status) {
            $sql = "UPDATE timeoff SET Status = ? WHERE ID = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $status, $request_id);
            return $stmt->execute();
        }
    

// Method to accept a time off request
public function acceptTimeOffRequest($request_id) {
    $sql = "UPDATE timeoff SET Status = 'Accepted' WHERE ID = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $request_id);
    if ($stmt->execute()) {
        return true; // Successfully accepted
    } else {
        return false; // Error accepting
    }
}

// Method to deny a time off request
public function denyTimeOffRequest($request_id) {
    $sql = "UPDATE timeoff SET Status = 'Denied' WHERE ID = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $request_id);
    if ($stmt->execute()) {
        return true; // Successfully denied
    } else {
        return false; // Error denying
    }
}

}
?>
