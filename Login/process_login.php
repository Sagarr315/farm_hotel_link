<?php
session_start(); // Start the session

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "farm_hotel_link";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_POST['email'];
$password = $_POST['password'];

if (empty($email) || empty($password)) {
    echo "Both email and password are required.";
    exit();
}

$stmt = $conn->prepare("SELECT UserID, Name, Password, UserType FROM users WHERE Email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($userID, $name, $hashed_password, $userType);
    $stmt->fetch();
    
    if (password_verify($password, $hashed_password)) {
        // Store UserID, UserType, Name, and Email in the session
        $_SESSION['UserID'] = $userID;
        $_SESSION['UserType'] = $userType;
        $_SESSION['Name'] = $name;
        $_SESSION['Email'] = $email;

        // Redirect to respective dashboard based on UserType
        if ($userType == 'Farmer') {
            header("Location: ../Dashboards/farmer_dashboard/farmer_dashboard.php");
        } elseif ($userType == 'HotelOwner') {
            header("Location: ../Dashboards/hotel_dashboard/hotel_dashboard.php");
        } else {
            echo "Unknown user type.";
        }
        exit();
    } else {
        echo "Invalid password.";
    }
} else {
    echo "No account found with that email.";
}

$stmt->close();
$conn->close();
?>
