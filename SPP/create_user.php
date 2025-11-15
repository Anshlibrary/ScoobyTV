<?php

// Enable error reporting
error_reporting(E_ALL);

// Display errors on the screen
ini_set('display_errors', 1);
session_start();
require '../conn.php';

// Initialize status messages array if not set
if (!isset($_SESSION['status_messages'])) {
    $_SESSION['status_messages'] = [];
}
// Check if associate is logged in
if (!isset($_SESSION['associate_id'])) {
    //$_SESSION['status_messages'][] = "Error: You must log in to create users.";
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['session_id'])) {
   // $_SESSION['status_messages'][] = "Error: You must log in to create users.";
    header('Location: signout.php');
    exit;
}

$associate_id = $_SESSION['associate_id'];
// Fetch associate's full name and credits
$associate_sql = "SELECT fullname, credits, paid_amt, username FROM Associate WHERE associate_id = ?";
$associate_stmt = $conn->prepare($associate_sql);
if (!$associate_stmt) {
    $_SESSION['status_messages'][] = "Database error: (" . $conn->errno . ") " . $conn->error;
    header('Location: dashboard.php');
    exit;
}
$associate_stmt->bind_param('i', $associate_id);
if (!$associate_stmt->execute()) {
    $_SESSION['status_messages'][] = "Database error: (" . $associate_stmt->errno . ") " . $associate_stmt->error;
    header('Location: dashboard.php');
    exit;
}
$associate_result = $associate_stmt->get_result();
if ($associate_result->num_rows === 0) {
    $_SESSION['status_messages'][] = "Associate not found.";
    header('Location: dashboard.php');
    exit;
}
$associate = $associate_result->fetch_assoc();
$current_credits = $associate['credits'];
$associate_paid_amt=$associate['paid_amt'];
$associate_username=$associate['username'];
$associate_stmt->close();


/*Jellyfin create user*/
function createUser($serverUrl, $apiKey, $jellyuser, $jellypass) {
    $url = $serverUrl . 'Users/New';
    $data = json_encode([
        'Name' => $jellyuser,
        'Password' => $jellypass
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-Emby-Token: ' . $apiKey
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        //echo "cURL Error: $error\n";
    }

    if ($httpCode !== 200) {
       //echo "HTTP Code: $httpCode\nResponse: $response\n";
    }
    return json_decode($response, true);
}

function setUserPermissions($serverUrl, $apiKey, $userId) {
    $url = $serverUrl . "Users/$userId/Policy";
    $data = json_encode([
        'IsAdministrator' => false,
        'IsHidden' => false,
        'IsDisabled' => false,
        'EnableAllDevices' => true,
        'EnableAllChannels' => true,
        'EnablePublicSharing' => true,
        'EnableRemoteAccess' => true,
        'EnableLiveTvManagement' => true,
        'EnableLiveTvAccess' => true,
        'EnableMediaPlayback' => true,
        'EnableAudioPlaybackTranscoding' => true,
        'EnableVideoPlaybackTranscoding' => true,
        'MaxActiveSessions' => 2
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-Emby-Token: ' . $apiKey
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    if ($error) {
        //echo "cURL Error: $error\n";
    }
    if ($httpCode !== 200) {  
        //echo "HTTP Code: $httpCode\nResponse: $response\n";
    }
      // Set user configuration to disable subtitles by default
    $url = $serverUrl . "Users/$userId/Configuration";
    $configData = json_encode([
        'SubtitleMode' => 'None'  // Ensure subtitles are set to 'None'
    ]);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $configData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-Emby-Token: ' . $apiKey
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    if ($error) {
       // echo "cURL Error: $error\n";
    }
    if ($httpCode !== 200) {
       // echo "HTTP Code: $httpCode\nResponse: $response\n";
    }
    return json_decode($response, true);
}

$serverUrl = 'http://stv1.xyz:53570/'; // Replace with your server URL
$apiKey = '6ca9deb34c874712bbc8ea219dcec6e2'; // Replace with your API key
$jellyfin_status="inactive";
/*jellyfin ends here*/



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Calculate the number of users being created
    $user_count = 0;
    $credit_deduct=0;
    for ($i = 1; $i <= 10; $i++) {
        if (!empty($_POST["username$i"]) && !empty($_POST["password$i"]) && isset($_POST["month$i"])) {
            $user_count++;
            $credit_deduct += $_POST["month$i"];
        }
    }
    // Ensure the associate has enough credits
    if ($credit_deduct > $current_credits) {
        $_SESSION['status_messages'][] = "Error: You do not have enough credits to create $user_count users.";
        header('Location: dashboard.php');
        exit;
    }
    // Begin transaction
    $conn->begin_transaction();
    
    $purchase="Successfull & Verified";
    $plan_amount="99";
    $no_of_devices=1;
    $updatedby=$associate_username;
    try {
        for ($i = 1; $i <= $user_count; $i++) {
            if (!empty($_POST["username$i"]) && !empty($_POST["password$i"])) {
                $username = trim($_POST["username$i"]);
                $password = trim($_POST["password$i"]); // Hash password
                $subscription_months = $_POST["month$i"]; // Subscription duration in months
                $paid_amt=$associate_paid_amt * $subscription_months;

                // Calculate plan expiry date with time
    $current_date = new DateTime(); // Current date and time
    $current_date->modify("+$subscription_months months");
    $plan_expiry = $current_date->format('Y-m-d H:i:s'); // Format with date and time
                // Check for unique username
                $unique_username = $username;
                $count = 1;
                while (true) {
                    // Check if the username exists
                    $check_username_sql = "SELECT COUNT(*) FROM users WHERE username = ?";
                    $check_username_stmt = $conn->prepare($check_username_sql);
                    if (!$check_username_stmt) {
                        throw new Exception("Database error: (" . $conn->errno . ") " . $conn->error);
                    }
                    $check_username_stmt->bind_param('s', $unique_username);
                    if (!$check_username_stmt->execute()) {
                        throw new Exception("Database error: (" . $check_username_stmt->errno . ") " . $check_username_stmt->error);
                    }
                    $check_username_stmt->bind_result($count_existing);
                    $check_username_stmt->fetch();
                    $check_username_stmt->close();
                    // If the username already exists, append a number
                    if ($count_existing > 0) {
                        $count++;
                        $unique_username = $username . $count; // Create new username
                    } else {
                        break; // Username is unique
                    }
                }
                $jellyuser=$unique_username;
$jellypass=$password;
$userResponse = createUser($serverUrl, $apiKey, $jellyuser, $jellypass);
$userId = $userResponse['Id'];
if (isset($userId)) {
    $jellyfin_status = "active - $userId"; 
    //echo "User created with ID: $userId\n";
    $permissionsResponse = setUserPermissions($serverUrl, $apiKey, $userId);
   // echo "Permissions set for user ID: $userId\n";
} else {
     $jellyfin_status = "Failed to create user";
     $_SESSION['status_messages'][] = "$userResponse"; 
} 
                // Insert user into users table with the unique username
                $create_user_sql = "INSERT INTO users (full_name, username, password, plan_valid_for, plan_amount, purchase, plan_expiry, no_of_devices, txn_id, paid_amt, updated_by, jellyfin_status ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $create_user_stmt = $conn->prepare($create_user_sql);
                if (!$create_user_stmt) {
                    throw new Exception("Database error: (" . $conn->errno . ") " . $conn->error);
                }
                $create_user_stmt->bind_param('sssisssisdss', $username, $unique_username, $password, $subscription_months, $plan_amount, $purchase, $plan_expiry, $no_of_devices, $associate_username, $paid_amt, $updatedby, $jellyfin_status);
                if (!$create_user_stmt->execute()) {
                    throw new Exception("Database error: (" . $create_user_stmt->errno . ") " . $create_user_stmt->error);
                }
                $create_user_stmt->close();
            }
        }
        // Deduct credits from associate
        $new_credits = $current_credits -  $credit_deduct;
        $update_credits_sql = "UPDATE Associate SET credits = ? WHERE associate_id = ?";
        $update_credits_stmt = $conn->prepare($update_credits_sql);
        if (!$update_credits_stmt) {
            throw new Exception("Database error: (" . $conn->errno . ") " . $conn->error);  
        }
        $update_credits_stmt->bind_param('ii', $new_credits, $associate_id);
        if (!$update_credits_stmt->execute()) {
            throw new Exception("Database error: (" . $update_credits_stmt->errno . ") " . $update_credits_stmt->error);
            
        }
        $update_credits_stmt->close();

        // Commit the transaction
        $conn->commit();
        $_SESSION['status_messages'][] = "$user_count users successfully created. $new_credits credits remaining.";
        header('Location: dashboard.php');
    } catch (Exception $e) {
        // Rollback transaction in case of error
        $conn->rollback();
        $_SESSION['status_messages'][] = $e->getMessage();
        header('Location: dashboard.php');
    }
}
$conn->close();
exit;
?>
