<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $image = isset($_POST['image']) ? $_POST['image'] : '';
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $city = isset($_POST['city']) ? $_POST['city'] : '';
    $country = isset($_POST['country']) ? $_POST['country'] : '';

    if (!empty($image) && !empty($name) && !empty($city) && !empty($country)) {

        $sql = "INSERT INTO locations (image, name, city, country) VALUES ('$image', '$name', '$city', '$country')";
        $result = $conn->query($sql);

        if ($result) {
            header("Location: add_hub_location.php");
            exit();
        } else {
            echo "<p>Fout bij het toevoegen van de locatie. Probeer het opnieuw.</p>";
        }
    } else {
        echo "<p>Alle velden zijn vereist. Vul alstublieft alle velden in.</p>";
    }
}

$sql = "SELECT * FROM locations";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voeg locatie toe</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Voeg locatie toe</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div>
            <label for="image">Afbeelding:</label>
            <input type="text" id="image" name="image">
        </div>
        <div>
            <label for="name">Naam:</label>
            <input type="text" id="name" name="name">
        </div>
        <div>
            <label for="city">Stad:</label>
            <input type="text" id="city" name="city">
        </div>
        <div>
            <label for="country">Land:</label>
            <input type="text" id="country" name="country">
        </div>
        <button type="submit">Locatie toevoegen</button>
    </form>

    <h2>Hublocaties</h2>
    <table>
        <tr>
            <th>Afbeelding</th>
            <th>Naam</th>
            <th>Stad</th>
            <th>Land</th>
            <th>Actie</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['image']}</td>";
                echo "<td>{$row['name']}</td>";
                echo "<td>{$row['city']}</td>";
                echo "<td>{$row['country']}</td>";
                echo "<td><a href='delete_location.php?id={$row['id']}'>Verwijderen</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Geen hublocaties gevonden</td></tr>";
        }
        ?>
    </table>
</body>
</html>
