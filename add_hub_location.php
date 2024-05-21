<?php
require_once './classes/Location.class.php';
require_once './classes/db.class.php';
require_once './classes/Session.class.php';

$location = new Location($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $city = $_POST['city'];
    $country = $_POST['country'];

    $result = $location->addLocation($name, $city, $country);

    if ($result === true) {
        header("Location: hub_location.php");
        exit();
    } else {
        echo "Error: " . $result;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Location</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<div class="container">    
    <h2>Add Location</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div>
            <label for="city">City:</label>
            <input type="text" id="city" name="city" required>
        </div>
        <div>
            <label for="country">Country:</label>
            <input type="text" id="country" name="country" required>
        </div>
        <button type="submit">Add Location</button>
    </form>
</div>
</body>
</html>