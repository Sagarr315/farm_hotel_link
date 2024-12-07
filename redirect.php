<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['user_Type'])) {
        $userType = $_POST['user_Type'];

        // Redirect based on the button clicked
        if ($userType == 'Farmer') {
            header('Location: Register/farmer_register.php');
        } elseif ($userType == 'Hotel Owner') {
            header('Location: Register/hotel_register.php');
        } else {
            echo "Invalid selection.";
        }
        exit();
    } else {
        echo "No user type selected.";
    }
} else {
    echo "No form submission detected.";
}
