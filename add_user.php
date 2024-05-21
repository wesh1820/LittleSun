<?php
require_once 'config.php';
include 'sidebar.php';

$user_id = $_SESSION['user_id'];
$sql_user_location = "SELECT location_id FROM user_location WHERE user_id = ?";
$stmt_user_location = $conn->prepare($sql_user_location);
$stmt_user_location->bind_param("i", $user_id);
$stmt_user_location->execute();
$result_user_location = $stmt_user_location->get_result();

if ($result_user_location->num_rows > 0) {
    $row_user_location = $result_user_location->fetch_assoc();
    $loggedInLocation = $row_user_location['location_id'];
} else {
    $sql_default_location = "SELECT id FROM locations LIMIT 1";
    $result_default_location = $conn->query($sql_default_location);
    $row_default_location = $result_default_location->fetch_assoc();
    $loggedInLocation = $row_default_location['id'];
}

$sql_locations = "SELECT id, name FROM locations WHERE id = ?";
$stmt_locations = $conn->prepare($sql_locations);
$stmt_locations->bind_param("i", $loggedInLocation);
$stmt_locations->execute();
$result_locations = $stmt_locations->get_result();

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
    $typeOfUser = "user";
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
            header("Location: user.php");
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

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Hub user</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2>Add Hub user</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="firstname">First Name:</label>
            <input type="text" id="firstname" name="firstname" required><br>

            <label for="lastname">Last Name:</label>
            <input type="text" id="lastname" name="lastname" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>

            <input type="hidden" id="hub_location" name="hub_location" value="<?php echo $loggedInLocation; ?>">

            <input type="submit" value="Submit">
        </form>
    </div>
    <script>
        $(document).ready(function(){
            $('#close-popup').click(function(){
                $('#tasks-popup').hide();
            });
        });
$(document).ready(function() {
    $(".hamburger-icon").click(function() {
        $(".sidebar").toggleClass("sidebar-open");
    });
    $(".add-button").click(function() {
        $("#popup-content").load("add_user.php");
        $("#myModal").css("display", "block");
    });
    $(".close, .modal").click(function() {
        $("#myModal").css("display", "none");
    });
    $(".modal-content").click(function(event) {
        event.stopPropagation();
    });
});
</script>
</body>

</html>
