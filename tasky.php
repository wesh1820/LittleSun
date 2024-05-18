<?php
require_once './classes/User.class.php';
require_once './classes/db.class.php';

// Maak een instantie van de db-klasse


// Create a new database connection
$connection = $db->getConnection();

// Create a new user instance
$user = new User($connection);

// Vervolg van je code...


// Get the ID van de ingelogde gebruiker
$loggedInUserId = $_SESSION['user_id']; // Pas dit aan op basis van hoe je de gebruiker identificeert

// Haal de locatie ID van de ingelogde gebruiker op
$loggedInUserLocationId = $user->getLocationId($loggedInUserId);

// Haal alleen de gebruikers op van de locatie van de ingelogde gebruiker
$users = $user->getUsersByLocation($loggedInUserLocationId);

include 'sidebar.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Users and Their Tasks</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* CSS styles for the table */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #dddddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        /* CSS styles for the popup */
        #tasks-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ccc;
            z-index: 9999;
        }
        #close-popup {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Users and Their Tasks</h1>

        <h2>Select a user to view their tasks:</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Loop through all users and display them in a table
                while ($row = $users->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['firstname'] . " " . $row['lastname'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>";
                    echo "<button onclick='viewTasks(" . $row['id'] . ")'>Assign tasks</button>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Popup to display tasks -->
    <div id="tasks-popup">
        <div id="tasks-popup-content">
            <!-- Tasks content will be loaded here -->
        </div>
        <span id="close-popup">&times;</span>
    </div>

    <script>
        function viewTasks(userId) {
            $.ajax({
                url: 'get_user_tasks.php',
                type: 'GET',
                data: { userID: userId },
                success: function(response){
                    $('#tasks-popup-content').html(response);
                    $('#tasks-popup').show();
                },
                error: function(xhr, status, error){
                    console.error(xhr.responseText);
                }
            });
        }

        $(document).ready(function(){
            $('#close-popup').click(function(){
                $('#tasks-popup').hide();
            });
        });
    </script>
</body>
</html>


