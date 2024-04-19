<?php
session_start();
require_once 'config.php';

// Check if location ID is provided in the URL
if (isset($_GET['id'])) {
    $location_id = $_GET['id'];

    // Fetch location details from the database
    $sql = "SELECT * FROM locations WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $location_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $location = $result->fetch_assoc();

    // Close the prepared statement
    $stmt->close();
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
    $status = 1; // Assuming new locations are active by default

    // Update the location in the database
    $sql = "UPDATE locations SET name = ?, city = ?, country = ?, status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $city, $country, $status, $location_id);

    if ($stmt->execute()) {
        // Redirect back to add_hub_location.php after successful update
        header("Location: add_hub_location.php");
        exit();
    } else {
        // Error handling
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
}

// Close the database connection
$conn->close();
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
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $location_id); ?>" method="post">
        <div>
            <label for="name">Naam:</label>
            <input type="text" id="name" name="name" value="<?php echo $location['name']; ?>" required>
        </div>
        <div>
            <label for="city">Stad:</label>
            <input type="text" id="city" name="city" value="<?php echo $location['city']; ?>" required>
        </div>
        <div>
            <label for="country">Land:</label>
            <input type="text" id="country" name="country" value="<?php echo $location['country']; ?>" required>
        </div>
        <button type="submit">Opslaan</button>
    </form>
</div>
</body>
</html>
