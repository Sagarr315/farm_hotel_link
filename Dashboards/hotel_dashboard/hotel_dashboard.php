<?php
session_start();

// Check if the user is logged in and is a Hotel Owner
if (!isset($_SESSION['UserID']) || $_SESSION['UserType'] != 'HotelOwner') {
    header("Location: login.html"); // Redirect to login page if not authenticated
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Owner Dashboard</title>

    <!-- Inline CSS -->
    <style>
        /* General Styles */
        /* General Styles */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    overflow-x: hidden; /* Prevent horizontal scrolling */
}

/* Header Styles */
header {
    display: flex;
    flex-direction: column; /* Stack items vertically */
    padding: 10px 20px;
    background-color: #2C3E50;
    color: white;
    top: 0;
    height:150px;
    width: 100%; /* Ensure header spans the full width */
    position: relative;
    box-sizing: border-box; /* Include padding in width calculations */
}

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .search-container {
    display: flex;
    justify-content: space-between; /* Ensures search bar and button are aligned properly */
    width: 100%; /* Increased from 60% to give more space */
    margin: 0 auto; /* Centers the container */
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




        /* Menu Styles */
        .menu {
            display: flex; /* Change to horizontal layout */
            flex-wrap: wrap; /* Allow wrapping to the next line if necessary */
            margin-top: 10px;
        }

        .menu-item {
            margin: 5px; /* Add margin between menu items */
            border-radius: 4px; /* Rounded corners for menu items */
            padding: 10px; /* Add padding */
            color: white; /* Text color */
            text-align: center; /* Center text */
            background-color: #2980b9; /* Same background color for all menu items */
        }

        .menu-item a {
            color: white;
            text-decoration: none;
            display: block; /* Make the entire area clickable */
        }

        .user-profile {
            display: flex;
            align-items: center;
        }

        /* Main Section Styles */
        main {
            padding: 20px;
        }

        h1 {
            color: #2C3E50; /* Changed header color */
            font-size: 28px;
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
            font-size: 25px;
            
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
            <img src="../images/farm_hotel_link_logo.png" alt="Farm Hotel Link Logo" class="logo" style="width: 65px;"> <!-- Add the logo here -->
            <strong style="font-size: 45px; color: white;margin-left: -155px; ">Farm Hotel Link</strong> <!-- Website Name -->
            
            <form method="POST" action="search_results.php">
    <div class="search-container">
        <input type="text" name="search" placeholder="Search Products..." value="<?php if (isset($_POST['search'])) { echo $_POST['search']; } ?>"> <!-- Search Bar -->
        <button type="submit" name="submit">Search</button> <!-- Submit Button -->
    </div>
</form>



            <div class="user-profile">
                <span>Welcome, <?php echo $_SESSION['Name']; ?> (Hotel Owner)</span> <!-- PHP code to display the logged-in hotel owner's name -->
            </div>
        </div>

        <!-- Menu Section -->
        <div class="menu">
            <div class="menu-item"><a href="hotel_dashboard.php">Home</a></div>
            <div class="menu-item"><a href="hotel_profile.php">Hotel Profile</a></div>
            <div class="menu-item"><a href="browse_products.php">Browse Products</a></div>
            <div class="menu-item"><a href="place_order_by_hotel.php">Place Orders</a></div>
            <div class="menu-item"><a href="manage_special_dishes.php">Manage Special Dishes</a></div>
            <div class="menu-item"><a href="track_order.php">Track Orders</a></div>
            <div class="menu-item"><a href="logout.php">Logout</a></div>
        </div>
    </header>

    
     <!-- Main Content Section -->
     <div class="content-container">
        <img src="../images/farmer_and_hotelowner.webp" alt="Farmer and Hotel Partnership" class="left-image">
        <div class="text-container">
            <p id="welcome-text">Welcome to the<br> Hotel Dashboard</p>
            <p id="extra-info">
            Hotels are at the heart of the hospitality industry, playing a crucial role in providing exceptional experiences to guests and ensuring high-quality service. 
            At Farm Hotel Link, we recognize the efforts of hotel owners, which is why this platform is designed to empower them by connecting directly with farmers.
            </p>
            <p id="extra-info">
                Through Farm Hotel Link, hotel owners gain direct access to fresh, high-quality produce while eliminating intermediaries. This ensures fair pricing, supports sustainable sourcing,
             and enhances food quality. Additionally, the platform serves as a marketing tool for hotels, helping them showcase their commitment to fresh and locally sourced ingredients,
              ultimately attracting more guests and building a strong reputation
            </p>       
        </div>
     </div>

      <!-- How the working of the hotel owner is mention -->
    <div style="width: 100%; background-color: #f0f0f0; padding: 20px; box-sizing: border-box;">
        <h2 style="width: 100%; font-size: 30px; color: #2c3e50; text-align: center; font-weight: bold;">How Farm Hotel Link Works for Hotel Owners</h2>
        <ol style="width: 100%; font-size: 18px; color: #555; line-height: 1.8;">
             <li><strong>Profile Creation:</strong> Hotel owners can create a dedicated profile to manage their purchases and highlight their hotel’s specialties.</li>
             <li><strong>Direct Sourcing:</strong> The platform allows hotel owners to bypass traditional supply chains and connect directly with farmers, ensuring fresh and affordable produce.</li>
             <li><strong>Marketing Your Hotel:</strong> By adding your hotel’s special dishes and details, you can attract more customers and increase visibility for your establishment.</li>
             <li><strong>Order Tracking:</strong> Hotel owners can view real-time updates on their orders, ensuring smooth coordination with farmers and timely delivery.</li>
             <li><strong>Collaboration Opportunities:</strong> Develop long-term partnerships with trusted farmers, ensuring consistent supply and quality for your business.</li>
       </ol>
    </div>
    <br>
      
    <!--Unique Features for Hotel Owners on Farm Hotel Link-->
    <div style="width: 100%; padding: 20px; background-color: #f0f0f0; font-family: Arial, sans-serif; line-height: 1.6;">
    <h2 style="color: #2c3e50; text-align: center; font-size: 30px;">Unique Features for Hotel Owners on Farm Hotel Link</h2>
    <ul style="margin: 0 auto; padding: 0 20px; max-width: 80%; font-size: 18px; color: #333;">
        <li style="margin-bottom: 10px;">
            <strong>Browse Fresh Products:</strong> Hotel owners can easily browse a wide range of fresh, locally sourced farm products. Detailed product information helps them make the best purchasing decisions.
        </li>
        <li style="margin-bottom: 10px;">
            <strong>Add Special Dishes:</strong> Showcase your hotel's culinary specialties by uploading details of signature dishes. This acts as a marketing tool, attracting customers by highlighting what makes your hotel stand out.
        </li>
        <li style="margin-bottom: 10px;">
            <strong>Order Management:</strong> Seamlessly place orders for farm products and track their progress from order confirmation to delivery. The system ensures transparency and reliability.
        </li>
        <li style="margin-bottom: 10px;">
            <strong>Farmer Profiles:</strong> View detailed profiles of farmers, including their location, farming practices, and crop availability. This fosters trust and ensures quality sourcing.
        </li>
        <li style="margin-bottom: 10px;">
            <strong>Location Services:</strong> Use integrated location tools to plan deliveries efficiently and ensure timely receipt of fresh produce for your hotel.
        </li>
     </ul>
     </div>
     <br>

     <!--Benefits for Hotel Owners-->
     <div style="width: 100%; padding: 20px; background-color: #f0f0f0; font-family: Arial, sans-serif; line-height: 1.6;">
     <h2 style="color: #2c3e50; text-align: center; font-size: 30px;">Benefits for Hotel Owners</h2>
     <ul style="margin: 0 auto; padding: 0 20px; max-width: 80%; font-size: 18px; color: #333;">
         <li style="margin-bottom: 10px;">
            <strong>Freshness Guaranteed:</strong> Access farm-fresh products directly from trusted farmers.
         </li>
         <li style="margin-bottom: 10px;">
            <strong>Cost Efficiency:</strong> Eliminate intermediaries, reducing costs and increasing value.
         </li>
         <li style="margin-bottom: 10px;">
            <strong>Marketing Potential:</strong> Highlight your hotel’s unique dishes and attract a broader customer base.
         </li>
         <li style="margin-bottom: 10px;">
            <strong>Sustainability:</strong> Support local farmers and promote eco-friendly practices by sourcing locally.
         </li>
         <li>
            <strong>Business Growth:</strong> Build strong relationships with farmers while enhancing your hotel’s reputation for offering fresh and quality dishes.
         </li>
     </ul>
     </div>
     <br>

    <!-- why hotel owner choose farm hotel link is -->
    <div style="width: 100%; padding: 20px; background-color: #f0f0f0; text-align: center; font-size: 18px; font-family: Arial, sans-serif;">
         <h2 style="color: #2c3e50; font-size: 30px;">Why hotel owner Choose Farm Hotel Link</h2>
            <p style="max-width: 80%; margin: 0 auto; line-height: 1.6;  font-size :18px;">
              Farm Hotel Link is more than a marketplace—it’s a platform that empowers hotel owners to grow their business, enhance their menu offerings, and establish meaningful collaborations with farmers. By prioritizing fresh, locally sourced ingredients, hotels can deliver exceptional dining experiences that customers will love.
            </p>
    </div> 



    <!-- Footer Section -->
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
        // JavaScript specific to Hotel Owner Dashboard
        document.addEventListener('DOMContentLoaded', () => {
            console.log('Hotel Owner Dashboard loaded');
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
