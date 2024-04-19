<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: add_hub_location.php");
    exit();
}

$id = $_GET['id'];

$sql = "DELETE FROM locations WHERE id = $id";
$result = $conn->query($sql);

if ($result) {

    header("Location: add_hub_location.php");
    exit();
} else {

    echo "Er is een fout opgetreden bij het verwijderen van de locatie. Probeer het opnieuw.";
}
?>
