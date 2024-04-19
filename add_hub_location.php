<?php
session_start();
require_once 'config.php';

// Fetch hub locations from the database
$sql = "SELECT * FROM locations";
$result = $conn->query($sql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process form submission
    $name = $_POST['name'];
    $city = $_POST['city'];
    $country = $_POST['country'];
    $status = 1; // Assuming new locations are active by default

    // Upload image file
    $image = ''; // Initialize image variable
    if ($_FILES['image']['error'] === 0) {
        // Check if file is uploaded without errors
        $target_dir = "uploads/"; // Directory where the file will be stored
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check file size and file type (you can add more checks if needed)
        if ($_FILES["image"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            exit();
        }
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            exit();
        }

        // Move uploaded file to the specified directory
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = $target_file;
        } else {
            echo "Sorry, there was an error uploading your file.";
            exit();
        }
    }

    // Insert the new location into the database
    $sql = "INSERT INTO locations (image, name, city, country, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $image, $name, $city, $country, $status);

    if ($stmt->execute()) {
        // Redirect back to add_hub_location.php after successful insertion
        header("Location: add_hub_location.php");
        exit();
    } else {
        // Error handling
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voeg locatie toe</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<div class="container">    
    <h2>Voeg locatie toe</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <div>
            <label for="image">Image:</label>
            <input type="file" id="image" name="image" accept="image/*" required><br>
        </div>
        <div>
            <label for="name">Naam:</label>
            <input type="text" id="name" name="name">
        </div>
        <div>
            <label for="city">Stad:</label>
            <input type="text" id="city" name="city">
        </div>
        <div>
            <label for="country">Land:</label>
            <input type="text" id="country" name="country">
        </div>
        <button type="submit">Locatie toevoegen</button>
    </form>
</div>
</body>
</html>
