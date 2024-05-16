<?php
class Calender {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function fetchUserTasks() {
        // Fetch user tasks from the database
        $sql_user_tasks = "SELECT UserTasks.UserTaskID, UserTasks.UserID, UserTasks.TaskID, UserTasks.Date, UserTasks.StartTime, UserTasks.EndTime, users.firstname, users.lastname, tasks.TaskName FROM UserTasks 
        INNER JOIN users ON UserTasks.UserID = users.id 
        INNER JOIN tasks ON UserTasks.TaskID = tasks.TaskID";
        $result_user_tasks = $this->conn->query($sql_user_tasks);
        
        $tasks = [];
        
        if ($result_user_tasks->num_rows > 0) {
            // Organize tasks by date
            while ($row = $result_user_tasks->fetch_assoc()) {
                $date = date("Y-m-d", strtotime($row['Date']));
                $tasks[$date][] = [
                    'start_time' => $row['StartTime'],
                    'end_time' => $row['EndTime'],
                    'task_name' => $row['TaskName'],
                    'user_id' => $row['UserID'], // Store user ID directly
                    'user_name' => $row['firstname'] . ' ' . $row['lastname']
                ];
            }
        }
        
        return $tasks;
    }
    
    public function fetchDaysOff() {
        $days_off = [];
        $sql_days_off = "SELECT user_id, date_off FROM user_days_off";
        $result_days_off = $this->conn->query($sql_days_off);
        
        if ($result_days_off->num_rows > 0) {
            while ($row = $result_days_off->fetch_assoc()) {
                $days_off[$row['user_id']][] = $row['date_off'];
            }
        }
        
        return $days_off;
    }
    
    public function displayCalendar($month, $year) {
    }
}
?>
