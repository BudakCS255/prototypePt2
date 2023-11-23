<?php
session_start();

// If the user is not logged in, redirect to the login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Access control function
function checkUserType($requiredType) {
    if ($_SESSION['user_type'] !== $requiredType) {
        echo "You do not have permission to access this page.";
        exit();
    }
}
?>
