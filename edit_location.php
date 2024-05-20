<?php
require_once './classes/db.class.php';
require_once './classes/Location.class.php';
require './sidebar.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Location ID is missing.";
    exit();
}

$location_id = $_GET['id'];
$conn = $db->getConnection();
$locationManager = new Location($conn);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $city = $_POST['city'];
    $country = $_POST['country'];

    $success = $locationManager->updateLocation($location_id, $name, $city, $country);
    if ($success) {
        echo "<script>alert('Location updated successfully');</script>";
        echo "<script>window.location.href = 'hub_location.php';</script>";
    } else {
        echo "<script>alert('Failed to update location');</script>";
    }
}

$location = $locationManager->getLocationById($location_id);
if (!$location) {
    echo "Location not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Location</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>Edit Location</h2>
        <form action="" method="post">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo $location['name']; ?>" required><br>
            <label for="city">City:</label>
            <input type="text" id="city" name="city" value="<?php echo $location['city']; ?>" required><br>
            <label for="country">Country:</label>
            <input type="text" id="country" name="country" value="<?php echo $location['country']; ?>" required><br>
            <input type="submit" value="Update">
        </form>
    </div>
</body>
</html>
