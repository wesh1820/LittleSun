<?php
class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        $config = [
            'host' => 'localhost',
            'username' => 'root',
            'password' => '',
            'dbname' => 'littlesun'
        ];
        $this->connect($config);
    }

    private function connect($config) {
        $this->conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
            $this->conn = null;
            self::$instance = null;
        }
    }

    public function query($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        if ($params) {
            $types = str_repeat('s', count($params)); // Assuming all parameters are strings, adjust accordingly
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result();
    }

    public function fetch($sql, $params = []) {
        $result = $this->query($sql, $params);
        return $result->fetch_assoc();
    }
}
?>
