<?php
class Calendar {
    private $conn;
    private $tasksAndSlots;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->tasksAndSlots = array();
    }

    // Fetch tasks and time slots from the database
    public function fetchTasksAndSlots($email) {
        $sql = "SELECT UserTasks.UserTaskID, UserTasks.UserID, UserTasks.TaskID, users.firstname, users.lastname, Tasks.TaskName, Time_slots.StartSlot, Time_slots.EndSlot, Time_slots.Date, Time_slots.Sick
                FROM UserTasks 
                INNER JOIN users ON UserTasks.UserID = users.id 
                INNER JOIN Tasks ON UserTasks.TaskID = Tasks.TaskID 
                INNER JOIN Time_slots ON UserTasks.UserID = Time_slots.UserID AND UserTasks.TaskID = Time_slots.TaskID
                WHERE Time_slots.Sick = 0
                AND users.email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $date = $row['Date'];
                $this->tasksAndSlots[$date][] = $row;
            }
        }
    }

    // Display tasks and time slots for a specific date
    public function displayTasksAndSlots($date) {
        if (isset($this->tasksAndSlots[$date])) {
            foreach ($this->tasksAndSlots[$date] as $taskSlot) {
                echo "<div class='task'>";
                echo "<strong>{$taskSlot['TaskName']}</strong><br>";
                echo "{$taskSlot['StartSlot']} - {$taskSlot['EndSlot']}<br>";
                echo "</div>";
            }
        }
    }

    // Generate monthly view
    public function displayMonthlyView($month, $year) {
        $numDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $firstDay = date("N", strtotime("$year-$month-01"));

        echo "<div class='monthly-view'>";
        echo "<div class='calendar'>";
        echo "<div class='calendar-header'>" . date('F Y', mktime(0, 0, 0, $month, 1, $year)) . "</div>";
        echo "<div class='day-header'>Sun</div>";
        echo "<div class='day-header'>Mon</div>";
        echo "<div class='day-header'>Tue</div>";
        echo "<div class='day-header'>Wed</div>";
        echo "<div class='day-header'>Thu</div>";
        echo "<div class='day-header'>Fri</div>";
        echo "<div class='day-header'>Sat</div>";

        for ($i = 1; $i <= $numDays; $i++) {
            $date = date("Y-m-d", strtotime("$year-$month-$i"));
            $dayOfWeek = date('N', strtotime("$year-$month-$i"));

            if ($i === 1 && $dayOfWeek !== 7) {
                for ($j = 0; $j < $dayOfWeek; $j++) {
                    echo "<div class='empty-cell'></div>";
                }
            }

            echo "<div class='day-cell'>$i";
            $this->displayTasksAndSlots($date);
            echo "</div>";

            if ($dayOfWeek === 7 || $i === $numDays) {
                if ($i === $numDays && $dayOfWeek !== 6) {
                    for ($j = $dayOfWeek; $j < 6; $j++) {
                        echo "<div class='empty-cell'></div>";
                    }
                }
                echo "</div>";
                if ($i !== $numDays) {
                    echo "<div class='calendar-row'>";
                }
            }
        }
        echo "</div>";
        echo "</div>";
    }

    // Generate weekly view
    public function displayWeeklyView($week, $year) {
        $weekStart = new DateTime();
        $weekStart->setISODate($year, $week);
        $weekEnd = clone $weekStart;
        $weekEnd->modify('+6 days');

        $weekStartDate = $weekStart->format('Y-m-d');
        $weekEndDate = $weekEnd->format('Y-m-d');

        echo "<div class='weekly-view'>";
        echo "<div class='weekly-calendar'>";

        for ($i = 0; $i < 7; $i++) {
            $currentDay = date('Y-m-d', strtotime("+$i days", strtotime($weekStartDate)));
            echo "<div class='day'>";
            echo "<h4>" . date('D', strtotime($currentDay)) . "</h4>";
            $this->displayTasksAndSlots($currentDay);
            echo "</div>";
        }

        echo "</div>";
        echo "</div>";
    }

    // Generate daily view
    public function displayDailyView($selectedDay) {
        echo "<div class='daily-view'>";
        echo "<h2>" . date('D, F j, Y', strtotime($selectedDay)) . "</h2>";
        $this->displayTasksAndSlots($selectedDay);
        echo "</div>";
    }
}
?>
