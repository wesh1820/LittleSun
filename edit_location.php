<?php
require_once './classes/Location.class.php';
require_once './classes/db.class.php';
require_once './classes/SessionManager.class.php';

// Instantiate the Location class
$locationEditor = new Location($db->getConnection());

// Check if location ID is provided in the URL
if (isset($_GET['id'])) {
    $location_id = $_GET['id'];

    // Fetch location details from the database
    $location = $locationEditor->getLocationById($location_id);

    // Check if location exists
    if (!$location) {
        // If location does not exist, redirect back to the locations page
        header("Location: add_hub_location.php");
        exit();
    }
} else {
    // If location ID is not provided, redirect back to the locations page
    header("Location: add_hub_location.php");
    exit();
}

// Check if form is submitted for updating location
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process form submission
    $name = $_POST['name'];
    $city = $_POST['city'];
    $country = $_POST['country'];

    // Update the location in the database
    $result = $locationEditor->updateLocation($location_id, $name, $city, $country);

    if ($result === true) {
        // Redirect back to hub_location.php after successful update
        header("Location: hub_location.php");
        exit();
    } else {
        // Error handling
        echo "Error: " . $result;
    }
}
?>
