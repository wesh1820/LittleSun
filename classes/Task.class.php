<?php
require_once 'config.php';

class Task {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addTask($taskName) {
        $sql = "INSERT INTO tasks (TaskName) VALUES (?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $taskName);
        return $stmt->execute();
    }

    public function deleteTask($taskId) {
        $sql = "DELETE FROM tasks WHERE TaskID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $taskId);
        return $stmt->execute();
    }

    public function getAllTasks() {
        $sql = "SELECT * FROM tasks";
        $result = $this->conn->query($sql);
        return ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getUsersWithTasks() {
        $sql = "SELECT users.id AS user_id, users.firstname, users.lastname, users.email, tasks.taskname 
                FROM users 
                LEFT JOIN UserTasks ON users.id = UserTasks.userid
                LEFT JOIN tasks ON UserTasks.taskid = tasks.taskid 
                WHERE users.typeOfUser = 'user'";
        return $this->conn->query($sql);
    }

    public function getUserDaysOff() {
        $daysOff = array();

        $sql = "SELECT user_id, date_off FROM user_days_off";
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $daysOff[$row['user_id']][] = $row['date_off'];
            }
        }

        return $daysOff;
    }

    public function getUserTasks($user_id) {
        $sql = "SELECT UserTasks.TaskID, Tasks.TaskName 
                FROM UserTasks 
                INNER JOIN Tasks ON UserTasks.TaskID = Tasks.TaskID 
                WHERE UserTasks.UserID = $user_id"; 
        $result = $this->conn->query($sql);

        if (!$result) {
            echo "Error: " . $this->conn->error;
        } else {
            if ($result->num_rows > 0) {
                echo "<h2>Your Tasks:</h2>";
                echo "<table border='1'>";
                echo "<tr><th>Task Name</th></tr>";
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["TaskName"] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "No tasks found for this user.";
            }
        }
    }

    public function getUserTasks_v2($user_id) {
        $sql = "SELECT UserTasks.TaskID, Tasks.TaskName 
                FROM UserTasks 
                INNER JOIN Tasks ON UserTasks.TaskID = Tasks.TaskID 
                WHERE UserTasks.UserID = ?"; 
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result) {
            echo "Error: " . $this->conn->error;
        } else {
            if ($result->num_rows > 0) {
                echo "<h2>Your Tasks:</h2>";
                echo "<table border='1'>";
                echo "<tr><th>Task Name</th></tr>";
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["TaskName"] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "No tasks found for this user.";
            }
        }
        $stmt->close();
    }
}
?>
