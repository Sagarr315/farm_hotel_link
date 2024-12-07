<?php
session_start();

// Check if the user is logged in and is a Farmer
if (!isset($_SESSION['UserID']) || $_SESSION['UserType'] != 'Farmer') {
    header("Location: login.html"); // Redirect to login page if not authenticated
    exit();
}

// Optionally, add a check for 'Name' to ensure it’s set
if (!isset($_SESSION['Name'])) {
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard</title>

    <!-- Inline CSS -->
    <style>
        html, body {
            margin: 0;
            margin-bottom:0px;
            font-family: Arial, sans-serif;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow-x: hidden;
        }

        /* Header Styles */
        header {
            display: flex;
            flex-direction: column;
            padding: 0;
            background-color: #228B22;
            color: white;
            height: 150px;
            position: sticky;
            top: 0;
            z-index: 1000;
            width: 100%;
            box-sizing: border-box;
        }

        .header-top {
    display: flex;
    align-items: center;
    padding: 8px;
    justify-content: space-between; /* Ensure space between elements */
}

.header-top .logo {
    margin-right: 10px; /* Adds some space between the logo and the name */
}

.search-container {
    display: flex;
    justify-content: space-between; /* Ensures search bar and button are aligned properly */
    width: 60%; /* Adjust width of the search container */
    margin-left: 160px; /* Adds space between the name and search bar */
}

.search-container input[type="text"] {
    flex-grow: 1; /* Allows the input to take up available space */
    padding: 15px; /* Increased padding for a larger input field */
    border: none;
    border-radius: 4px;
    margin-right: 10px; /* Space between the input and button */
    font-size: 16px; /* Adjust font size */
}

.search-container button {
    width: auto; /* Makes the button size fit content */
    padding: 15px 25px; /* Increased padding for a larger button */
    border: 1px solid #ccc;
    background-color: white;
    border-radius: 4px;
    font-size: 16px; /* Adjust font size */
    cursor: pointer;
}

.search-container button:hover {
    background-color: #f0f0f0; /* Hover effect */
}



        .menu {
            display: flex;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .menu-item {
            margin: 5px;
            border-radius: 4px;
            padding: 10px;
            color: black;
            text-align: center;
            font-weight: bold;
            background-color: #F0FFF0;
        }

        .menu-item a {
            color: black;
            text-decoration: none;
            display: block;
        }

        .menu-item a:hover {
            background-color: #555;
        }

        .user-profile {
            margin-left: auto;
            font-size: 16px;
        }

        /* Content Container */
        .content-container {
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
            overflow: hidden;
            padding: 20px;
        }

        .text-container {
            margin-left: 20px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        /* Welcome Text */
        #welcome-text {
            top:0;
            font-size: 58px;
            color: black;
            font-weight: bold;
            font-family: Cooper Black;
            white-space: nowrap;
            overflow: hidden;
            width: 0;
            animation: typing 4s steps(30) 2s forwards, blink 0.75s step-end infinite;
            margin-bottom: 20px;
        }

        /* Extra Info */
        #extra-info {
            font-size: 29px;
            
            color: #555;
            opacity: 0;
            animation: fadeInText 3s forwards 6s;
            width: 100%;
        }

        /* Left Image */
        .left-image {
            width: 50%;
            opacity: 0;
            animation: fadeIn 2s forwards;
        }

        /* Animations */
        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }

        @keyframes fadeInText {
            to {
                opacity: 1;
            }
        }

        @keyframes typing {
            to {
                width: 100%;
            }
        }

        @keyframes blink {
            50% {
                border-color: transparent;
            }
        }

/* Footer Styling */
footer {
    background: linear-gradient(90deg, #2c3e50, #34495e); /* Gradient for modern look */
    color: #ecf0f1; /* Light text color */
    padding: 40px 20px; /* Spacious padding for content */
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap; /* Ensures responsive design */
    gap: 20px; /* Space between sections */
    box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.2); /* Subtle shadow for depth */
    border-top: 3px solid white; /* Accent border for style */
}

.footer-section {
    flex: 1;
    min-width: 200px; /* Ensures proper stacking on smaller screens */
}

.footer-section h3 {
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 10px;
    text-transform: uppercase;
    border-bottom: 2px solid black; /* Change the border color to white */

    padding-bottom: 5px;
}

.footer-section ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-section ul li {
    margin: 10px 0; /* Spacing between list items */
}

.footer-section ul li a {
    color: #ecf0f1;
    text-decoration: none;
    font-size: 16px;
    transition: color 0.3s ease;
}

.footer-section ul li a:hover {
    color: #1abc9c; /* Highlight color for links on hover */
}

.footer-section .social-icons a {
    display: inline-flex;
    align-items: center;
    margin-right: 10px;
    color: #ecf0f1;
    text-decoration: none;
    transition: transform 0.3s ease, color 0.3s ease;
}

.footer-section .social-icons a img {
    width: 30px; /* Size for social media icons */
    height: 30px;
    margin-right: 8px;
}

.footer-section .social-icons a:hover {
    color: #1abc9c;
    transform: scale(1.1); /* Slight zoom effect */
}

.footer-logo img {
    max-height: 100px; /* Website logo size */
    width: 100px;
}

.footer-bottom {
    text-align: center;
    font-size: 18px;
    color: #bdc3c7;
    width: 100%;
    position: relative; /* Positioned at the bottom */
    left: 0;
    margin: 0; /* Removes margin around the footer */
    padding: 0; /* Removes padding around the footer */
}

    </style>
</head>
<body>
    <!-- Header Section -->
    <header>
        <div class="header-top">
            <img src="../images/farm_hotel_link_logo.png" alt="Farm Hotel Link Logo" class="logo" style="width: 65px;">
            <strong style="font-size: 45px; color: white;">Farm Hotel Link</strong>
            
            <form method="POST" action="search_results.php">
               <div class="search-container" style="display:center;">
               <input type="text" name="search" placeholder="Search Products..." value="<?php if (isset($_POST['search'])) { echo $_POST['search']; } ?>"> <!-- Search Bar -->
                    <button type="submit" name="submit">Search</button> <!-- Submit Button -->
            </div>
            </form>

            <div class="user-profile">
                <span>Welcome, <?php echo $_SESSION['Name']; ?> (Farmer)</span>
            </div>
        </div>

        <div class="menu">
            <div class="menu-item"><a href="farmer_dashboard.php">Home</a></div>
            <div class="menu-item"><a href="farmer_profile.php">Profile</a></div>
            <div class="menu-item"><a href="Manage_Products.php">Manage Products</a></div>
            <div class="menu-item"><a href="hotel_special_details.php">Hotel & Special Dishes</a></div>
            <div class="menu-item"><a href="orders_overview_farmer.php">Orders</a></div>
            <div class="menu-item"><a href="manage_order.php">Manage Order</a></div>
            <div class="menu-item"><a href="logout.php">Logout</a></div>
        </div>
    </header>

    <!-- Main Content Section -->
    <div class="content-container">
        <img src="../images/farmer_and_hotelowner.webp" alt="Farmer and Hotel Partnership" class="left-image">
        <div class="text-container">
            <p id="welcome-text">Welcome to the<br> Farmer Dashboard</p>
            <p id="extra-info">
                Farmers are at the heart of agriculture, playing an essential role in ensuring the availability of fresh produce and maintaining a healthy food supply chain. 
                At Farm Hotel Link, we recognize the hard work and dedication of farmers, which is why this platform is designed to empower them by bridging the gap between farms and hotels.
            </p>
            <p id="extra-info">
                Through Farm Hotel Link, farmers gain direct access to hotel owners who are looking for fresh, high-quality produce. 
                By bypassing intermediaries, farmers can ensure fair pricing, create sustainable partnerships, and reduce waste in the supply chain.
            </p>       
            </div>
    </div>

    <!-- How the working of the farmer is mention -->
    <div style="width: 100%; background-color: #f0f0f0; padding: 20px; box-sizing: border-box;">
    <h2 style="width: 100%; font-size: 30px; color: #228B22; text-align: center; font-weight: bold;">How Farm Hotel Link Works for Farmers</h2>
    <ol style="width: 100%; font-size: 18px; color: #555; line-height: 1.8;">
        <li><strong>Profile Creation:</strong> Farmers can create a dedicated profile showcasing their farm's unique offerings, including crops grown, farming practices, and certifications.</li>
        <li><strong>Direct Connections:</strong> The platform connects farmers directly with hotel owners, eliminating middlemen and ensuring fair trade.</li>
        <li><strong>Order Management:</strong> Farmers can manage their orders, update order statuses, and even communicate directly with hotel owners if needed.</li>
        <li><strong>Support and Insights:</strong> Farm Hotel Link provides resources and tools to help farmers make informed decisions, optimize their offerings, and enhance profitability.</li>
    </ol>
</div>
<br>


<!-- Unique Features for Farmers on Farm Hotel Link -->
<div style="width: 100%; padding: 20px; background-color: #f0f0f0; font-family: Arial, sans-serif; line-height: 1.6;">
    <h2 style="color: #228B22; text-align: center; font-size: 30px;">Unique Features for Farmers on Farm Hotel Link</h2>
    <ul style="margin: 0 auto; padding: 0 20px; max-width: 80%; font-size: 18px; color: #333;">
        <li style="margin-bottom: 10px;">
            <strong>Manage Products:</strong> Farmers can easily upload details about their crops, update availability, and set competitive prices. This allows hotels to view and select the products they need directly from the farm.
        </li>
        <li style="margin-bottom: 10px;">
            <strong>Order Tracking:</strong> The platform provides a seamless order tracking system. Farmers can stay informed about the status of their orders, manage shipping schedules, and receive real-time updates.
        </li>
        <li style="margin-bottom: 10px;">
            <strong>Hotel Feedback:</strong> Farmers can view ratings and reviews from hotel owners, fostering transparency and helping build trust between both parties.
        </li>
        <li style="margin-bottom: 10px;">
            <strong>Location Services:</strong> Farmers can share the exact location of their farms, making it easier for hotel owners to plan logistics and ensure timely deliveries.
        </li>
        <li style="margin-bottom: 10px;">
            <strong>Partnership Opportunities:</strong> Beyond selling products, the platform encourages long-term collaborations with hotel owners, promoting a steady income stream and mutual growth.
        </li>
    </ul>
</div>
<br>

     
       <!-- Benefits for Farmers -->
<div style="width: 100%; padding: 20px; background-color: #f0f0f0; font-family: Arial, sans-serif; line-height: 1.6;">
    <h2 style="color: #228B22; text-align: center; font-size: 30px;">Benefits for Farmers</h2>
    <ul style="margin: 0 auto; padding: 0 20px; max-width: 80%; font-size: 18px; color: #333;">
        <li style="margin-bottom: 10px;">
            <strong>Direct Access to Buyers:</strong> Farmers can connect with hotel owners to sell their produce directly.
        </li>
        <li style="margin-bottom: 10px;">
            <strong>Fair Pricing:</strong> Eliminates middlemen, ensuring better profits for their crops.
        </li>
        <li style="margin-bottom: 10px;">
            <strong>Efficient Order Management:</strong> Manage orders seamlessly and track order status in real-time.
        </li>
        <li style="margin-bottom: 10px;">
            <strong>Promote Sustainable Practices:</strong> Highlight organic and eco-friendly farming techniques to attract buyers.
        </li>
        <li>
            <strong>Grow Customer Base:</strong> Expand business opportunities by connecting with multiple hotel owners.
        </li>
    </ul>
</div>
<br>


    <!-- why farmer choose farm hotel link is -->
     <div style="width: 100%; padding: 20px; background-color: #f0f0f0; text-align: center; font-size: 18px; font-family: Arial, sans-serif;">
         <h2 style="color: #228B22; font-size: 30px;">Why Farmers Choose Farm Hotel Link</h2>
            <p style="max-width: 80%; margin: 0 auto; line-height: 1.6;">
                Farm Hotel Link isn't just a marketplace—it's a community where farmers are valued for their contributions. By leveraging modern technology, the platform empowers farmers to showcase their hard work and reach customers who value fresh, sustainable, and locally sourced produce.
            </p>
     </div>

     <footer>
    <!-- Left Section: Social Media Links -->
    <div class="footer-section">
        <h3>Follow Us</h3>
        <div class="social-icons">
            <a href="https://www.instagram.com" target="_blank">
                <img src="../Images/instagram_logo.jpeg" alt="Instagram"> Instagram
            </a><br>
            <a href="https://www.facebook.com" target="_blank">
                <img src="../Images/facebook_logo.jpeg" alt="Facebook"> Facebook
            </a><br>
            <a href="https://www.twitter.com" target="_blank">
                <img src="../images/twitter-x-logo.png" alt="Twitter"> Twitter
            </a><br>
            <a href="https://www.linkedin.com" target="_blank">
                <img src="../images/linkedin_logo.jpeg" alt="LinkedIn"> LinkedIn
            </a>
        </div>
    </div>

    <!-- Center Section: Website Links -->
    <div class="footer-section">
        <h3>Quick Links</h3>
        <ul>
            <li><a href="../../Footer_pages/about_us.html">About Us</a></li>
            <li><a href="../../Footer_pages/privacy_policy.html">Privacy Policy</a></li>
            <li><a href="../../Footer_pages/terms_and_conditions.html">Terms & Conditions</a></li>
            <li><a href="../../Footer_pages/contact_us.html">Contact Us</a></li>
        </ul>
    </div>

    <!-- Right Section: Website Logo -->
    <div class="footer-section footer-logo">
        <h3>Our Logo</h3>
        <img src="../images/farm_hotel_link_logo.png" alt="Website Logo">
    </div>

    <!-- Bottom Section -->
    <p class="footer-bottom">
        © 2024 Farm Hotel Link. All rights reserved. Designed with ❤️ by Sagar Bhor.
</p>
</footer>



    <!-- Inline JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            console.log('Farmer Dashboard loaded');
        });


        function search() {
        var query = document.querySelector('.search-container input[type="text"]').value;
        if (query) {
            // Redirect to a search results page with the query as a URL parameter
            window.location.href = 'search_results.php?query=' + encodeURIComponent(query);
        } else {
            alert('Please enter a search query.');
        }
    }

    </script>
</body>
</html>
