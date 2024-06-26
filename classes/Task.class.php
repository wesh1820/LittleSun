<?php
require_once 'config.php';

class Task {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getTasks() {
        $sql_Tasks = "SELECT TaskID, TaskName FROM Tasks";
        $result_Tasks = $this->conn->query($sql_Tasks);
        $Tasks = array();
        if ($result_Tasks->num_rows > 0) {
            while ($row_task = $result_Tasks->fetch_assoc()) {
                $Tasks[] = new Task($row_task['TaskID'], $row_task['TaskName']);
            }
        }
        return $Tasks;
    }

    public function getUserTasks() {
        $sql = "SELECT ut.UserID, CONCAT(u.firstname, ' ', u.lastname) AS UserName, ut.TaskID, t.TaskName, ut.Date, ut.StartTime, ut.EndTime FROM UserTasks ut JOIN users u ON ut.UserID = u.id JOIN Tasks t ON ut.TaskID = t.TaskID";
        $result = $this->conn->query($sql);
        $userTasks = array();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $userTasks[] = new Task($row['UserID'], $row['TaskID'], $row['Date'], $row['StartTime'], $row['EndTime']);
            }
        }
        return $userTasks;
    }

    public function assignTask($userId, $taskId, $date, $startTime, $endTime) {
        $sql_insert_assignment = "INSERT INTO UserTasks (UserID, TaskID, Date, StartTime, EndTime) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert_assignment = $this->conn->prepare($sql_insert_assignment);
        $stmt_insert_assignment->bind_param("iisss", $userId, $taskId, $date, $startTime, $endTime);
        return $stmt_insert_assignment->execute();
    }

    public function save($userId, $taskId, $date, $startTime, $endTime) {
        return $this->assignTask($userId, $taskId, $date, $startTime, $endTime);
    }
    public function addTask($taskName) {
        $sql = "INSERT INTO Tasks (TaskName) VALUES (?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $taskName);
        return $stmt->execute();
    }

    public function deleteTask($taskId) {
        $sql = "DELETE FROM Tasks WHERE TaskID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $taskId);
        return $stmt->execute();
    }
    public function updateTask($taskId, $newTaskName) {
        $sql = "UPDATE Tasks SET TaskName = ? WHERE TaskID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $newTaskName, $taskId);
        return $stmt->execute();
    }

    public function getAllTasks() {
        $sql = "SELECT * FROM Tasks";
        $result = $this->conn->query($sql);
        return ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getUsersWithTasks() {
        $sql = "SELECT users.id AS user_id, users.firstname, users.lastname, users.email, Tasks.taskname 
                FROM users 
                LEFT JOIN UserTasks ON users.id = UserTasks.userid
                LEFT JOIN Tasks ON UserTasks.taskid = Tasks.taskid 
                WHERE users.typeOfUser = 'user'";
        return $this->conn->query($sql);
    }
    public function updateUserTasks($userid, $selectedTasks) {
        // Delete existing user Tasks
        $sqlDelete = "DELETE FROM UserTasks WHERE UserID = ?";
        $stmtDelete = $this->conn->prepare($sqlDelete);
        $stmtDelete->bind_param("i", $userid);
        $stmtDelete->execute();
    
        // Insert new user Tasks
        foreach ($selectedTasks as $taskId) {
            $sqlInsert = "INSERT INTO UserTasks (UserID, TaskID) VALUES (?, ?)";
            $stmtInsert = $this->conn->prepare($sqlInsert);
            $stmtInsert->bind_param("ii", $userid, $taskId);
            $stmtInsert->execute();
        }
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

    public function getTaskById($id) {
        $sql_select_task = "SELECT TaskID, TaskName FROM Tasks WHERE TaskID = ?";
        $stmt_select_task = $this->conn->prepare($sql_select_task);
        $stmt_select_task->bind_param("i", $id);
        $stmt_select_task->execute();
        $result_task = $stmt_select_task->get_result();

        if ($result_task->num_rows !== 1) {
            return false;
        }

        $row = $result_task->fetch_assoc();
        $stmt_select_task->close();

        return $row;
    }

    public function getUserTasksById($user_id) {
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
                echo "No Tasks found for this user.";
            }
        }
    }

    public function getTaskName($post = null) {
        if ($post === null) {
            $post = $_POST;
        }

        if (isset($post['task_name'])) {
            return $post['task_name'];
        }
        return null;
    }
    public function updateStatus($request_id, $new_status) {
        $stmt = $this->conn->prepare("UPDATE Timeoff SET Status = ? WHERE ID = ?");
        $stmt->bind_param("ii", $new_status, $request_id);

        if ($stmt->execute()) {
            $stmt->close();
            return "Status updated successfully to " . $new_status . ".";
        } else {
            $error = "Error updating status: " . $stmt->error;
            $stmt->close();
            return $error;
        }
    }

    public function getTimeOffRequests() {
        $sql = "SELECT Timeoff.*, users.firstname, users.lastname 
                FROM Timeoff 
                JOIN users ON Timeoff.UserID = users.id 
                JOIN user_location ON users.id = user_location.user_id
                WHERE Timeoff.Status != 1";
        return $this->conn->query($sql);
    }
}
?>
