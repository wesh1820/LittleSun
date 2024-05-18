<?php
require_once './classes/Location.class.php';
require_once './classes/Task.class.php'; 
require_once './classes/db.class.php'; 
require_once './classes/Session.class.php';
require_once './classes/User.class.php';
include 'sidebar.php';

$userManager = new User($conn);
$locationManager = new Location($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hub_location_id = $_POST['hub_location']; 

    $userManager->addHubManager($firstname, $lastname, $email, $password, $hub_location_id);
}

$location_options_html = $userManager->getLocationsOptions();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Hub Manager</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2>Add Hub Manager</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="firstname">First Name:</label>
            <input type="text" id="firstname" name="firstname" required><br>

            <label for="lastname">Last Name:</label>
            <input type="text" id="lastname" name="lastname" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>

            <label for="hub_location">Hub Location:</label>
            <select id="hub_location" name="hub_location" required>
                <?php echo $location_options_html; ?>
            </select><br>

            <input type="submit" value="Submit">
        </form>
    </div>
</body>
</html>
