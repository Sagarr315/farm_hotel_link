<?php
// Include database connection
include('db_connection.php'); // Ensure this file contains your database connection code
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Special Dishes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #F0FFF0;
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

        /* Adding margin to create space between header and content */
        h1.special-dishes {
            margin-top: 150px; /* Space between the fixed header and the content */
            text-align: center;
            font-family: Cooper Black;
            text-decoration:underline;
            font-size:55px;
        }

        .dish-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            opacity: 0;
            animation: fadeInUp 2s ease-out forwards;
            margin-top: 20px; /* Space between the heading and the container */
        }

        @keyframes fadeInUp {
            from {
                transform: translateY(50px); /* Start from 50px below */
                opacity: 0;
            }
            to {
                transform: translateY(0); /* End at original position */
                opacity: 1;
            }
        }

        .dish-card {
            width: 300px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .dish-card:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        .dish-photo {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .dish-details {
            padding: 15px;
        }
        .dish-name {
            font-size: 25px;
            font-weight: bold;
            color: #333;
            font-family: Cooper Black;
            margin-bottom: 10px;
        }
        .hotel-name {
            font-size: 16px;
            color: black;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .dish-rating {
            font-size: 16px;
            color: black;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .dish-price {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .dish-vegetables {
            font-size: 20px;
            color: black; /* Darker color for the label */
            text-align: center;
            font-weight: bold;
            
        }
        .dish-vegetables span {
            font-size: 16px;
            color: #333; 
        }
        </style>
</head>
<body>

   <header>
      <img src="../images/farm_hotel_link_logo.png" alt="Website Logo">
      <h1>Farm Hotel Link</h1>
   </header>

    <h1 class="special-dishes">Special Dishes</h1>
    
    <div class="dish-container">
        <?php
        // Fetch data from the specialdish and hotelowner tables
        $query = "
            SELECT 
                sd.DishName, 
                sd.VegetableRequirements, 
                sd.DishPhoto, 
                sd.DishPrice, 
                sd.DishRating, 
                ho.HotelName
            FROM 
                specialdish sd
            INNER JOIN 
                hotelowner ho
            ON 
                sd.HotelOwnerID = ho.HotelOwnerID
        ";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            // Loop through each dish and display it
            while ($row = mysqli_fetch_assoc($result)) {
                // Check if DishPhoto is available and set the path accordingly
                $dishPhotoPath = !empty($row['DishPhoto']) ? '../uploads/' . $row['DishPhoto'] : '';
                
                // If there's no specific image, the image element will not be rendered at all
                if (empty($dishPhotoPath)) {
                    echo '
                    <div class="dish-card">
                        <div class="dish-details">
                            <div class="dish-name">' . htmlspecialchars($row['DishName']) . '</div>
                            <div class="hotel-name">Hotel: ' . htmlspecialchars($row['HotelName']) . '</div>
                            <div class="dish-rating">Rating: ' . str_repeat('⭐', intval($row['DishRating'])) . '</div>
                            <div class="dish-price">Price: ₹' . htmlspecialchars($row['DishPrice']) . '</div>
                            <hr> 
                            <div class="dish-vegetables"><u>Vegetables Required </u><span>' . htmlspecialchars($row['VegetableRequirements']) . '</span></div>
                        </div>
                    </div>
                    ';
                } else {
                    echo '
                    <div class="dish-card">
                        <img src="' . $dishPhotoPath . '" alt="' . $row['DishName'] . '" class="dish-photo">
                        <div class="dish-details">
                            <div class="dish-name">' . htmlspecialchars($row['DishName']) . '</div>
                            <div class="hotel-name">Hotel: ' . htmlspecialchars($row['HotelName']) . '</div>
                            <div class="dish-rating">Rating: ' . str_repeat('⭐', intval($row['DishRating'])) . '</div>
                            <div class="dish-price">Price: ₹' . htmlspecialchars($row['DishPrice']) . '</div>
                            <hr> 
                            <div class="dish-vegetables"><u>Vegetables Required </u><span>' . htmlspecialchars($row['VegetableRequirements']) . '</span></div>
                        </div>
                    </div>
                    ';
                }
            }
        } else {
            echo '<p>No special dishes available.</p>';
        }
        ?>
    </div>
</body>
</html>