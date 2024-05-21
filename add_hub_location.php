<?php
require_once './classes/Location.class.php';
require_once './classes/db.class.php';
require_once './classes/Session.class.php';

// Establish a database connection
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
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Location</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">

    <style>

        .container {
            margin-left: 0px;
        }
        .pop-up-container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            top: 20px;
            right: 20px;
            background-color: #e9ca01;
            color: rgb(54, 52, 52);
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
        }
    </style>

</head>
<body>
<div class="container">    
    <h2>Add Location</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <div>
            <label  "name">Name</label>
            <input type="text" id="name" name="name" placeholder="Alexander" required>
        </div>
        <div>
            <label for="city">City</label>
            <input type="text" id="city" name="city" placeholder="Brussels" required>
        </div>
        <div>
            <label for="country">Country</label>
            <input type="text" id="country" name="country" placeholder="Belgium" required>
        </div>
        <button type="submit">Add Location</button>
    </form>
</div>
</body>
</html>
