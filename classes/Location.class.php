<?php
require_once 'config.php';

class Location {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getLocationById($location_id) {
        $sql = "SELECT * FROM locations WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $location_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $location = $result->fetch_assoc();
        $stmt->close();

        return $location;
    }

    public function updateLocation($location_id, $name, $city, $country) {
        $status = 1; // Assuming new locations are active by default

        $sql = "UPDATE locations SET name = ?, city = ?, country = ?, status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $city, $country, $status, $location_id);

        if ($stmt->execute()) {
            return true;
        } else {
            return "Error: " . $sql . "<br>" . $this->conn->error;
        }

        $stmt->close();
    }

    public function addLocation($name, $city, $country) {
        $status = 1;
        $default_image = "default.jpg";

        $sql = "INSERT INTO locations (name, city, country, status, image) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssis", $name, $city, $country, $status, $default_image);

        if ($stmt->execute()) {
            return true;
        } else {
            return "Error: " . $sql . "<br>" . $this->conn->error;
        }

        $stmt->close();
    }

    public function getLocations() {
        $sql = "SELECT * FROM locations";
        $result = $this->conn->query($sql);

        $locations = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $locations[] = $row;
            }
        }

        return $locations;
    }

    public function closeConnection() {
        $this->conn->close();
    }
    public function deleteLocation($location_id) {
        // Prepare and execute the SQL query to delete the location
        $sql = "DELETE FROM locations WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $location_id);
        
        if ($stmt->execute()) {
            // Return true if deletion is successful
            return true;
        } else {
            // Return false if an error occurs during deletion
            return false;
        }
    }
}

?>
