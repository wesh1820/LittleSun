
<?php
require_once 'classes/db.class.php';

$config = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'dbname' => 'Littlesun'
];

$db = Database::getInstance($config);

// Rest van de code in user.php
?>
