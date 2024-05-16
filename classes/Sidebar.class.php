<?php

class Sidebar {
    private $userRole;

    public function __construct($userRole) {
        $this->userRole = $userRole;
    }

    public function render() {
        echo '<div class="sidebar">';
        echo '<h2><i class="fas fa-columns"></i></h2>';
        $this->renderLinks();
        echo '</div>';
    }

    private function renderLinks() {
        switch ($this->userRole) {
            case 'admin':
                $this->renderAdminLinks();
                break;
            case 'manager':
                $this->renderManagerLinks();
                break;
            case 'user':
                $this->renderUserLinks();
                break;
            default:
                // Handle unrecognized role
                break;
        }
    }

    private function renderAdminLinks() {
        echo '<a href="manager.php"><i class="fas fa-user">Managers</i></a>';
        echo '<a href="hub_location.php"><i class="fas fa-map-marker-alt">Locations</i></a>';
        echo '<a href="tasks.php"><i class="fas fa-tasks">Tasks</i></a>';
        echo '<a href="logout.php"><i class="fas fa-sign-out-alt">Logout</i></a>';
    }

    private function renderManagerLinks() {
        echo '<a href="user.php"><i class="fas fa-user">Users</i></a>';
        echo '<a href="user_tasks.php"><i class="fa fa-tasks"> tasks</i></a>';
        echo '<a href="calender.php"><i class="fa fa-calendar-alt"> calender</i></a>';
        echo '<a href="logout.php"><i class="fas fa-sign-out-alt">Logout</i></a>';
    }

    private function renderUserLinks() {
        echo '<a href="clockin.php"><i class="fas fa-tasks">Clockin</i></a>';
        echo '<a href="taskasuser.php"><i class="fas fa-tasks">Tasks</i></a>';
        echo '<a href="logout.php"><i class="fas fa-sign-out-alt">Logout</i></a>';
    }
}

?>
