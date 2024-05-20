<?php
require_once './classes/db.class.php';
require_once './classes/User.class.php';
require_once './classes/Session.class.php';
require './sidebar.php';

// Instantiate the database
$db = Database::getInstance();
$conn = $db->getConnection();

// Fetch user email from session
$email = Session::getSession('email');
$user = new User($conn);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profilepic'])) {
    $target_dir = "uploads/";

    // Maak de bestandsnaam veilig
    $filename = basename($_FILES["profilepic"]["name"]);
    $filename = preg_replace("/[^a-zA-Z0-9._-]/", "_", $filename);

    $target_file = $target_dir . $filename;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Controleer of het bestand een afbeelding is
    $check = getimagesize($_FILES["profilepic"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Controleer of bestand al bestaat
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Controleer bestandsgrootte
    if ($_FILES["profilepic"]["size"] > 500000) { // 500KB limiet
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Beperk tot bepaalde bestandstypes
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Controleer of $uploadOk op 0 is gezet door een fout
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        // Controleer of de doelmap bestaat, zo niet, maak deze aan
        if (!is_dir($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                echo "Failed to create directory: " . $target_dir;
                exit;
            }
        }

        // Voeg een extra controle toe om te zien welke rechten de directory heeft
        clearstatcache();
        if (!is_writable($target_dir)) {
            echo "Directory is not writable: " . $target_dir;
            exit;
        }

        if (move_uploaded_file($_FILES["profilepic"]["tmp_name"], $target_file)) {
            echo "The file " . htmlspecialchars(basename($_FILES["profilepic"]["name"])) . " has been uploaded.";

            // Sla het pad op in de database
            $email = $_SESSION['email']; // Zorg ervoor dat de gebruiker ingelogd is
            $sql = "UPDATE users SET profilepic = '$target_file' WHERE email = '$email'";

            if ($conn->query($sql) === TRUE) {
                echo "Profile picture updated successfully.";
            } else {
                echo "Error updating record: " . $conn->error;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>
