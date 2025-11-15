<?php
include('../conn.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer library files
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

// Get the form data
$username=$_POST['username'];
$password=$_POST['password'];
$user_id = $_POST['user_id'];
$updated_by = $_POST['updated_by'];
$purchase = $_POST['purchase'];
$allocated_ip = $_POST['allocated_ip'];
$plan_amount = $_POST['plan_amount'];
$plan_valid_for = $_POST['plan_valid_for'];
$reason = $_POST['reason'];
$email = $_POST['useremail'];

// Check if reason is empty and set purchase status accordingly
if (empty($reason)) {
    $purchase = "Successfull & Verified";
    
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
        echo "cURL Error: $error\n";
    }

    if ($httpCode !== 200) {
        echo "HTTP Code: $httpCode\nResponse: $response\n";
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
        'EnableVideoPlaybackTranscoding' => true
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
        echo "cURL Error: $error\n";
    }

    if ($httpCode !== 200) {
        echo "HTTP Code: $httpCode\nResponse: $response\n";
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

// Function to get a user ID by username
function getUserIdByUsername($serverUrl, $apiKey, $jellyuser) {
    $url = $serverUrl . "Users?searchTerm=" . urlencode($jellyuser);

    $options = [
        'http' => [
            'header' => [
                "Content-Type: application/json",
                "X-Emby-Token: $apiKey"
            ],
            'method' => 'GET',
        ],
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === FALSE) {
        return null;
    }

    $users = json_decode($response, true);
    foreach ($users as $user) {
        if (strcasecmp($user['Name'], $jellyuser) == 0) {
            return $user['Id'];
        }
    }

    return null;
}

// Function to enable the user by sending a request to Jellyfin
function enableUserById($serverUrl, $apiKey, $userId) {
    $url = $serverUrl . "Users/$userId/Policy";
    $data = json_encode([
        "IsDisabled" => false,
        "PasswordResetProviderId" => "default",
        "AuthenticationProviderId" => "default"

    ]);

    $options = [
        'http' => [
            'header' => [
                "Content-Type: application/json",
                "X-Emby-Token: $apiKey"
            ],
            'method' => 'POST',
            'content' => $data,
        ],
    ];
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === FALSE) {
        $error = error_get_last();
        echo "Error occurred while enabling the user:\n";
        echo "Message: " . $error['message'] . "\n";
        echo "File: " . $error['file'] . "\n";
        echo "Line: " . $error['line'] . "\n";
        return false;
    }

    $httpResponseCode = $http_response_header[0] ?? "No response header";
    echo "HTTP Response: $httpResponseCode\n";

    $decodedResponse = json_decode($result, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "Error decoding JSON response: " . json_last_error_msg() . "\n";
        echo "Raw Response: $result\n";
    } else {
        echo "Response: " . print_r($decodedResponse, true) . "\n";
    }

    return true;
}

$serverUrl = 'http://stv1.xyz:53570/'; // Replace with your server URL
$apiKey = '6ca9deb34c874712bbc8ea219dcec6e2'; // Replace with your API key
$jellyuser=$username;
$jellypass=$password;
$userResponse;
// $userResponse = createUser($serverUrl, $apiKey, $jellyuser, $jellypass);
 $userId;
//  $userId = $userResponse['Id'];
$jellyfin_status ="active";
if (isset($userId)) {
    echo "User created with ID: $userId\n";
    $permissionsResponse = setUserPermissions($serverUrl, $apiKey, $userId);
    $jellyfin_status ="active - $userId";
    echo "Permissions set for user ID: $userId\n";
} else {
    // $userId = getUserIdByUsername($serverUrl, $apiKey, $jellyuser);
    // if ($userId) {
    // echo "User already exists with ID: $userId\n";
    // echo "enabling user...\n";
    //  if (0) { //enableUserById($serverUrl, $apiKey, $userId)
    //         echo "User re-enabled successfully.\n";
    //     } else {
    //         echo "Failed to re-enable the user.\n";
    //     }
    // }
}
/*jellyfin ends here*/
} else {
    $purchase = $reason;
}

// Calculate the plan_expiry date
$current_date = new DateTime();
$plan_expiry = $current_date->add(new DateInterval('P' . $plan_valid_for . 'M'))->format('Y-m-d');

// Set no_of_devices based on plan_amount
if ($plan_amount == 99) {
    $no_of_devices = 1;
} else {
    $no_of_devices = 2;
}

// Prepare the SQL query
$sql = "UPDATE users SET 
    updated_by = ?, 
    purchase = ?, 
    allocated_ip = ?, 
    plan_amount = ?, 
    plan_valid_for = ?, 
    plan_expiry = ?, 
    no_of_devices = ?,
    jellyfin_status = ?
    WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssdsisi", $updated_by, $purchase, $allocated_ip, $plan_amount, $plan_valid_for, $plan_expiry, $no_of_devices, $jellyfin_status, $user_id);

// Execute the query
if ($stmt->execute()) {

    echo "updated successfully ";

    // Send email notification to the user
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'scoobytv49@gmail.com'; // Your Gmail address
        $mail->Password = 'pnrejfrmudnytlss'; // Your Gmail password or App password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('scoobytv49@gmail.com', 'ScoobyTV');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Plan Status';
        if ($purchase == "Successfull & Verified") {
            $mail->Body = "Dear User,<br><br>Your plan has been activated successfully.<br><br>Host/Server Ip: stv1.xyz:53570 <br>Username: $username <br> Password: [Same that you had set] <br><br>  Mobile or TV Application download links:- scoobytv.com/apps <br><br> To see more plan details, please visit ScoobyTv -> Profile.<br><br>Regards,<br>The ScoobyTV Team";
        } else {
            $mail->Body = "Dear User,<br><br>Your plan activation failed. Reason: $purchase.<br><br>To see the details, please visit ScoobyTv -> Profile.<br><br>Regards,<br>The ScoobyTV Team";
        }

        $mail->send();
        echo ' and Email has been sent';
        header("Refresh: 1; url=BabuaPanel.php"); 
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

} else {
    echo "Error updating record: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();

