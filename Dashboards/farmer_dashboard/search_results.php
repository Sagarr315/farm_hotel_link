<?php
// Include the database connection
include('db_connection.php');  // Adjust the path as needed

if (isset($_POST['submit'])) {
    // Sanitize the input to prevent SQL injection
    $search = mysqli_real_escape_string($conn, $_POST['search']);
    
    // SQL query to search in the hotelowner table by Name (hotel names)
    $hotelQuery = "SELECT HotelName, HotelAddress FROM hotelowner WHERE HotelName LIKE '%$search%'";
    $hotelResult = mysqli_query($conn, $hotelQuery);

    // SQL query to search in the specialdish table by DishName (dish names)
    $dishQuery = "SELECT DishName, VegetableRequirements FROM specialdish WHERE DishName LIKE '%$search%'";
    $dishResult = mysqli_query($conn, $dishQuery);

    // Check if any results were found in the hotelowner table
    if (mysqli_num_rows($hotelResult) > 0) {
        echo "<h2>Hotel Owners</h2>";
        echo "<table class='result-table'>
                <tr><th>Hotel Name</th><th>Hotel Address</th></tr>";
        
        // Display the hotel names and addresses
        while ($row = mysqli_fetch_assoc($hotelResult)) {
            echo "<tr><td>" . $row['HotelName'] . "</td><td>" . $row['HotelAddress'] . "</td></tr>";
        }
        echo "</table>";
    }

    // Check if any results were found in the specialdish table
    if (mysqli_num_rows($dishResult) > 0) {
        echo "<h2>Special Dishes</h2>";
        echo "<table class='result-table'>
                <tr><th>Dish Name</th><th>Vegetable Requirement</th></tr>";
        
        // Display the special dishes
        while ($row = mysqli_fetch_assoc($dishResult)) {
            echo "<tr><td>" . $row['DishName'] . "</td><td>" . $row['VegetableRequirements'] . "</td></tr>";
        }
        echo "</table>";
    }

    // If no results are found in either table
    if (mysqli_num_rows($hotelResult) == 0 && mysqli_num_rows($dishResult) == 0) {
        echo "<div class='no-results'>No results found matching your search.</div>";
    }
}
?>

<!-- Add some custom CSS to style the results -->
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }
    
    h2 {
        text-align: center;
        color: #333;
        margin-top: 20px;
    }

    .result-table {
        width: 80%;
        margin: 20px auto;
        border-collapse: collapse;
        background-color: #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .result-table th, .result-table td {
        padding: 12px;
        text-align: left;
        border: 1px solid #ddd;
    }

    .result-table th {
        background-color: #4CAF50;
        color: white;
    }

    .result-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .result-table tr:hover {
        background-color: #f1f1f1;
    }

    .no-results {
        text-align: center;
        color: #e74c3c;
        font-size: 1.2em;
        margin-top: 20px;
    }
</style>
