<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "farm_hotel_link");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

// Ensure UserID and UserType are set in session and are valid
if (!isset($_SESSION['UserID']) || !isset($_SESSION['UserType'])) {
    die("User is not logged in.");
}

$userID = $_SESSION['UserID'];
$userType = $_SESSION['UserType'];

// Initialize query variables
$orderQuery = "";
$title = "";

// Fetch payment details based on user type
if ($userType === "HotelOwner") {
    // Fetch HotelOwnerID
    $query = "SELECT HotelOwnerID FROM hotelowner WHERE UserID = '$userID'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hotelOwnerID = $row['HotelOwnerID'];
    } else {
        die("Hotel owner not found in the database.");
    }

    $orderQuery = "
        SELECT o.OrderID, o.OrderDate, oi.ProductID, p.Name AS ProductName, oi.Quantity, oi.Price, o.Status, 
               payment.Amount AS PaymentAmount, payment.PaymentDate, payment.Status AS PaymentStatus
        FROM orders o
        JOIN orderitems oi ON o.OrderID = oi.OrderID
        JOIN product p ON oi.ProductID = p.ProductID
        LEFT JOIN payment ON o.OrderID = payment.OrderID
        WHERE o.HotelOwnerID = '$hotelOwnerID'
        ORDER BY o.OrderDate DESC";
    $title = "Your Payment History (Hotel Owner)";
} elseif ($userType === "Farmer") {
    // Fetch FarmerID
    $query = "SELECT FarmerID FROM farmers WHERE UserID = '$userID'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $farmerID = $row['FarmerID'];
    } else {
        die("Farmer not found in the database.");
    }

    $orderQuery = "
        SELECT o.OrderID, o.OrderDate, oi.ProductID, p.Name AS ProductName, oi.Quantity, oi.Price, o.Status, 
               payment.Amount AS PaymentAmount, payment.PaymentDate, payment.Status AS PaymentStatus
        FROM orders o
        JOIN orderitems oi ON o.OrderID = oi.OrderID
        JOIN product p ON oi.ProductID = p.ProductID
        LEFT JOIN payment ON o.OrderID = payment.OrderID
        WHERE p.FarmerID = '$farmerID'
        ORDER BY o.OrderDate DESC";
    $title = "Your Payment History (Farmer)";
} else {
    die("Invalid user type.");
}

$orderResult = $conn->query($orderQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Payment Details</title>
    <style>
        /* Styling and Animation */
        body {
            font-family: Arial, sans-serif;
            background: #e0f7fa;
            color: #333;
            padding: 0;
            margin: 0;
        }
        .header {
            background-color: #333;
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
            background: #fff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            animation: slideInUp 1s ease-in-out;
            margin-top: 150px;
            padding: 20px;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
        h2 {
            color: Black;
            text-align: center;
            font-size: 55px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #333;
            color: white;
        }
        tr:hover {
            background-color: #e0f2f1;
        }
        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="header">
    <img src="../images/farm_hotel_link_logo.png" alt="Website Logo" style="width: 65px; height:65px;">
    <h1>Farm Hotel Link</h1>
</div>

<h1>View Payment Details</h1>
<div class="container">
    <h2><?php echo $title; ?></h2>
    
    <?php if ($orderResult->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Payment Status</th>
                    <th>Payment Date</th>
                    <th>Payment Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($orderRow = $orderResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($orderRow['OrderID']); ?></td>
                        <td><?php echo htmlspecialchars($orderRow['OrderDate']); ?></td>
                        <td><?php echo htmlspecialchars($orderRow['ProductName']); ?></td>
                        <td><?php echo htmlspecialchars($orderRow['Quantity']); ?> kg</td>
                        <td>₹<?php echo number_format($orderRow['Price'], 2); ?></td>
                        <td><?php echo $orderRow['PaymentStatus'] ? $orderRow['PaymentStatus'] : "Pending"; ?></td>
                        <td><?php echo $orderRow['PaymentDate'] ? $orderRow['PaymentDate'] : "N/A"; ?></td>
                        <td>₹<?php echo $orderRow['PaymentAmount'] ? number_format($orderRow['PaymentAmount'], 2) : "Not Paid"; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No payments have been made for your orders yet.</p>
    <?php endif; ?>

    <?php $conn->close(); ?>
</div>

</body>
</html>
