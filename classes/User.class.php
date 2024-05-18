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
        $query = "SELECT * FROM users WHERE id IN (SELECT user_id FROM user_location WHERE location_id = ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $locationId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result;
    }
    

    

    // Method to get user first name based on email
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

    // Method to get user by ID
    public function getUserById($id) {
        $sql_select_manager = "SELECT id, firstname, lastname, email FROM users WHERE id = ?";
        $stmt_select_manager = $this->conn->prepare($sql_select_manager);
        $stmt_select_manager->bind_param("i", $id);
        $stmt_select_manager->execute();
        $result_manager = $stmt_select_manager->get_result();

        if ($result_manager->num_rows !== 1) {
            return false;
        }

        $row = $result_manager->fetch_assoc();
        $stmt_select_manager->close();

        return $row;
    }

    // Method to update user information
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

        $sql_insert_user = "INSERT INTO users (firstname, lastname, typeOfUser, email, password, phoneNumber) VALUES (?, ?, ?, ?, ?, '')";
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
        $sql = "SELECT tasks.TaskID, tasks.TaskName, 
                CASE WHEN ut.UserID IS NULL THEN 0 ELSE 1 END AS HasTask
                FROM tasks
                LEFT JOIN UserTasks ut ON tasks.TaskID = ut.TaskID AND ut.UserID = ?";
        $stmt = $this->conn->prepare($sql);  // This is where the error occurs
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $tasks = array();
        while ($row = $result->fetch_assoc()) {
            $tasks[] = array(
                "TaskID" => $row['TaskID'],
                "TaskName" => $row['TaskName'],
                "HasTask" => $row['HasTask']
            );
        }
    
        return $tasks;
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
    
        // Insert new user tasks
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
}
    
?>
