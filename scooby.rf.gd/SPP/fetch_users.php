<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../conn.php'; // Include your database connection file

// Check if associate is logged in
if (!isset($_SESSION['associate_id'])) {
   // $_SESSION['status_messages'][] = "Error: You must log in to create users.";
    header('Location: login.php');
    exit;
}
if (!isset($_SESSION['session_id'])) {
   // $_SESSION['status_messages'][] = "Error: You must log in to create users.";
    header('Location: signout.php');
    exit;
}
$associate_id = $_SESSION['associate_id'];

 $assoc_sql = "SELECT coupon_code FROM Associate WHERE associate_id = ?";
$assoc_stmt = $conn->prepare($assoc_sql);
if (!$assoc_stmt) {
    $_SESSION['status_messages'][] = "Database error: (" . $conn->errno . ") " . $conn->error;
    exit;
}
$assoc_stmt->bind_param('i', $associate_id);
if (!$assoc_stmt->execute()) {
    $_SESSION['status_messages'][] = "Database error: (" . $associate_stmt->errno . ") " . $associate_stmt->error;
    exit;
}
$assoc_result = $assoc_stmt->get_result();
if ($assoc_result->num_rows === 0) {
    $_SESSION['status_messages'][] = "Associate not found.";
    exit;
}
$assoc = $assoc_result->fetch_assoc();

$assoc_coupon = htmlspecialchars($assoc['coupon_code']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);


    $associate_sql = "SELECT username, coupon_code FROM Associate WHERE email = ?";
$associate_stmt = $conn->prepare($associate_sql);
if (!$associate_stmt) {
    $_SESSION['status_messages'][] = "Database error: (" . $conn->errno . ") " . $conn->error;
    exit;
}
$associate_stmt->bind_param('s', $email);
if (!$associate_stmt->execute()) {
    $_SESSION['status_messages'][] = "Database error: (" . $associate_stmt->errno . ") " . $associate_stmt->error;
    exit;
}
$associate_result = $associate_stmt->get_result();
if ($associate_result->num_rows === 0) {
    $_SESSION['status_messages'][] = "Associate not found.";
    exit;
}
$associate = $associate_result->fetch_assoc();

$assoc_username = htmlspecialchars($associate['username']);
$user_coupon = htmlspecialchars($associate['coupon_code']);
if ($assoc_coupon !== $user_coupon) {
     echo json_encode(array());
    exit;
}

    // Prepare the SQL query to fetch the relevant fields
    $sql = "SELECT full_name, username, created, plan_expiry, plan_valid_for FROM users WHERE txn_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('s', $assoc_username);
        $stmt->execute();
        $result = $stmt->get_result();

        $users = array(); // Initialize an array to store the result

        // Fetch the result as an associative array
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        // Return the result in JSON format
        echo json_encode($users);

        $stmt->close();
    } else {
        echo json_encode(array()); // Return an empty array if no users are found
    }
}
?>
