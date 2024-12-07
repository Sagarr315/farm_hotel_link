<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "farm_hotel_link");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

// Ensure UserID is set in session and is valid
if (!isset($_SESSION['UserID'])) {
    die("User is not logged in.");
}

$userID = $_SESSION['UserID']; // Get UserID from session

// Fetch HotelOwnerID from hotelowner table based on UserID
$query = "SELECT HotelOwnerID FROM hotelowner WHERE UserID = '$userID'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hotelOwnerID = $row['HotelOwnerID']; // Get the correct HotelOwnerID
} else {
    die("Hotel owner not found in the database.");
}

// Handle order placement and message display
$orderPlaced = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $productID = $_POST['product_id'];
    $quantity = $_POST['quantity']; // Correctly get the quantity from the form
    $totalPrice = $_POST['total_price']; // Get the correct total price from the hidden input field

    // Fetch FarmerID for the selected product
    $farmerQuery = "SELECT FarmerID FROM product WHERE ProductID = '$productID'";
    $farmerResult = $conn->query($farmerQuery);

    if ($farmerResult->num_rows > 0) {
        $farmerRow = $farmerResult->fetch_assoc();
        $farmerID = $farmerRow['FarmerID'];

        // Insert into orders table with the FarmerID
        $conn->query("INSERT INTO orders (HotelOwnerID, FarmerID, OrderDate) 
                      VALUES ('$hotelOwnerID', '$farmerID', NOW())");
        $orderID = $conn->insert_id; // Get the last inserted OrderID

        // Insert into orderitems table with correct quantity and total price
        $conn->query("INSERT INTO orderitems (OrderID, ProductID, Quantity, Price) 
                      VALUES ('$orderID', '$productID', '$quantity', '$totalPrice')");

        // Create Payment record with Pending status
        $conn->query("INSERT INTO Payment (OrderID, Amount, PaymentDate, Status, PaymentMethod) 
                      VALUES ('$orderID', '$totalPrice', NOW(), 'Pending', 'Cash on Delivery')");

        $orderPlaced = true; // Set orderPlaced to true to show success message
    } else {
        die("Farmer not found for the selected product.");
    }
}

// Fetch products posted by farmers
$sql = "SELECT ProductID, Name, Description, Price, VegetableImg FROM product";
$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Products</title>
    <style>
        /* CSS styling for layout and animations */
        body {
            font-family: Arial, sans-serif;
            background: #e0f7fa;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #2e3a50;
            color: white;
            text-align: center;
            padding: 15px;
            position: fixed;
            top: 0;
            z-index: 1000;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100px;
        }
        .header img {
            height: 50px;
            vertical-align: middle;
            margin-right: 10px;
        }
        .header h1 {
            font-weight: bold;
            font-size: 45px;
            display: inline-block;
            margin: 0;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 120px 20px 20px;
            opacity: 0;
            animation: fadeInDown 3s ease forwards;
        }
        @keyframes fadeInDown {
            0% {
                opacity: 0;
                transform: translateY(-50px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
        h1 {
            text-align: center;
            font-size: 2em;
            margin-bottom: 20px;
        }
        .product {
            display: flex;
            align-items: center;
            background: #fff;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s ease;
        }
        .product:hover {
            transform: scale(1.02);
        }
        .product img {
            width: 100px;
            height: 100px;
            border-radius: 10px;
            margin-right: 20px;
        }
        .product-details {
            flex-grow: 1;
        }
        .product-details h2 {
            margin: 0;
            color: #333;
        }
        .price, .quantity, .total-cost {
            font-weight: bold;
            margin-top: 5px;
        }
        .quantity-control {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }
        .quantity-control button {
            padding: 5px 10px;
            font-size: 16px;
            margin: 0 5px;
            cursor: pointer;
        }
        .order-btn {
            background-color: #2c3e50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .order-btn:hover {
            background-color: #45a049;
        }
        .success-message {
            text-align: center;
            color: green;
            font-size: 18px;
            margin-top: 20px;
            display: none;
        }
    </style>
    <script>
        // JavaScript to dynamically calculate total price based on selected quantity
        function updateTotalCost(price, quantityId, costId, productId) {
            const quantity = document.getElementById(quantityId).value;
            const totalCost = price * quantity;
            document.getElementById(costId).innerText = "Total Cost: ₹" + totalCost.toFixed(2);
            
            // Update the hidden input field for quantity and total price
            document.getElementById('quantity-input-' + productId).value = quantity;
            document.getElementById('total-price-input-' + productId).value = totalCost.toFixed(2);
        }

        function showSuccessMessage() {
            const successMessage = document.getElementById('success-message');
            successMessage.style.display = 'block';
            setTimeout(() => {
                successMessage.style.display = 'none';
                window.location.href = ''; // Reload the page
            }, 5000); // 5 seconds
        }
    </script>
</head>
<body>
<div class="header">
    <img src="../images/farm_hotel_link_logo.png" alt="Website Logo" style="width: 65px; height:65px;">
    <h1>Farm Hotel Link</h1>
</div>
<div class="container">
    <h1>Browse Fresh Vegetables</h1>
    
    <?php if ($orderPlaced): ?>
        <p id="success-message" class="success-message">Order placed successfully! Payment status: Pending. You can check the order details in the Place Order page.</p>
        <script>showSuccessMessage();</script>
    <?php endif; ?>
    
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="product">
                <img src="../uploads/<?php echo htmlspecialchars($row['VegetableImg']); ?>" alt="<?php echo htmlspecialchars($row['Name']); ?>">

                <div class="product-details">
                    <h2><?php echo htmlspecialchars($row['Name']); ?></h2>
                    <p><?php echo htmlspecialchars($row['Description']); ?></p>
                    
                    
                    <div class="quantity-control">
                        <label for="quantity-<?php echo $row['ProductID']; ?>">Quantity (kg):</label>
                        <input type="number" id="quantity-<?php echo $row['ProductID']; ?>" 
                               name="quantity" min="1" max="100" value="1" 
                               onchange="updateTotalCost(<?php echo $row['Price']; ?>, 'quantity-<?php echo $row['ProductID']; ?>', 'total-cost-<?php echo $row['ProductID']; ?>', <?php echo $row['ProductID']; ?>)">
                    </div>
                    
                    <p id="total-cost-<?php echo $row['ProductID']; ?>" class="total-cost">Total Cost: ₹<?php echo number_format($row['Price'], 2); ?></p>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="product_id" value="<?php echo $row['ProductID']; ?>">
                        <input type="hidden" id="quantity-input-<?php echo $row['ProductID']; ?>" name="quantity" value="1">
                        <input type="hidden" id="total-price-input-<?php echo $row['ProductID']; ?>" name="total_price" value="<?php echo $row['Price']; ?>">
                        
                        <button type="submit" name="place_order" class="order-btn">Place Order</button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No products available.</p>
    <?php endif; ?>
</div>
</body>
</html>