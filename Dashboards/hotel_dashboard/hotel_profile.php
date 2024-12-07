<?php
session_start();
include('db_connection.php');

// Fetch user details from 'users' and 'hotelowner' tables based on the logged-in user's ID
$userId = $_SESSION['UserID'];

// Fetch user details from 'users' table
$userQuery = "SELECT * FROM users WHERE UserID = ?";
$userStmt = $conn->prepare($userQuery);
$userStmt->bind_param("i", $userId);
$userStmt->execute();
$userResult = $userStmt->get_result();
$user = $userResult->fetch_assoc();

// Fetch hotel owner details from 'hotelowner' table
$hotelOwnerQuery = "SELECT * FROM hotelowner WHERE UserID = ?";
$hotelOwnerStmt = $conn->prepare($hotelOwnerQuery);
$hotelOwnerStmt->bind_param("i", $userId);
$hotelOwnerStmt->execute();
$hotelOwnerResult = $hotelOwnerStmt->get_result();
$hotelOwner = $hotelOwnerResult->fetch_assoc();

// Fetch location details from 'location' table
$locationQuery = "SELECT * FROM location WHERE UserID = ?";
$locationStmt = $conn->prepare($locationQuery);
$locationStmt->bind_param("i", $userId);
$locationStmt->execute();
$locationResult = $locationStmt->get_result();
$location = $locationResult->fetch_assoc();

// Handle profile information update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $hotelName = $_POST['hotelName'];
    $hotelAddress = $_POST['hotelAddress'];
    $sinceStarted = $_POST['sinceStarted'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Ensure latitude and longitude are not empty
    if (empty($latitude) || empty($longitude)) {
        echo "<script>alert('Please ensure latitude and longitude are properly set.');</script>";
        exit();
    }

    // Handle profile picture upload
    $profilePicPath = $hotelOwner ? $hotelOwner['ProfilePicture'] : ''; // Default to existing path

    if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] == 0) {
        $uploadDir = './farmhotel/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true); // Create directory if it does not exist
        }

        $fileName = time() . "_" . basename($_FILES['profilePic']['name']); // Add timestamp to avoid conflicts
        $uploadFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['profilePic']['tmp_name'], $uploadFile)) {
            $profilePicPath = $uploadFile; // Set the new profile picture path
        } else {
            echo "<script>alert('Failed to upload the profile picture. Please try again.');</script>";
        }
    }

    // Update profile information in 'users' table
    $updateUserQuery = "UPDATE users SET name = ?, email = ?, phone = ? WHERE UserID = ?";
    $updateUserStmt = $conn->prepare($updateUserQuery);
    $updateUserStmt->bind_param("sssi", $name, $email, $phone, $userId);
    $updateUserStmt->execute();

    // Insert new hotel owner details or update existing record
    if (!$hotelOwner) {
        $insertHotelOwnerQuery = "INSERT INTO hotelowner (HotelName, HotelAddress, SinceStartedYear, ProfilePicture, UserID) 
                                  VALUES (?, ?, ?, ?, ?)";
        $insertHotelOwnerStmt = $conn->prepare($insertHotelOwnerQuery);
        $insertHotelOwnerStmt->bind_param("ssssi", $hotelName, $hotelAddress, $sinceStarted, $profilePicPath, $userId);
        $insertHotelOwnerStmt->execute();
    } else {
        $updateHotelOwnerQuery = "UPDATE hotelowner SET HotelName = ?, HotelAddress = ?, SinceStartedYear = ?, ProfilePicture = ? WHERE UserID = ?";
        $updateHotelOwnerStmt = $conn->prepare($updateHotelOwnerQuery);
        $updateHotelOwnerStmt->bind_param("ssssi", $hotelName, $hotelAddress, $sinceStarted, $profilePicPath, $userId);
        $updateHotelOwnerStmt->execute();
    }

    // Update or insert location data
    if ($location) {
        $updateLocationQuery = "UPDATE location SET Latitude = ?, Longitude = ? WHERE UserID = ?";
        $updateLocationStmt = $conn->prepare($updateLocationQuery);
        $updateLocationStmt->bind_param("ddi", $latitude, $longitude, $userId);
        $updateLocationStmt->execute();
    } else {
        $insertLocationQuery = "INSERT INTO location (UserID, Latitude, Longitude) VALUES (?, ?, ?)";
        $insertLocationStmt = $conn->prepare($insertLocationQuery);
        $insertLocationStmt->bind_param("idd", $userId, $latitude, $longitude);
        $insertLocationStmt->execute();
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Owner Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0f7fa;
            color: #333;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #2c3e50;
            color: white;
            padding: 15px;
            text-align: center;
            position: sticky;
            top: 0;
            width: 100%;
            z-index: 1000;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: white;
        }
        .profile-pic-container {
            position: relative;
            margin: 20px auto;
            width: 190px;
            height: 190px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid #2c3e50;
            background-color: #e9ecef;
        }
        .profile-pic {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .pencil-icon {
            position: absolute;
            bottom: 10px;
            right: 10px;
            width: 35px;
            height: 35px;
            cursor: pointer;
            background-color: #fff;
            border-radius: 50%;
            border: 2px solid;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s;
        }
        .pencil-icon:hover {
            background-color: #f8f9fa;
        }
        .upload-options {
            display: none;
            position: absolute;
            bottom: 50px;
            right: 0;
            background-color: #2c3e50;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            z-index: 10;
        }
        .upload-options button {
            width: 100%;
            padding: 10px;
            border: none;
            background-color: #2c3e50;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        .upload-options button:hover {
            background-color: blue;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        input[type="file"] {
            margin-bottom: 15px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #2c3e50;                                    
            color: white;                                         
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: blue;
        }
        .message {
            display: none;
            padding: 10px;
            margin: 10px 0;
            color: #fff;
            background-color: #2C3E50;
            text-align: center;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<header class="header">
    <img src="../images/farm_hotel_link_logo.png" alt="Website Logo" style="width: 65px; height:65px; vertical-align: middle;">
    <h1 style="font-weight:bold; font-size:45px; display:inline-block; vertical-align: middle;">Farm Hotel Link</h1>
</header>

<div class="container">
    <h1 style="color:black;">Hotel Owner Profile</h1>
    <div class="message" id="message"></div>

    <div class="profile-pic-container">
        <?php
        // Check if ProfilePicture exists and set path, else set default
        $profilePicPath = (!empty($hotelOwner['ProfilePicture']) && file_exists($hotelOwner['ProfilePicture'])) ? 
            $hotelOwner['ProfilePicture'] : 'default.jpg';
        ?>
        
        <!-- Profile Picture -->
        <img src="<?php echo htmlspecialchars($profilePicPath); ?>" alt="Profile Picture" class="profile-pic" id="profilePic" style="width: 100%; height: 100%; object-fit: cover;">
        
        <div class="pencil-icon" id="pencilIcon">✏️</div>
        <div class="upload-options" id="uploadOptions">
            <button id="uploadPhotoBtn">Upload Photo</button>
            <button id="removePhotoBtn">Remove Photo</button>
            <button id="cancelBtn">Cancel</button>
        </div>
    </div>

    <form id="hotelOwnerProfileForm" method="POST" enctype="multipart/form-data">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo $user['Name']; ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $user['Email']; ?>" required>

        <label for="phone">Phone Number:</label>
        <input type="text" id="phone" name="phone" value="<?php echo $user['Phone']; ?>" required>

        <label for="hotelName">Hotel Name:</label>
        <input type="text" id="hotelName" name="hotelName" value="<?php echo $hotelOwner['HotelName'] ?? ''; ?>" required>

        <label for="hotelAddress">Hotel Address:</label>
        <input type="text" id="hotelAddress" name="hotelAddress" value="<?php echo $hotelOwner['HotelAddress'] ?? ''; ?>" required>

        <label for="sinceStarted">Since Started:</label>
        <input type="text" id="sinceStarted" name="sinceStarted" value="<?php echo $hotelOwner['SinceStartedYear']?? ''; ?>" required>

        
         <!-- Location fields -->
         <label for="latitude">Latitude:</label>
        <input type="text" id="latitude" name="latitude" value="<?php echo $location['Latitude'] ?? ''; ?>" readonly required>

        <label for="longitude">Longitude:</label>
        <input type="text" id="longitude" name="longitude" value="<?php echo $location['Longitude'] ?? ''; ?>" readonly required>

        <button type="button" onclick="getLocation()">Get Location</button>


        <!-- Hidden file input for profile picture -->
        <input type="file" name="profilePic" id="profilePicUpload" style="display:none;">

        <button type="submit">Save Information</button>
    </form>
</div>

<script>
function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
            }, function() {
                alert("Unable to retrieve your location.");
            });
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }

    const profilePic = document.getElementById('profilePic');
    const uploadOptions = document.getElementById('uploadOptions');
    const pencilIcon = document.getElementById('pencilIcon');
    const fileInput = document.getElementById('profilePicUpload');
    const form = document.getElementById('hotelOwnerProfileForm');

    pencilIcon.addEventListener('click', function() {
        uploadOptions.style.display = 'block';
    });

    document.getElementById('uploadPhotoBtn').addEventListener('click', function() {
        fileInput.click();
    });

    fileInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                profilePic.src = e.target.result;
                profilePic.style.display = 'block';
                uploadOptions.style.display = 'none';
            };
            reader.readAsDataURL(file);
            form.submit();
        }
    });

    document.getElementById('removePhotoBtn').addEventListener('click', function() {
        profilePic.src = '';
        profilePic.style.display = 'none';
        uploadOptions.style.display = 'none';
    });

    document.getElementById('cancelBtn').addEventListener('click', function() {
        uploadOptions.style.display = 'none';
    });
</script>

</body>
</html>
