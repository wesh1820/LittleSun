<?php
require_once 'config.php';

$sql_locations = "SELECT id, name FROM locations";
$result_locations = $conn->query($sql_locations);

if ($result_locations->num_rows > 0) {
    $location_options = array();
    while ($row_location = $result_locations->fetch_assoc()) {
        $location_id = $row_location['id'];
        $location_name = $row_location['name'];
        $location_options[] = "<option value='$location_id'>$location_name</option>";
    }

    $location_options_html = implode('', $location_options);
} else {
    $location_options_html = "<option value=''>No locations found</option>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hub_location_id = $_POST['hub_location']; 
    $typeOfUser = "manager"; 

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql_insert_user = "INSERT INTO users (firstname, lastname, typeOfUser, email, password, phoneNumber) VALUES (?, ?, ?, ?, ?, '')";
    $stmt_insert_user = $conn->prepare($sql_insert_user);
    $stmt_insert_user->bind_param("sssss", $firstname, $lastname, $typeOfUser, $email, $hashed_password);

    if ($stmt_insert_user->execute()) {
        $user_id = $stmt_insert_user->insert_id;
        $sql_insert_relation = "INSERT INTO user_location (user_id, location_id) VALUES (?, ?)";
        $stmt_insert_relation = $conn->prepare($sql_insert_relation);
        $stmt_insert_relation->bind_param("ii", $user_id, $hub_location_id);
        
        if ($stmt_insert_relation->execute()) {
            header("Location: manager.php");
            exit();
        } else {

            echo "Error: " . $sql_insert_relation . "<br>" . $conn->error;
        }
    } else {

        echo "Error: " . $sql_insert_user . "<br>" . $conn->error;
    }

    $stmt_insert_user->close();
    if (isset($stmt_insert_relation)) {
        $stmt_insert_relation->close();
    }
}

$sql_managers = "SELECT * FROM users WHERE typeOfUser = 'manager'";
$result_managers = $conn->query($sql_managers);

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
