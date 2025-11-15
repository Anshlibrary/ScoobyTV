<?php
function createUser($serverUrl, $apiKey, $username, $password) {
    $url = $serverUrl . 'Users/New';
    $data = json_encode([
        'Name' => $username,
        'Password' => $password
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-Emby-Token: ' . $apiKey
    ]);
 curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Set timeout to 30 seconds
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
        'IsAdministrator' => true,
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
 curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Set timeout to 30 seconds
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

// Accept GET parameters
// $serverUrl = 'http://stv1.xyz:53570/'; // Replace with your server URL
// $apiKey = '77dd7c600da04348b24a24e891b3c7c9'; // Replace with your API key
$serverUrl = 'http://127.0.0.1:8096/'; // Replace with your server URL
$apiKey = 'd1bd3d0a5bd24f2c85a51aecfcda8179'; // Replace with your API key
// $username = isset($_GET['jellyname']) ? htmlspecialchars($_GET['jellyname']) : '';
// $password = isset($_GET['jellypass']) ? htmlspecialchars($_GET['jellypass']) : '';
$username ='jelly';
$password = 'jellypass';

if ($username && $password) {
    $userResponse = createUser($serverUrl, $apiKey, $username, $password);
    $userId = isset($userResponse['Id']) ? $userResponse['Id'] : null;

    if ($userId) {
        echo "User created with ID: $userId\n";
        $permissionsResponse = setUserPermissions($serverUrl, $apiKey, $userId);
        echo "Permissions set for user ID: $userId\n";
    } else {
        echo "Failed to create user\n";
        var_dump($userResponse); // Output the response for debugging
    }
} else {
    echo "Username and password are required.\n";
}
