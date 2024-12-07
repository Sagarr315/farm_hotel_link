<?php
// forget_password.php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['next'])) {
    $userType = $_POST['userType'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    $query = "SELECT UserID FROM users WHERE UserType = ? AND Name = ? AND Phone = ? AND Email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssss', $userType, $name, $phone, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['reset_user_id'] = $user['UserID'];
        header('Location: change_password.php');
        exit;
    } else {
        $error = "No user found with the provided details. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
        }
        h2 {
            margin-bottom: 30px;
            color: #333;
            font-size: 32px;
        }
        p {
            margin-bottom: 20px;
            color: #555;
            font-size: 16px;
        }
        input[type="text"],
        input[type="email"],
        input[type="tel"] {
            width: calc(100% - 20px);
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 18px;
        }
        .role-selection {
            margin-bottom: 15px;
        }
        .role-selection label {
            font-size: 18px;
            margin-right: 15px;
        }
        button {
            width: 100%;
            padding: 15px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            font-size: 18px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            font-size: 16px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2><u>Forget Password</u></h2>
        <p style="font-size: 19px; font-family: Arial, sans-serif; color: #333; text-align: center;"><strong>
    Fill the fields correctly as you provided during registration.</strong>
</p>

        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
        <form method="POST">
            <div class="role-selection">
                <label><input type="radio" name="userType" value="Farmer" required> Farmer</label>
                <label><input type="radio" name="userType" value="HotelOwner" required> Hotel Owner</label>
            </div>

            <input type="text" name="name" placeholder="Name" required>
            <input type="tel" name="phone" placeholder="Phone Number" required>
            <input type="email" name="email" placeholder="Email" required>

            <button type="submit" name="next">Next</button>
        </form>
    </div>
</body>
</html>
