<?php
class Database {
    private $host;
    private $username;
    private $password;
    private $dbname;
    protected $conn;

    public function __construct($host, $username, $password, $dbname) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
        $this->connect(); // Automatically connect upon instantiation
    }

    private function connect() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);
        if ($this->conn->connect_error) {
            throw new Exception("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getConnection() {
        if (!$this->conn) {
            $this->connect();
        }
        return $this->conn;
    }

    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
            $this->conn = null;
        }
    }

    public function query($sql) {
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new Exception("Query failed: " . $this->conn->error);
        }
        return $result;
    }

    public function fetchAssoc($result) {
        $row = $result->fetch_assoc();
        return $row;
    }

    public function numRows($result) {
        return $result->num_rows;
    }
}

// Usage:
$db = new Database('localhost', 'root', '', 'Littlesun');
?>
