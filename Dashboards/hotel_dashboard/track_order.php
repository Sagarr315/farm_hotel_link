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

// Fetch tracking updates from ordertrackingupdates table for the hotel owner
$orderQuery = "
    SELECT otu.OrderID, otu.StatusUpdate, otu.ShippedDateTime, otu.UpdateMessage, otu.UpdatedBy, o.FarmerID
    FROM ordertrackingupdates otu
    JOIN orders o ON otu.OrderID = o.OrderID
    WHERE o.HotelOwnerID = '$hotelOwnerID'
    ORDER BY otu.ShippedDateTime DESC";

$orderResult = $conn->query($orderQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Orders History</title>
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
<h1>Track Orders History</h1>
<div class="container">
    <h2>Track Orders History</h2>
    
    <?php if ($orderResult->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Status Update</th>
                    <th>Shipped Date & Time</th>
                    <th>Update Message</th>
                    <th>Updated By</th>
                    <th>Location</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($orderRow = $orderResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($orderRow['OrderID']); ?></td>
                        <td><?php echo htmlspecialchars($orderRow['StatusUpdate']); ?></td>
                        <td><?php echo htmlspecialchars($orderRow['ShippedDateTime']); ?></td>
                        <td><?php echo htmlspecialchars($orderRow['UpdateMessage']); ?></td>
                        <td><?php echo htmlspecialchars($orderRow['UpdatedBy']); ?></td>
                        <td>
                            <a href="farmer_location_details.php?OrderID=<?php echo urlencode($orderRow['OrderID']); ?>&FarmerID=<?php echo urlencode($orderRow['FarmerID']); ?>">
                                View Location
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No tracking updates available for your orders yet.</p>
    <?php endif; ?>

    <?php $conn->close(); ?>
</div>

</body>
</html>
