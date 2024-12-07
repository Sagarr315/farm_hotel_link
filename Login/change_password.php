<?php
// change_password.php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['reset_user_id'])) {
    header('Location: forget_password.php');
    exit;
}

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password === $confirm_password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $user_id = $_SESSION['reset_user_id'];

        $query = "UPDATE users SET password = ? WHERE UserID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('si', $hashed_password, $user_id);

        if ($stmt->execute()) {
            unset($_SESSION['reset_user_id']);
            $successMessage = "Password successfully changed! Redirecting to login page...";
            echo "<script>
                setTimeout(() => { window.location.href = 'login.html'; }, 3000);
            </script>";
        } else {
            $errorMessage = "Failed to update password. Please try again.";
        }
    } else {
        $errorMessage = "Passwords do not match. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
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
        .message-container {
            position: absolute;
            top: 10px;
            width: 100%;
            text-align: center;
        }
        .message {
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            margin: 0 auto;
            display: inline-block;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
        label {
            display: block;
            margin-bottom: 5px;
            font-size: 16px;
            font-weight: bold;
            text-align: left;
        }
        input[type="password"] {
            width: calc(100% - 20px);
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 18px;
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
    </style>
</head>
<body>
    <div class="message-container" id="messageContainer"></div>

    <div class="form-container">
        <h2>Change Password</h2>
            <form method="POST" id="passwordForm">
                 <input type="password" id="password" name="password" placeholder="New Password" required>
                 <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                 <button type="submit" name="save">Save</button>
            </form>
    </div>

    <script>
        // Display success or error messages dynamically
        const successMessage = "<?php echo $successMessage; ?>";
        const errorMessage = "<?php echo $errorMessage; ?>";
        const messageContainer = document.getElementById('messageContainer');

        if (successMessage) {
            messageContainer.innerHTML = `<div class="message success">${successMessage}</div>`;
        }
        if (errorMessage) {
            messageContainer.innerHTML = `<div class="message error">${errorMessage}</div>`;
        }
    </script>
</body>
</html>
