<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require '../conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['emailDisable'])) {
$email = filter_var($_POST['emailDisable'], FILTER_SANITIZE_EMAIL);

    // SQL query to disable the user based on email
    $sql = "UPDATE Associate SET isdisabled = 1 WHERE email = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('s', $email);
        if ($stmt->execute()) {
            $_SESSION['status_messages'][] = "$email - disabled successfully.";
        } else {
            $_SESSION['status_messages'][] = "Error disabling user.";
        }
        $stmt->close();
    }
    $conn->close();
    
    // Redirect back to the user management page
    header("Location: clients.php");
    exit();
}
else {
            $_SESSION['status_messages'][] = "Something went wrong.";
             header("Location: clients.php");
    exit();
        }
?>
