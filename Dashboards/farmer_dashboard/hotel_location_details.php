<?php
// Include database connection file
include('db_connection.php');

// Initialize variables to avoid undefined variable warnings
$hotel_details = null;
$location_details = null;
$hotel_owner_phone = null; // Variable to store hotel owner's phone number

// Check if the 'id' parameter is set in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $hotel_owner_id = $_GET['id'];

    // Query to fetch hotel details using HotelOwnerID (which also has UserID in the table)
    $hotel_query = "SELECT * FROM hotelowner WHERE HotelOwnerID = ?";
    $stmt = $conn->prepare($hotel_query);
    $stmt->bind_param("i", $hotel_owner_id);
    $stmt->execute();
    $hotel_result = $stmt->get_result();

    // Check if hotel details are found
    if ($hotel_result->num_rows > 0) {
        $hotel_details = $hotel_result->fetch_assoc();  // Assign result to variable
        
        // Fetch the UserID of the hotel owner
        $user_id = $hotel_details['UserID'];
        
        // Query to fetch hotel owner's phone number from the users table
        $phone_query = "SELECT Phone FROM users WHERE UserID = ?";
        $stmt = $conn->prepare($phone_query);
        $stmt->bind_param("i", $user_id); // Use UserID from the hotelowner table
        $stmt->execute();
        $phone_result = $stmt->get_result();

        // Check if phone number is found
        if ($phone_result->num_rows > 0) {
            $phone_data = $phone_result->fetch_assoc();
            $hotel_owner_phone = $phone_data['Phone']; // Assign phone number to variable
        }
    } else {
        echo "No hotel details found for HotelOwnerID: $hotel_owner_id<br>";
    }

    // Query to fetch location details using UserID (which corresponds to HotelOwnerID in the location table)
    $location_query = "SELECT * FROM location WHERE UserID = ?";
    $stmt = $conn->prepare($location_query);
    $stmt->bind_param("i", $hotel_owner_id);  // UserID in location corresponds to HotelOwnerID
    $stmt->execute();
    $location_result = $stmt->get_result();

    // Check if location details are found
    if ($location_result->num_rows > 0) {
        $location_details = $location_result->fetch_assoc();  // Assign result to variable
    }
} else {
    // If 'id' is not provided
    echo "HotelOwnerID is missing in the URL<br>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Location Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #F0FFF0;
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
    <img src="../images/farm_hotel_link_logo.png" alt="Logo">
    <h1>Farm Hotel Link</h1>
</header>

<div class="container">
    <?php if ($hotel_details && $location_details): ?>
        <h2 style="font-family: 'Poppins', sans-serif; text-decoration: underline;"><?php echo htmlspecialchars($hotel_details['HotelName']); ?> Location</h2>
        <table class="vertical-table">
            <tr>
                <th>Hotel Name :</th>
                <td><?php echo htmlspecialchars($hotel_details['HotelName']); ?></td>
            </tr>
            <tr>
                <th>Address :</th>
                <td><?php echo htmlspecialchars($hotel_details['HotelAddress']); ?></td>
            </tr>
            <tr>
                <th>Since Started :</th>
                <td><?php echo htmlspecialchars($hotel_details['SinceStartedYear']); ?></td>
            </tr>
            <tr>
                <th>Phone No :</th>
                <td><?php echo htmlspecialchars($hotel_owner_phone); ?></td>
            </tr>
            <tr>
                <th>Latitude :</th>
                <td><?php echo htmlspecialchars($location_details['Latitude']); ?></td>
            </tr>
            <tr>
                <th>Longitude :</th>
                <td><?php echo htmlspecialchars($location_details['Longitude']); ?></td>
            </tr>
            
        </table>
    <?php else: ?>
        <p>No details found for this hotel. Please check the hotel ID or try again later.</p>
    <?php endif; ?>
</div>

</body>
</html>
