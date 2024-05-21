<?php

require_once './config.php'; // Verbind met de database

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profilepic'])) {
    $target_dir = "uploads/";

    $filename = basename($_FILES["profilepic"]["name"]);
    $filename = preg_replace("/[^a-zA-Z0-9._-]/", "_", $filename);

    $target_file = $target_dir . $filename;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["profilepic"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    if ($_FILES["profilepic"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (!is_dir($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                echo "Failed to create directory: " . $target_dir;
                exit;
            }
        }

        clearstatcache();
        if (!is_writable($target_dir)) {
            echo "Directory is not writable: " . $target_dir;
            exit;
        }

        if (move_uploaded_file($_FILES["profilepic"]["tmp_name"], $target_file)) {
            echo "The file " . htmlspecialchars(basename($_FILES["profilepic"]["name"])) . " has been uploaded.";

            $email = $_SESSION['email'];
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
