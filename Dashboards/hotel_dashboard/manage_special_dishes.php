<?php
// Database connection
$servername = "localhost";
$username = "root"; // your database username
$password = ""; // your database password
$dbname = "farm_hotel_link"; // replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session and get HotelOwnerID from the session
session_start();
$user_id = $_SESSION['UserID']; // Assuming `user_id` is stored in the session

// Fetch HotelOwnerID using the logged-in UserID
$sql = "SELECT HotelOwnerID FROM hotelowner WHERE UserID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hotelOwnerId = $row['HotelOwnerID']; // Get the HotelOwnerID
} else {
    die("Invalid HotelOwnerID. Please contact support.");
}

// Handle form submission for adding/updating dishes
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['deleteDishId'])) {
        // Handle deletion
        $deleteDishId = $_POST['deleteDishId'];
        $sql = "DELETE FROM SpecialDish WHERE SpecialDishID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $deleteDishId);
        if ($stmt->execute()) {
            echo "Dish deleted successfully!";
        } else {
            echo "Error deleting dish: " . $stmt->error;
        }
    } else {
        // Add or update dish
        $dishId = $_POST['dishId'] ?? null;
        $dishName = $_POST['dishName'];
        $vegetableRequirements = $_POST['vegetableRequirements'];
        $dishRating = $_POST['dishRating'];
        $dishPrice = $_POST['dishPrice'];
        $dishPhoto = ""; // Initialize variable for photo

        // Handle file upload
        if (isset($_FILES['dishPhoto']) && $_FILES['dishPhoto']['error'] == UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['dishPhoto']['tmp_name'];
            $fileName = $_FILES['dishPhoto']['name'];
            $uploadDir = '../uploads/';

            // Ensure the uploads directory exists, create if it doesn't
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true); // Create directory with appropriate permissions
            }

            $filePath = $uploadDir . $fileName; // Define the path where you want to save the image
            if (move_uploaded_file($fileTmpPath, $filePath)) {
                $dishPhoto = $filePath; // Save the path of the uploaded photo
            } else {
                echo "Error: Failed to upload file.";
                exit;
            }
        }

        if ($dishId) {
            // Update existing dish
            $sql = "UPDATE SpecialDish SET DishName=?, VegetableRequirements=?, DishRating=?, DishPrice=?, DishPhoto=? WHERE SpecialDishID=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdssi", $dishName, $vegetableRequirements, $dishRating, $dishPrice, $dishPhoto, $dishId);
        } else {
            // Add new dish
            $sql = "INSERT INTO SpecialDish (HotelOwnerID, DishName, VegetableRequirements, DishRating, DishPrice, DishPhoto) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issdds", $hotelOwnerId, $dishName, $vegetableRequirements, $dishRating, $dishPrice, $dishPhoto);
        }

        if ($stmt->execute()) {
            echo "Dish saved successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

// Fetch existing dishes for the logged-in hotel owner
$dishes = [];
$sql = "SELECT * FROM SpecialDish WHERE HotelOwnerID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hotelOwnerId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dishes[] = $row;
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Special Dishes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0f7fa;
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 2px solid #2C3E50;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #2C3E50;
            color: white;
        }
        button {
            background-color: #2C3E50;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
        }
        button:hover {
            background-color: #1e7a1e;
        }
        .form-container {
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            width: 50%;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #2C3E50;
            color: white;
            text-align: center;
            padding: 15px;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="../images/farm_hotel_link_logo.png" alt="Website Logo" style="height: 65px; vertical-align: middle">
        <h1 style="display: inline; vertical-align: middle; font-size:50px;">Farm Hotel Link</h1>
    </div>

    <h1 style="text-align: center;">Manage Special Dishes</h1>

    <div class="form-container">
        <h2>Add/Edit Dish</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="dishId" value="<?php echo isset($dish) ? $dish['SpecialDishID'] : ''; ?>">
            <label for="dishName">Dish Name:</label>
            <input type="text" name="dishName" required value="<?php echo isset($dish) ? htmlspecialchars($dish['DishName']) : ''; ?>"><br><br>

            <label for="vegetableRequirements">Vegetable Requirements:</label>
            <input type="text" name="vegetableRequirements" required value="<?php echo isset($dish) ? htmlspecialchars($dish['VegetableRequirements']) : ''; ?>"><br><br>

            <label for="dishRating">Dish Rating:</label>
            <input type="number" name="dishRating" step="0.1" min="0" max="5" required value="<?php echo isset($dish) ? htmlspecialchars($dish['DishRating']) : ''; ?>"><br><br>

            <label for="dishPrice">Dish Price:</label>
            <input type="number" name="dishPrice" step="0.01" min="0" required value="<?php echo isset($dish) ? htmlspecialchars($dish['DishPrice']) : ''; ?>"><br><br>

            <label for="dishPhoto">Dish Photo:</label>
            <input type="file" name="dishPhoto" accept="image/*"><br><br>

            <button type="submit">Save Dish</button>
        </form>
    </div>

    <table>
        <tr>
            <th>Dish Name</th>
            <th>Vegetable Requirements</th>
            <th>Dish Rating</th>
            <th>Dish Price</th>
            <th>Dish Photo</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($dishes as $dish): ?>
        <tr>
            <td><?php echo htmlspecialchars($dish['DishName']); ?></td>
            <td><?php echo htmlspecialchars($dish['VegetableRequirements']); ?></td>
            <td><?php echo htmlspecialchars($dish['DishRating']); ?></td>
            <td><?php echo htmlspecialchars($dish['DishPrice']); ?></td>
            <td><img src="<?php echo htmlspecialchars($dish['DishPhoto']); ?>" alt="<?php echo htmlspecialchars($dish['DishName']); ?>" width="100"></td>
            <td>
                <form action="" method="post" style="display:inline;">
                    <input type="hidden" name="deleteDishId" value="<?php echo $dish['SpecialDishID']; ?>">
                    <button type="submit" onclick="return confirm('Are you sure you want to delete this dish?');">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
