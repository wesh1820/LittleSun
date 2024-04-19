<div class="sidebar">
<p> Logged in as: <?php echo $_SESSION['firstname']; ?> (<?php echo $user_role; ?>)</p>
    <?php 
    if ($user_role === 'admin') {
        echo '<h2><i class="fas fa-columns"></i></h2>';
        echo '<a href="manager.php"><i class="fas fa-user"> Manager</i></a>';
        echo '<a href="hub_location.php"><i class="fas fa-map-marker-alt"> Locations</i></a>';
    } elseif ($user_role === 'manager') {
        echo '<h2><i class="fas fa-columns"></i></h2>';
    }
    ?>
 <a class="logout-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>
