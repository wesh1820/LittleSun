<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hub Location Management</title>
    <style>
        .hub-location {
            margin-bottom: 10px;
        }
        .delete-button {
            cursor: pointer;
            color: red;
        }
    </style>
</head>
<body>
    <h2>Add Hub Location</h2>
    <form id="addHubLocationForm">
        <label for="user_id">User ID:</label>
        <input type="text" id="user_id" name="user_id" required><br><br>
        
        <label for="location_id">Location ID:</label>
        <input type="text" id="location_id" name="location_id" required><br><br>
        
        <input type="submit" value="Add Hub Location">
    </form>

    <h2>Hub Locations</h2>
    <div id="hubLocationsContainer"></div>

    <script>

        function addHubLocationElement(user_id, location_id) {
            var container = document.getElementById("hubLocationsContainer");
            var hubLocationElement = document.createElement("div");
            hubLocationElement.classList.add("hub-location");
            hubLocationElement.innerHTML = `
                <span>User ID: ${user_id}, Location ID: ${location_id}</span>
                <span class="delete-button" onclick="deleteHubLocation(this)">X</span>
            `;
            container.appendChild(hubLocationElement);
        }


        function deleteHubLocation(element) {
            element.parentElement.remove();
        }


        document.getElementById("addHubLocationForm").addEventListener("submit", function(event) {
            event.preventDefault();
            var user_id = document.getElementById("user_id").value;
            var location_id = document.getElementById("location_id").value;
            addHubLocationElement(user_id, location_id);

            document.getElementById("user_id").value = "";
            document.getElementById("location_id").value = "";
        });
    </script>
</body>
</html>
