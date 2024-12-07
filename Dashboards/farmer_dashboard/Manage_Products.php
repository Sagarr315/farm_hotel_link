<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "farm_hotel_link"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    // Redirect if no user is logged in
    header("Location: login.php");
    exit();
}

// Get the logged-in user's ID
$user_id = $_SESSION['UserID'];

// Fetch the FarmerID from the farmers table based on the logged-in UserID and UserType 'farmer'
$sql = "SELECT FarmerID FROM farmers WHERE UserID = '$user_id'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $farmer_id = $row['FarmerID'];
} else {
    // If no matching farmer is found
    echo "Invalid FarmerID. Please contact support.";
    exit();
}

// Handle adding a new product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = $conn->real_escape_string($_POST['price']);
    $quantity = $conn->real_escape_string($_POST['quantity']);
    $category = $conn->real_escape_string($_POST['category']);
    $status = $conn->real_escape_string($_POST['status']);

    // Image upload
    $vegetable_img = '';
    if (!empty($_FILES['vegetable_img']['name'])) {
        // Ensure the uploads folder exists and is writable
        $target_dir = "../uploads/"; // Assuming the folder is in the same directory as this file
        $target_file = $target_dir . basename($_FILES["vegetable_img"]["name"]);

        // Check if the directory exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);  // Create the directory if it doesn't exist
        }

        // Attempt to move the uploaded file
        if (move_uploaded_file($_FILES["vegetable_img"]["tmp_name"], $target_file)) {
            $vegetable_img = $target_file; // Store the path of the uploaded file
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }

    $vegetable_img = $conn->real_escape_string($vegetable_img); // Escape the image path

    // Insert the new product into the database
    $sql = "INSERT INTO Product (FarmerID, Name, Description, Price, QuantityAvailable, Category, VegetableImg, Status) 
            VALUES ('$farmer_id', '$name', '$description', '$price', '$quantity', '$category', '$vegetable_img', '$status')";

    if ($conn->query($sql) === TRUE) {
        $message = "Product added successfully!";
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle delete request
if (isset($_GET['delete'])) {
    $product_id = $conn->real_escape_string($_GET['delete']);
    $sql = "DELETE FROM Product WHERE ProductID = '$product_id'";
    if ($conn->query($sql) === TRUE) {
        $message = "Product deleted successfully!";
    } else {
        $message = "Error deleting record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #F0FFF0;
            color: #333;
            margin:0;
            padding:0;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 28px;
        }
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        input[type="text"], input[type="number"], input[type="file"], select, textarea {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
            font-size: 14px;
        }
        button {
            display: inline-block;
            padding: 10px 20px;
            color: #fff;
            background-color: #228B22;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #2980b9;
        }
        table {
            width: 100%;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #228B22;
            color: #fff;
            font-weight: bold;
        }
        .delete-button {
            color: red;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .delete-button:hover {
            transform: scale(1.1);
        }
        .message {
            text-align: center;
            padding: 10px;
            background-color: #2ecc71;
            color: #fff;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .header {
            background-color: #228B22;
            color: white;
            text-align: center;
            padding: 15px;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .header img {
            height: 50px;
            vertical-align: middle;
        }
    </style>
</head>
<body>

    <div class="header">
        <img src="../images/farm_hotel_link_logo.png" alt="Website Logo" style="width: 65px; height:65px;">
        <h1 style="font-weight:bold; font-size:45px; display: inline-block; vertical-align: middle;">Farm Hotel Link</h1>
    </div>

<div class="container">
    <h2>Manage Products</h2>

    <?php if (isset($message)) { echo "<div class='message'>$message</div>"; } ?>

    <!-- Form for adding new products -->
    <div class="form-container">
        <form action="manage_products.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Product Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <div class="form-group">
                <label for="price">Price (per unit):</label>
                <input type="number" id="price" name="price" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity Available:</label>
                <input type="number" id="quantity" name="quantity" required>
            </div>
            <div class="form-group">
                <label for="category">Category:</label>
                <input type="text" id="category" name="category">
            </div>
            <div class="form-group">
                <label for="vegetable_img">Vegetable Image:</label>
                <input type="file" id="vegetable_img" name="vegetable_img">
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="Available">Available</option>
                    <option value="Out of Stock">Out of Stock</option>
                </select>
            </div>
            <button type="submit" name="add_product">Add Product</button>
        </form>
    </div>

    <!-- Display existing products -->
    <h3>Existing Products</h3>
    <table>
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Category</th>
                <th>Image</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM Product WHERE FarmerID = '$farmer_id'"; // Use dynamic FarmerID
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row['ProductID'] . "</td>
                            <td>" . $row['Name'] . "</td>
                            <td>" . $row['Price'] . "</td>
                            <td>" . $row['QuantityAvailable'] . "</td>
                            <td>" . $row['Category'] . "</td>
                            <td><img src='" . $row['VegetableImg'] . "' width='50'></td>
                            <td>" . $row['Status'] . "</td>
                            <td><a href='?delete=" . $row['ProductID'] . "' class='delete-button'>Delete</a></td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No products available.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
