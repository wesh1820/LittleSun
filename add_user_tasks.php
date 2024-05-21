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
<<<<<<< HEAD
=======

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User to Task</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
       
       select,
       input {
        padding: 10px;
        width: 100%;
        border-radius: 8px;
        margin-bottom: 16px;
       }

       input {
        width: 97%;
       }
    </style>



</head>
<body>
    <div class="container">
        <h2>Add User to Task</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="selected_user">Select User</label>
            <select id="selected_user" name="selected_user" required>
                <?php echo $user_options_html; ?>
            </select><br>

            <label for="selected_task">Select Task</label>
            <select id="selected_task" name="selected_task" required>
                <?php echo $task_options_html; ?>
            </select><br>

            <label for="date">Date</label>
            <input type="date" id="date" name="date" required><br>

            <label for="start_time">Start Time</label>
            <input type="time" id="start_time" name="start_time" required><br>

            <label for="end_time">End Time</label>
            <input type="time" id="end_time" name="end_time" required><br>

            <input type="submit" value="Assign Task">
        </form>
    </div>
</body>
</html>
>>>>>>> 9fd0143fa0023f17c58364575694595a0274282d
