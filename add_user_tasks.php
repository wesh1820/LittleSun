<?php

class Database {
    private $host;
    private $username;
    private $password;
    private $dbname;
    private $conn;
    private static $instance = null;

    private function __construct($config) {
        $this->host = $config['host'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->dbname = $config['dbname'];
        $this->connect(); // Automatically connect upon instantiation
    }

    public static function getInstance($config = null) {
        if (self::$instance === null) {
            if ($config === null) {
                // Standaardwaarden als geen configuratie wordt doorgegeven
                $config = [
                    'host' => 'localhost',
                    'username' => 'root',
                    'password' => '',
                    'dbname' => 'Littlesun'
                ];
            }
            self::$instance = new Database($config);
        }
        return self::$instance;
    }

    private function connect() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
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
}

// Usage:
$db = Database::getInstance();
$conn = $db->getConnection();

// Rest van de code in user.php

?>
