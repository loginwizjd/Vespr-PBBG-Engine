<?php
session_start();
include('includes/utils.php'); // Ensure this path is correct
session_unset(); // Clear all session variables
session_destroy(); // Destroy the session

// Redirect to the home page or login page
redirect('login.php');
?>
