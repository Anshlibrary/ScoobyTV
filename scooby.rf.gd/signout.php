<?php
// Start the session
session_start();

// Unset individual session variables
unset($_SESSION['user_id']);
unset($_SESSION['first_name']);
unset($_SESSION['allocated_ip']);

// Destroy all session data
session_destroy();

// Redirect to a sign-out page or any other page as needed
header("Location: index.php");// Redirect to signout.php
exit; // Ensure script execution stops here
?>