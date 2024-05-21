<?php
require_once './classes/User.class.php';
require_once './classes/db.class.php';
require_once './classes/Session.class.php';

$id = Session::getSession('id');
$user = new User($db->getConnection());
$user_role = $user->getUserRole($id);
Session::setSession('id', $user->getID($id));
$db->closeConnection();
$userManager = new User($conn);
$result_managers = $userManager->getManagers();
$user_role = "";
if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];
    $user_role = $userManager->getUserRole($id);
}

if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $result_managers = $userManager->searchManagers($search); 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hub Managers</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
        <div class="sidebar">
        <?php include 'sidebar.php'; ?>
    </div>
<div class="container">    
    <h2>Hub Managers</h2>
    <form method="GET">
        <input type="text" name="search" placeholder="Search by first or last name" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
        <button class="view-button" type="submit">Search</button>
    </form>
    <?php
    if ($result_managers->num_rows > 0) {

        echo "<table>";
        echo "<tr><th>First Name</th><th>Last Name</th><th>Email</th><th>Actions</th></tr>";
        while ($row_manager = $result_managers->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row_manager['firstname']}</td>";
            echo "<td>{$row_manager['lastname']}</td>";
            echo "<td>{$row_manager['email']}</td>";
            echo "<td>";
            echo "<a href='edit_manager.php?id={$row_manager['id']}' class='view-button'>Edit</a> ";
            echo "<a href='delete_manager.php?id={$row_manager['id']}' class='view-button'>Delete</a>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } else {

        echo "<p>No hub managers found.</p>";
    }
    ?>
</div>

<?php if ($user_role === 'admin') : ?>
    <a class="add-button" href="#">Add</a>
<?php endif; ?>

<div id="myModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <div id="popup-content"></div>
  </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="./js/script.js"></script>
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
