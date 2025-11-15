<?php
session_start();

// Unset all OTP-related session variables
unset($_SESSION['otp']);
unset($_SESSION['otp_email']);
unset($_SESSION['otp_sent']);
unset($_SESSION['otp_verified']);

// Redirect to login page
header("Location: login.php");
exit;
?>