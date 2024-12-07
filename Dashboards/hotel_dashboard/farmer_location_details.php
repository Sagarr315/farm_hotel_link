<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "farm_hotel_link");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

// Ensure that OrderID and FarmerID are passed in the URL
if (!isset($_GET['OrderID']) || !isset($_GET['FarmerID'])) {
    die("Invalid request. OrderID and FarmerID are required.");
}

$orderID = $_GET['OrderID'];
$farmerID = $_GET['FarmerID'];

// Fetch location data for the specific farmer from the location table (using UserID)
$locationQuery = "SELECT Latitude, Longitude FROM location WHERE UserID = '$farmerID'";
$locationResult = $conn->query($locationQuery);
if ($locationResult->num_rows > 0) {
    $locationRow = $locationResult->fetch_assoc();
    $latitude = $locationRow['Latitude'];
    $longitude = $locationRow['Longitude'];
} else {
    die("Location details for this farmer are not available.");
}

// Fetch farmer details from the farmers table
$farmerQuery = "SELECT CropsGrown, FarmAddress, PreferredContactNumber FROM farmers WHERE FarmerID = '$farmerID'";
$farmerResult = $conn->query($farmerQuery);
if ($farmerResult->num_rows > 0) {
    $farmerRow = $farmerResult->fetch_assoc();
    $cropsGrown = $farmerRow['CropsGrown'];
    $farmAddress = $farmerRow['FarmAddress'];
    $preferredContactNumber = $farmerRow['PreferredContactNumber'];
} else {
    die("Farmer details not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Location Details</title>
    <style>
       body {
            font-family: Arial, sans-serif;
            background: #F0FFF0; /* Keep the original background color */
            color: #333;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #2C3E50;
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
            width: 60%;
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
            font-size: 55px;
        }

        /* Table Styles */
        .vertical-table {
            width: 50%;
            margin: auto;
           
        }

        .vertical-table td {
            text-align: left;
            padding: 10px 20px;
        }

        .vertical-table th, .vertical-table td {
            padding: 10px;
        }

        .vertical-table th {
            text-align: right;
            width: 30%;
            font-weight: bold;
        }

        /* Styling for row hover effect */
        tr:hover {
            background-color: #f1f1f1;
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
    <h2 style="font-family: 'Poppins', sans-serif; text-decoration: underline;">Farmer Location Details</h2>
    <table class="vertical-table">
        <tr>
            <th>Crops Grown:</th>
            <td><?php echo htmlspecialchars($cropsGrown); ?></td>
        </tr>
        <tr>
            <th>Farm Address:</th>
            <td><?php echo htmlspecialchars($farmAddress); ?></td>
        </tr>
        <tr>
            <th>Preferred Contact Number:</th>
            <td><?php echo htmlspecialchars($preferredContactNumber); ?></td>
        </tr>
        <tr>
            <th>Latitude:</th>
            <td><?php echo htmlspecialchars($latitude); ?></td>
        </tr>
        <tr>
            <th>Longitude:</th>
            <td><?php echo htmlspecialchars($longitude); ?></td>
        </tr>
    </table>
</div>


</body>
</html>

<?php $conn->close(); ?>
