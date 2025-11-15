<?php

function createUser($serverUrl, $apiKey, $username, $password) {
    $url = $serverUrl . 'Users/New';
    $data = json_encode([
        'Name' => $username,
        'Password' => $password
    ]);

    $options = [
        'http' => [
            'header' => [
                "Content-Type: application/json",
                "X-Emby-Token: " . $apiKey,
            ],
            'method' => 'POST',
            'content' => $data,
            'timeout' => 30 // Set timeout to 30 seconds
        ]
    ];

    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    $httpCode = $response === false ? 0 : 200; // Use 200 as a default for successful response

    if ($httpCode !== 200) {
        $error = error_get_last();
        echo "HTTP Code: $httpCode\nResponse: " . ($response ?: $error['message']) . "\n";
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

    $options = [
        'http' => [
            'header' => [
                "Content-Type: application/json",
                "X-Emby-Token: " . $apiKey,
            ],
            'method' => 'POST',
            'content' => $data,
            'timeout' => 30 // Set timeout to 30 seconds
        ]
    ];

    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    $httpCode = $response === false ? 0 : 200; // Use 200 as a default for successful response

    if ($httpCode !== 200) {
        $error = error_get_last();
        echo "HTTP Code: $httpCode\nResponse: " . ($response ?: $error['message']) . "\n";
    }

    return json_decode($response, true);
}

// Accept GET parameters
$serverUrl = 'http://stv1.xyz:53570/'; // Replace with your server URL
$apiKey = '6ca9deb34c874712bbc8ea219dcec6e2'; // Replace with your API key
$username = 'jelly';
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
?>