<?php
require_once 'config.php';

class Timeoff {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }
    public function requestTimeOff($user_id, $date_off) {
        $sql = "INSERT INTO time_off_requests (user_id, date_off) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $user_id, $date_off);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
    public function getTimeOffRequests() {
        $sql = "SELECT tor.*, u.firstname, u.lastname 
                FROM time_off_requests tor
                INNER JOIN users u ON tor.user_id = u.id";
        $result = $this->conn->query($sql);
    
        if ($result) {
            // Check if there are any time off requests
            if ($result->num_rows > 0) {
                $time_off_requests = array();
                // Fetch all time off requests
                while ($row = $result->fetch_assoc()) {
                    $time_off_requests[] = $row;
                }
                return $time_off_requests;
            } else {
                return array(); // Return an empty array if no time off requests found
            }
        } else {
            // Handle query execution error
            echo "Error retrieving time off requests: " . $this->conn->error;
            return false;
        }
    }
    
    
    public function acceptTimeOffRequest($request_id) {
        $sql = "UPDATE time_off_requests SET status = 1 WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $request_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
    
    public function denyTimeOffRequest($request_id) {
        $sql = "UPDATE time_off_requests SET status = 0 WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $request_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
    
    
}
?>
