<?php

require_once './classes/Task.class.php'; 
require_once './classes/db.class.php';
require_once './classes/Session.class.php';

class User {
    private $conn;
    private $taskManager;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->taskManager = new Task($conn);
    }

    // Method to get user data based on type (if provided)
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
    

    // Method to get user role based on email
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
    public function getLocationIdFromUserLocation($userId) {
        // Prepare and execute a query to get the location ID from the user_location table
        $query = "SELECT location_id FROM user_location WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Check if a location ID is found
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            return $row['location_id'];
        } else {
            return null; // Location not found for the user
        }
    }
    
    
    // Method to get location ID from user
public function getLocationId($userId) {
    // Controleer of de databaseverbinding is ingesteld
    if (!$this->conn) {
        die("Database connection is not set.");
    }

    // Voer een query uit om de locatie-ID van de gebruiker op te halen
    $query = "SELECT location_id FROM user_location WHERE user_id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        return $row['location_id'];
    } else {
        return null; // Gebruiker niet gevonden of locatie niet ingesteld
    }
}
    
    public function getUsersByLocation($locationId) {
        // Prepare and execute a query to get users based on their location
        $query = "SELECT * FROM users WHERE id IN (SELECT user_id FROM user_location WHERE location_id = ?) AND typeofuser = 'user'";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $locationId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result;
    }
    

// Method to search managers by first or last name
public function searchManagers($search) {
    try {
        // Prepare the query to search for managers by first or last name
        $query = "SELECT * FROM users WHERE firstname LIKE ? OR lastname LIKE ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            // Output detailed error message if prepare fails
            echo "Prepare failed: " . $this->conn->error;
            return false;
        }
        // Bind the search parameter to the query
        $searchParam = '%' . $search . '%';
        $stmt->bind_param('ss', $searchParam, $searchParam);
        // Execute the query
        $stmt->execute();
        // Return the result set
        return $stmt->get_result();
    } catch (Exception $e) {
        // Handle exception
        echo "Error: " . $e->getMessage();
        return false;
    }
}





    

    // Method to get user first name based on email
    public function getID($email) {
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

    // Method to get managers
    public function getManagers() {
        $sql = "SELECT * FROM users WHERE typeOfUser = 'manager'";
        $result = $this->conn->query($sql);
        return $result;
    }

    // Method to get location options
    public function getLocationsOptions() {
        $sql_locations = "SELECT id, name FROM locations";
        $result_locations = $this->conn->query($sql_locations);

        if ($result_locations->num_rows > 0) {
            $location_options = array();
            while ($row_location = $result_locations->fetch_assoc()) {
                $location_id = $row_location['id'];
                $location_name = $row_location['name'];
                $location_options[] = "<option value='$location_id'>$location_name</option>";
            }

            return implode('', $location_options);
        } else {
            return "<option value=''>No locations found</option>";
        }
    }

    public function getUserById($userId) {
        $stmt = $this->conn->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

public function updateUser($id, $firstname, $lastname, $email, $password) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql_update_manager = "UPDATE users SET firstname = ?, lastname = ?, email = ?, password = ? WHERE id = ?";
    $stmt_update_manager = $this->conn->prepare($sql_update_manager);
    $stmt_update_manager->bind_param("ssssi", $firstname, $lastname, $email, $hashed_password, $id);

    $success = $stmt_update_manager->execute();
    $stmt_update_manager->close();

    return $success;
}

    

    public function getAllLocationsOptionsHtml() {
        $location_options_html = "";
        $locations = $this->taskManager->getAllTasks(); // Assuming getAllTasks() fetches locations
        if (!empty($locations)) {
            foreach ($locations as $location) {
                $location_options_html .= "<option value='" . $location['id'] . "'>" . $location['name'] . "</option>";
            }
        } else {
            $location_options_html = "<option value=''>No locations found</option>";
        }
        return $location_options_html;
    }

    public function registerUser($firstname, $lastname, $email, $password, $hub_location_id) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user
        return $this->taskManager->save($firstname, $lastname, 'user', $email, $hashed_password, $hub_location_id);
    }

    // Method to add hub manager
    public function addHubManager($firstname, $lastname, $email, $password, $hub_location_id) {
        $typeOfUser = "manager"; 
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
        $sql_insert_user = "INSERT INTO users (firstname, lastname, typeOfUser, email, password, Profilepic, phoneNumber) VALUES (?, ?, ?, ?, ?, 'default_profile_pic.jpg', '')";
        $stmt_insert_user = $this->conn->prepare($sql_insert_user);
        $stmt_insert_user->bind_param("sssss", $firstname, $lastname, $typeOfUser, $email, $hashed_password);
    
        if ($stmt_insert_user->execute()) {
            $user_id = $stmt_insert_user->insert_id;
            $sql_insert_relation = "INSERT INTO user_location (user_id, location_id) VALUES (?, ?)";
            $stmt_insert_relation = $this->conn->prepare($sql_insert_relation);
            $stmt_insert_relation->bind_param("ii", $user_id, $hub_location_id);
    
            $success = $stmt_insert_relation->execute();
            $stmt_insert_relation->close();
            
            $stmt_insert_user->close();
    
            if ($success) {
                header("Location: manager.php");
                exit();
            } else {
                echo "Error: " . $sql_insert_relation . "<br>" . $this->conn->error;
            }
        } else {
            echo "Error: " . $sql_insert_user . "<br>" . $this->conn->error;
        }
    }
    
    
    public function getAllTasksWithUserCheck($user_id) {
        $sql = "SELECT Tasks.TaskID, Tasks.TaskName, 
                CASE WHEN ut.UserID IS NULL THEN 0 ELSE 1 END AS HasTask
                FROM Tasks
                LEFT JOIN UserTasks ut ON Tasks.TaskID = ut.TaskID AND ut.UserID = ?";
        $stmt = $this->conn->prepare($sql);  // This is where the error occurs
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $Tasks = array();
        while ($row = $result->fetch_assoc()) {
            $Tasks[] = array(
                "TaskID" => $row['TaskID'],
                "TaskName" => $row['TaskName'],
                "HasTask" => $row['HasTask']
            );
        }
    
        return $Tasks;
    }
    public function updateUserTasks($userid, $selectedTasks) {
        $sqlDelete = "DELETE FROM UserTasks WHERE UserID = ?";
        $stmtDelete = $this->conn->prepare($sqlDelete);
        
        // Check for errors in preparing the statement
        if (!$stmtDelete) {
            // Print error message and terminate script
            die("Error in preparing statement: " . $this->conn->error);
        }
    
        // Bind parameters and execute the statement
        $stmtDelete->bind_param("i", $userid);
        $stmtDelete->execute();
    
        // Check for errors in executing the statement
        if ($stmtDelete->error) {
            // Print error message and terminate script
            die("Error in executing statement: " . $stmtDelete->error);
        }
    
        // Insert new user Tasks
        foreach ($selectedTasks as $taskId) {
            $sqlInsert = "INSERT INTO UserTasks (UserID, TaskID) VALUES (?, ?)";
            $stmtInsert = $this->conn->prepare($sqlInsert);
            
            // Check for errors in preparing the statement
            if (!$stmtInsert) {
                // Print error message and terminate script
                die("Error in preparing statement: " . $this->conn->error);
            }
    
            // Bind parameters and execute the statement
            $stmtInsert->bind_param("ii", $userid, $taskId);
            $stmtInsert->execute();
    
            // Check for errors in executing the statement
            if ($stmtInsert->error) {
                // Print error message and terminate script
                die("Error in executing statement: " . $stmtInsert->error);
            }
        }
    }
    public function getManagerById($manager_id) {
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $manager_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return false;
        }
    }
public function updateManager($id, $firstname, $lastname, $email, $password) {
    $sql = "UPDATE users SET firstname = ?, lastname = ?, email = ?, password = ? WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("ssssi", $firstname, $lastname, $email, $password, $id);
    return $stmt->execute();
}

    public function getUserLocation($email) {
        $sql = "SELECT location FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['location'];
        } else {
            return null; // Or handle the case where location is not found
        }
    }
    public function getSickUsersByDate($date) {
        $sql = "SELECT users.firstname, users.lastname, Time_slots.StartSlot, Time_slots.EndSlot, Time_slots.TaskID
                FROM Time_slots
                INNER JOIN users ON Time_slots.UserID = users.id
                WHERE Time_slots.Sick = 1 AND Time_slots.Date = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $date);
        $stmt->execute();
        $result = $stmt->get_result();

        $sick_users = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $sick_users[] = $row;
            }
        }

        return $sick_users;
    }
    public function getManagerLocationId($manager_firstname) {
        $sql = "SELECT location_id FROM user_location INNER JOIN locations ON user_location.location_id = locations.id INNER JOIN users ON user_location.user_id = users.id WHERE users.firstname = '$manager_firstname'";
        $result = $this->conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['location_id'];
        } else {
            // Handle error or return a default value
            return null;
        }
    }
    public function deleteManager($manager_id) {
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            // Handle query preparation error
            echo "Error: " . $this->conn->error;
            return false;
        }
        $stmt->bind_param("i", $manager_id);
        $result = $stmt->execute();
        if (!$result) {
            // Handle query execution error
            echo "Error: " . $stmt->error;
            return false;
        } else {
            return true;
        }
    }
    public function getUserTasksAndTimeSlots($user_id) {
        $sql = "SELECT 
                    Time_slots.TimeSlotID, 
                    Tasks.TaskName, 
                    Time_slots.StartSlot, 
                    Time_slots.EndSlot, 
                    Time_slots.Date, 
                    Time_slots.Sick
                FROM 
                    UserTasks
                INNER JOIN 
                    Tasks ON UserTasks.TaskID = Tasks.TaskID
                INNER JOIN 
                    Time_slots ON UserTasks.TaskID = Time_slots.TaskID
                WHERE 
                    UserTasks.UserID = ? 
                    AND Time_slots.UserID = ? 
                    AND Time_slots.Sick = 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<h3>Your Tasks and Time Slots:</h3>";
            echo "<table border='1'>";
            echo "<tr><th>Task Name</th><th>Start Time</th><th>End Time</th><th>Date</th><th>Sick</th><th>Action</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["TaskName"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["StartSlot"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["EndSlot"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["Date"]) . "</td>";
                echo "<td>" . ($row["Sick"] ? "Yes" : "No") . "</td>";
                echo "<td>
                        <form method='POST' action=''>
                            <input type='hidden' name='Timeslot_id' value='" . $row['TimeSlotID'] . "'>
                            <button class='view-button' type='submit' name='mark_sick'>Mark as Sick</button>
                        </form>
                      </td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No Tasks and Time slots found for this user.";
        }
    }

    public function setSick($Timeslot_id) {
        $sql = "UPDATE Time_slots SET Sick = 1 WHERE TimeSlotID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $Timeslot_id);
        if ($stmt->execute()) {
            echo "The selected Time slot has been marked as sick.";
        } else {
            echo "Error: " . $this->conn->error;
        }
    }

    public function setSickForDay($user_id, $date) {
        $sql = "UPDATE Time_slots SET Sick = 1 WHERE UserID = ? AND Date = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $user_id, $date);
        if ($stmt->execute()) {
            echo "All Time slots for $date have been marked as sick.";
        } else {
            echo "Error: " . $this->conn->error;
        }
    }
}
    
?>
