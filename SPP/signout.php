<?php
// Start the session
session_start();

// Include your database connection script
include '../conn.php';

// Check if the user is logged in
if (isset($_SESSION['associate_id'])) {
    // Get the associate_id from the session
    $associate_id = $_SESSION['associate_id'];

    // Clear the session_id in the database
    $update_stmt = $conn->prepare("UPDATE Associate SET session_id = NULL WHERE associate_id = ?");
    $update_stmt->bind_param("i", $associate_id);
    $update_stmt->execute();
    $update_stmt->close();

    // Destroy all session data
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session

    // Optional: Delete the session cookie if used
    // if (ini_get("session.use_cookies")) {
    //     $params = session_get_cookie_params();
    //     setcookie(session_name(), '', time() - 42000,
    //         $params["path"], $params["domain"],
    //         $params["secure"], $params["httponly"]
    //     );
    // }
}

// Redirect to the login page
header("Location: login.php"); // or redirect to signout.php
exit; // Ensure script execution stops here
?>
