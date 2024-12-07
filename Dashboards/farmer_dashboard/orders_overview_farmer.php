<?php
session_start();

// Check if the user is logged in and if the user is a farmer (using UserType)
if (!isset($_SESSION['UserID']) || $_SESSION['UserType'] != 'Farmer') {
    die("Please log in as a farmer to view your orders.");
}

// Database connection
$conn = new mysqli("localhost", "root", "", "farm_hotel_link");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the UserID from session and retrieve FarmerID based on UserID
$userID = $_SESSION['UserID'];
$sql = "SELECT FarmerID FROM farmers WHERE UserID = '$userID'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $farmerID = $row['FarmerID'];
} else {
    die("Error: Farmer not found in database.");
}

// Handle cancellation request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancelOrderID'])) {
    $orderID = $_POST['cancelOrderID'];
    $cancelSql = "UPDATE orders 
                  SET Status = 'Cancelled', CancelledBy = 'Farmer', CancellationTime = NOW() 
                  WHERE OrderID = '$orderID'";
    if ($conn->query($cancelSql) === TRUE) {
        echo "<p>Order $orderID has been successfully cancelled.</p>";
    } else {
        echo "<p>Error cancelling order: " . $conn->error . "</p>";
    }
}

// Fetch orders for farmer's products, including CancelledBy, CancellationTime, and payment details
$sql = "SELECT orders.OrderID, product.Name, orderitems.Quantity, orders.OrderDate, 
               hotelowner.HotelName, orders.Status, orders.CancelledBy, orders.CancellationTime, 
               hotelowner.HotelOwnerID
        FROM orders 
        JOIN orderitems ON orders.OrderID = orderitems.OrderID
        JOIN product ON orderitems.ProductID = product.ProductID 
        JOIN hotelowner ON orders.HotelOwnerID = hotelowner.HotelOwnerID 
        WHERE product.FarmerID = '$farmerID'
        ORDER BY orders.OrderDate DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Orders Page</title>
    <style>
        /* Styling and Animation */
        body {
            font-family: Arial, sans-serif;
            background: #F0FFF0; /* Keep the original background color */
            color: #333;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #228B22;
            color: white;
            padding: 15px;
            text-align: center;
            width: 100%;
            z-index: 1000;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        header img {
            width: 65px;
            height: 65px;
            margin-right: 0px;
        }
        header h1 {
            font-weight: bold;
            font-size: 45px;
            display: inline-block;
            margin-left: 0;
        }
        .container {
            width: 80%;
            background: #fff;
            padding: 20px;
            margin-top: 200px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            animation: fadeIn 1s ease-in-out;
            margin-left: auto;
            margin-right: auto;
        }
        h2 {
            color: Black;
            text-align: center;
            font-size:55px;
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
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        a {
            color: #0066cc;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<header>
    <img src="../images/farm_hotel_link_logo.png" alt="Website Logo">
    <h1>Farm Hotel Link</h1>
</header>

<div class="container">
    <h2>Your Order History</h2>
    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Order Date</th>
                    <th>Hotel</th>
                    <th>Status</th>
                    <th>Cancellation Info</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['OrderID']; ?></td>
                        <td><?php echo $row['Name']; ?></td>
                        <td><?php echo $row['Quantity']; ?> kg</td>
                        <td><?php echo $row['OrderDate']; ?></td>
                        <td>
                            <!-- Make HotelName clickable -->
                            <a href="hotel_location_details.php?id=<?php echo $row['HotelOwnerID']; ?>">
                                <?php echo $row['HotelName']; ?>
                            </a>
                        </td>
                        <td><?php echo $row['Status']; ?></td>
                        <td>
                            <?php 
                                if ($row['Status'] == 'Cancelled') {
                                    echo "Cancelled by " . $row['CancelledBy'] . " at " . $row['CancellationTime'];
                                }
                            ?>
                        </td>
                        <td>
                            <?php if ($row['Status'] == 'In Process'): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="cancelOrderID" value="<?php echo $row['OrderID']; ?>">
                                    <button type="submit">Cancel</button>
                                </form>
                            <?php endif; ?>
                            <a href="view_payment.php?OrderID=<?php echo $row['OrderID']; ?>" class="btn btn-info">View Payment Details</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No orders have been placed for your products yet.</p>
    <?php endif; ?>
</div>

</body>
</html>

<?php $conn->close(); ?>
