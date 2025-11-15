<?php

/* Jellyfin create user */
function createUser($serverUrl, $apiKey, $jellyuser, $jellypass) {
    $url = $serverUrl . 'Users/New';
    $data = json_encode([
        'Name' => $jellyuser,
        'Password' => $jellypass
    ]);

    $options = [
        'http' => [
            'header'  => "Content-Type: application/json\r\nX-Emby-Token: $apiKey\r\n",
            'method'  => 'POST',
            'content' => $data,
        ]
    ];
    $context  = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $httpCode = $http_response_header[0]; // Get HTTP response code

    if (strpos($httpCode, "200") === false) {
        echo "HTTP Code (createUser): $httpCode\nResponse: $response\n";
    }

    return json_decode($response, true);
}

/* Set user permissions */
function setUserPermissions($serverUrl, $apiKey, $userId) {
    $url = $serverUrl . "Users/$userId/Policy";
    $data = json_encode([
        'IsAdministrator' => false,
        'IsHidden' => false,
        'IsDisabled' => false,
        'EnableRemoteAccess' => true,
        'EnablePublicSharing' => true,
        'EnableAudioPlaybackTranscoding' => true,
        'EnableVideoPlaybackTranscoding' => true
    ]);

    $options = [
        'http' => [
            'header'  => "Content-Type: application/json\r\nX-Emby-Token: $apiKey\r\n",
            'method'  => 'POST',
            'content' => $data,
        ]
    ];
    $context  = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    $httpCode = $http_response_header[0]; // Get HTTP response code

    if (strpos($httpCode, "200") === false && strpos($httpCode, "204") === false) {
        echo "HTTP Code (setUserPermissions): $httpCode\nResponse: $response\n";
    }

    return json_decode($response, true);
}

/* Disable subtitles by default */
function setUserConfiguration($serverUrl, $apiKey, $userId) {
    $url = $serverUrl . "Users/$userId/Configuration";
    $data = json_encode([
        'SubtitleMode' => 'None'
    ]);

    $options = [
        'http' => [
            'header'  => "Content-Type: application/json\r\nX-Emby-Token: $apiKey\r\n",
            'method'  => 'POST',
            'content' => $data,
        ]
    ];
    $context  = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $httpCode = $http_response_header[0]; // Get HTTP response code

    if (strpos($httpCode, "200") === false && strpos($httpCode, "204") === false) {
        echo "HTTP Code (setUserConfiguration): $httpCode\nResponse: $response\n";
    }

    return json_decode($response, true);
}

/* Login user */
function loginUser($serverUrl, $jellyuser, $jellypass) {
    $url = $serverUrl . 'Users/AuthenticateByName';
    $data = json_encode([
        'Username' => $jellyuser,
        'Pw' => $jellypass // Corrected 'Password' to 'Pw' per API docs
    ]);

    $options = [
        'http' => [
            'header'  => "Content-Type: application/json\r\n",
            'method'  => 'POST',
            'content' => $data,
        ]
    ];
    $context  = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    $httpCode = $http_response_header[0]; // Get HTTP response code

    if (strpos($httpCode, "200") === false) {
        echo "HTTP Code (loginUser): $httpCode\nResponse: $response\n";
        if (isset($response)) {
            $responseJson = json_decode($response, true);
            if (isset($responseJson['ErrorMessage'])) {
                echo "Error Message: " . $responseJson['ErrorMessage'] . "\n";
            }
        }
    } else {
        echo "Login Successful! Response: $response\n";
    }

    return json_decode($response, true);
}

$serverUrl = 'http://stv1.xyz:53570/'; // Replace with your server URL
$apiKey = '6ca9deb34c874712bbc8ea219dcec6e2'; // Replace with your API key
$jellyuser = "HoJaLogin3";
$jellypass = "HoGyaHu";
$jellyfin_status = "";

// Create the user
$userResponse = createUser($serverUrl, $apiKey, $jellyuser, $jellypass);
$userId = $userResponse['Id'];

if (isset($userId)) {
    $jellyfin_status = "active - $userId"; 
    // Set user permissions
    $permissionsResponse = setUserPermissions($serverUrl, $apiKey, $userId);
    // Disable subtitles by default
    $configResponse = setUserConfiguration($serverUrl, $apiKey, $userId);
    // Automatically log in the user
    $loginResponse = loginUser($serverUrl, $jellyuser, $jellypass);

    // If login is successful, store the AccessToken in a session or cookie
    if (isset($loginResponse['AccessToken'])) {
        session_start();
        $_SESSION['jellyfin_token'] = $loginResponse['AccessToken'];  // Store token in session
        $_SESSION['jellyfin_user_id'] = $userId;  // Store user ID in session
        echo "User created and logged in successfully!";
    } else {
        echo "Login failed, no AccessToken found.";
    }
} else {
    $jellyfin_status = "Failed to create user"; 
    echo "Failed to create user.";
}

?>
