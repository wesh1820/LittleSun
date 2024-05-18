<?php
require_once './classes/User.class.php';
require_once './classes/db.class.php';

// Create a new user instance
$user = new User($db->getConnection());

// Get the ID of the logged-in user
$loggedInUserId = $_SESSION['user_id']; // Adjust this based on how you identify the user

// Get the location ID of the logged-in user
$loggedInUserLocationId = $user->getLocationId($loggedInUserId);

// Get users only from the location of the logged-in user
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
                    echo "<a href='javascript:void(0);' onclick='viewTasks(" . $row['id'] . ")'>View Tasks</a>";
                    echo "<button onclick='addUser(" . $row['id'] . ")'>Add User</button>";
                    echo "<button onclick='editUser(" . $row['id'] . ")'>Edit</button>";
                    echo "<button onclick='deleteUser(" . $row['id'] . ")'>Delete</button>";
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
            function addUser(userId) {
        // Redirect to add_user.php with the user ID as a query parameter
        window.location.href = 'add_user.php?userId=' + userId;
    }
        function viewTasks(userId) {
            $.ajax({
                url: 'tasks.php',
                type: 'GET',
                data: { userid: userId },
                success: function(response){
                    $('#tasks-popup-content').html(response);
                    $('#tasks-popup').show();
                },
                error: function(xhr, status, error){
                    console.error(xhr.responseText);
                }
            });
        }

        function editUser(userId) {
            // Redirect to edit user page with user ID
            window.location.href = "edit_user.php?userid=" + userId;
        }

        function deleteUser(userId) {
            // Confirm before deleting the user
            if (confirm("Are you sure you want to delete this user?")) {
                // Perform AJAX request to delete user
                $.ajax({
                    url: "delete_user.php",
                    type: "POST",
                    data: { userid: userId },
                    success: function(response) {
                        // Reload the page after successful deletion
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            }
        }

        $(document).ready(function(){
            $('#close-popup').click(function(){
                $('#tasks-popup').hide();
            });
        });
    </script>
</body>
</html>