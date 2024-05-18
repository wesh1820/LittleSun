<?php

require_once './classes/Task.class.php'; 
require_once './classes/db.class.php';
require_once './classes/Session.class.php';
class Clock {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }
    

    public function clockIn($userId) {
        $clockInTime = date("Y-m-d H:i:s");
        $sql = "INSERT INTO clock_records (user_id, clock_in_time) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $userId, $clockInTime);
        $success = $stmt->execute();
        $stmt->close();
        return $success ? "You have successfully clocked in at: $clockInTime" : $this->conn->error;
    }

    public function clockOut($userId) {
        $clockOutTime = date("Y-m-d H:i:s");
        $sql = "UPDATE clock_records SET clock_out_time = ? WHERE user_id = ? AND clock_out_time IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $clockOutTime, $userId);
        $success = $stmt->execute();
        $stmt->close();
        return $success ? "You have successfully clocked out at: $clockOutTime" : $this->conn->error;
    }

    public function getUserRecords($userId) {
        $sql = "SELECT clock_in_time, clock_out_time FROM clock_records WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function isClockedIn($userId) {
        $sql = "SELECT COUNT(*) as count FROM clock_records WHERE user_id = ? AND clock_out_time IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result['count'] > 0;
    }
    
}
?>
