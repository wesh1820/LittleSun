<?php 
require_once './config.php'; 

$user_role = ""; 
$profile_pic = "";
$user_id = "";

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql_role = "SELECT * FROM users WHERE id = '$user_id'";
    $result_role = $conn->query($sql_role);

    if ($result_role && $result_role->num_rows > 0) {
        $row_role = $result_role->fetch_assoc();
        $user_role = $row_role['typeOfUser'];
        $profile_pic = $row_role['Profilepic']; 
        $user_id = $row_role['id'];
    } else {
        echo "User not found!";
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_FILES['profilepic']) && $_FILES['profilepic']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profilepic"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["profilepic"]["tmp_name"]);
        if($check !== false) {
            if (move_uploaded_file($_FILES["profilepic"]["tmp_name"], $target_file)) {
                $profile_pic = $target_file;
                $update_sql = "UPDATE users SET Profilepic = '$profile_pic' WHERE id = '$user_id'";
                if ($conn->query($update_sql) === TRUE) {
                    echo "Profile picture updated successfully.";
                } else {
                    echo "Error updating profile picture: " . $conn->error;
                }
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            echo "File is not an image.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="editprofile">
    <a href="" class="close-btn">&times;</a>
        <h2 class="edit-profile-tekst">Edit Picture</h2>
        <div class="profile-pic">
            <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture" class="profile-img">
        </div>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <h3>Change Profile Picture:</h3>
            <input type="file" name="profilepic" id="profilepic">
            <input type="submit" value="Upload Image" name="submit">
        </form>
        
    </div>
</body>
</html>
