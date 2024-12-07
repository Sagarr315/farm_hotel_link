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

$userID = $_SESSION['UserID'];

// Fetch HotelOwnerID from hotelowner table based on UserID
$query = "SELECT HotelOwnerID FROM hotelowner WHERE UserID = '$userID'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hotelOwnerID = $row['HotelOwnerID'];
} else {
    die("Hotel owner not found in the database.");
}

// Handle cancellation and delivery requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle "Cancel Order" request
    if (isset($_POST['cancelOrderID'])) {
        $orderID = $_POST['cancelOrderID'];
        $cancelSql = "UPDATE orders SET Status = 'Cancelled', CancelledBy = 'HotelOwner', CancellationTime = NOW() WHERE OrderID = '$orderID'";
        if ($conn->query($cancelSql) === TRUE) {
            echo "<p>Order $orderID has been successfully cancelled.</p>";
        } else {
            echo "<p>Error cancelling order: " . $conn->error . "</p>";
        }
    }

    // Handle "Mark as Delivered" request
    if (isset($_POST['deliveredOrderID'])) {
        $orderID = $_POST['deliveredOrderID'];
        
        // Update order status to 'Completed'
        $deliverSql = "UPDATE orders SET Status = 'Completed' WHERE OrderID = '$orderID'";
        if ($conn->query($deliverSql) === TRUE) {
            // Also update payment status to 'Paid' and set PaymentDate to current time
            $updatePaymentSql = "UPDATE payment SET Status = 'Paid', PaymentDate = NOW() WHERE OrderID = '$orderID'";
            if ($conn->query($updatePaymentSql) === TRUE) {
                echo "<p>Order $orderID has been marked as delivered, and payment status updated to 'Paid'.</p>";
            } else {
                echo "<p>Error updating payment status: " . $conn->error . "</p>";
            }
        } else {
            echo "<p>Error updating order status: " . $conn->error . "</p>";
        }
    }
}

// Fetch placed orders, order items, and payment details for the hotel owner
$orderQuery = "
    SELECT o.OrderID, o.OrderDate, oi.ProductID, p.Name AS ProductName, oi.Quantity, oi.Price, o.Status, o.CancelledBy, o.CancellationTime, pay.Amount, pay.PaymentDate, pay.Status AS PaymentStatus
    FROM orders o
    JOIN orderitems oi ON o.OrderID = oi.OrderID
    JOIN product p ON oi.ProductID = p.ProductID
    LEFT JOIN payment pay ON o.OrderID = pay.OrderID
    WHERE o.HotelOwnerID = '$hotelOwnerID'
    ORDER BY o.OrderDate DESC";

$orderResult = $conn->query($orderQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Placed Orders</title>
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
            background-color: #00796b;
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
<h1>Placed Orders</h1>
<div class="container">
    <h2>Your Order History</h2>
    
    <?php if ($orderResult->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Cancellation Info</th>
                    <th>Payment Info</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($orderRow = $orderResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($orderRow['OrderID']); ?></td>
                        <td><?php echo htmlspecialchars($orderRow['OrderDate']); ?></td>
                        <td><?php echo htmlspecialchars($orderRow['ProductName']); ?></td>
                        <td><?php echo htmlspecialchars($orderRow['Quantity']); ?> kg</td>
                        <td>₹<?php echo number_format($orderRow['Price'], 2); ?></td> <!-- Display stored TotalPrice -->
                        <td><?php echo $orderRow['Status']; ?></td>
                        <td>
                            <?php if ($orderRow['Status'] == 'Cancelled'): ?>
                                Cancelled by <?php echo htmlspecialchars($orderRow['CancelledBy']); ?> at <?php echo htmlspecialchars($orderRow['CancellationTime']); ?>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($orderRow['Amount'] != NULL): ?>
                                <a href="view_payment.php?orderID=<?php echo $orderRow['OrderID']; ?>">View Payment Details</a>
                            <?php else: ?>
                                Payment Pending
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($orderRow['Status'] == 'In Process'): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="deliveredOrderID" value="<?php echo $orderRow['OrderID']; ?>">
                                    <button type="submit">Mark as Delivered</button>
                                </form>
                                <form method="POST" action="">
                                    <input type="hidden" name="cancelOrderID" value="<?php echo $orderRow['OrderID']; ?>">
                                    <button type="submit">Cancel Order</button>
                                </form>
                            <?php elseif ($orderRow['Status'] == 'Delivered'): ?>
                                Delivered
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No orders placed yet.</p>
    <?php endif; ?>

    <?php $conn->close(); ?>
</div>

</body>
</html>
