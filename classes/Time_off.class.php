<?php
require_once 'config.php';

class Timeoff {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    
        // Method to retrieve all time-off requests except those with status 1, including user first and last names
        public function getTimeOffRequests() {
            $sql = "SELECT timeoff.*, users.firstname, users.lastname 
                    FROM timeoff 
                    JOIN users ON timeoff.UserID = users.id 
                    WHERE timeoff.Status != 1";
            $result = $this->conn->query($sql);
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
