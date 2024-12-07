<?php
// Include the database connection
include('db_connection.php');  // Adjust the path as needed

if (isset($_POST['submit'])) {
    // Sanitize the input to prevent SQL injection
    $search = mysqli_real_escape_string($conn, $_POST['search']);
    
    // SQL query to search in the product table by Name
    $sql = "SELECT * FROM product WHERE Name LIKE '%$search%'";

    // Execute the query
    $result = mysqli_query($conn, $sql);

    // Check if any products were found
    if (mysqli_num_rows($result) > 0) {
        echo "<h2>Search Results</h2>";
        echo "<table class='result-table'>
                <tr><th>Product Name</th><th>Description</th><th>Price</th></tr>";
        
        // Display the products
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr><td>" . $row['Name'] . "</td><td>" . $row['Description'] . "</td><td>" . $row['Price'] . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='no-results'>No products found matching your search.</div>";
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
        background-color: #2C3E50;
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
