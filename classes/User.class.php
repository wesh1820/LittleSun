<?php
require_once 'config.php';

class User {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getUsers($type = null) {
        $sql = "SELECT * FROM users";
        if ($type) {
            $sql .= " WHERE typeOfUser = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $type);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->conn->query($sql);
        }
        return $result;
    }

    public function getUserRole($email) {
        $sql = "SELECT typeOfUser FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['typeOfUser'];
        } else {
            return "";
        }
    }

    public function getFirstName($email) {
        $sql = "SELECT firstname FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['firstname'];
        } else {
            return "Unknown";
        }
    }

    public function getManagers() {
        $sql = "SELECT * FROM users WHERE typeOfUser = 'manager'";
        $result = $this->conn->query($sql);
        return $result;
    }
}
?>
