<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #333;
            color: white;
            padding: 15px;
            text-align: center;
            width: 100%;
            z-index: 1000;
            position: relative;
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
        .logout-container {
            background-color: white;
            padding: 20px;
            max-width: 600px;
            margin: 50px auto;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .logout-container h2 {
            color: #333;
        }
        .logout-container p {
            margin: 20px 0;
        }
        .logout-container .buttons {
            margin: 20px 0;
        }
        .logout-container button {
            padding: 10px 20px;
            margin: 10px;
            border: none;
            border-radius: 5px;
            background-color: #007BFF;
            color: white;
            cursor: pointer;
        }
        .logout-container button:hover {
            background-color: #0056b3;
        }
        .feedback-form {
            display: none; /* Initially hidden */
        }
        textarea {
            width: 100%;
            height: 80px;
            margin-bottom: 10px;
        }
        footer {
            text-align: center;
            padding: 1rem 0;
            background-color: #333;
            color: white;
            position: relative;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <!-- Header with logo and website name -->
    <header>
        <img src="../images/farm_hotel_link_logo.png" alt="Website Logo">
        <h1 style="color:white;">Farm Hotel Link</h1>
    </header>

    <div class="logout-container">
        <h2>Do you really want to logout?</h2>
        <p>Please provide feedback before you logout.</p>

        <!-- Buttons for Yes and No -->
        <div class="buttons">
            <button id="yesButton">Yes</button>
            <!-- Redirect to login page when No is clicked -->
            <button onclick="window.location.href='farmer_dashboard.php';">No</button>
        </div>

        <!-- Feedback Form (Initially hidden) -->
        <div class="feedback-form" id="feedbackForm">
            <form action="logout_process.php" method="POST">
                <p><strong>Is our website good or bad?</strong></p>
                <label>
                    <input type="radio" name="website_quality" value="Good" required> Good
                </label>
                <label>
                    <input type="radio" name="website_quality" value="Bad" required> Bad
                </label>

                <p><strong>What improvements should we make?</strong></p>
                <textarea name="improvements" placeholder="Type your feedback here..." required></textarea>

                <p><strong>What is the reason for logging out?</strong></p>
                <textarea name="logout_reason" placeholder="Why are you logging out?" required></textarea>

                <button type="submit">Submit Feedback & Logout</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        &copy; 2024 Farm Hotel Link. All Rights Reserved.
    </footer>

    <script>
        document.getElementById('yesButton').addEventListener('click', function() {
            document.getElementById('feedbackForm').style.display = 'block';
            this.style.display = 'none';
        });
    </script>
</body>
</html>
