<?php
require_once './classes/Location.class.php'; // Assuming Location class file path

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

// Check if location ID is provided and not empty
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: hub_location.php");
    exit();
}

// Get location ID from the URL
$location_id = $_GET['id'];

// Instantiate Location class with database connection
$locationEditor = new Location($conn);

// Delete location based on ID
$result = $locationEditor->deleteLocation($location_id);

if ($result) {
    // If deletion is successful, redirect to hub_location.php
    header("Location: hub_location.php");
    exit();
} else {
    // If an error occurs during deletion, display an error message
    echo "Er is een fout opgetreden bij het verwijderen van de locatie. Probeer het opnieuw.";
}
?>
